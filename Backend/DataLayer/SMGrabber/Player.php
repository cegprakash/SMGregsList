<?php
namespace SMGregsList\Backend\DataLayer\SMGrabber;
use SMGregsList\Player as p, SMGregsList\DataPlayer, SMGregsList\SMGrabber, SMGregsList\Manager;
class Player extends p implements DataPlayer
{
    protected $downloader;
    protected $downloaded = false;
    protected $rawdata;
    protected $exists = false;
    protected $onauction = false;
    protected $isyouth = false;
    function __construct(SMGrabber $downloader)
    {
        $this->id = 0;
        $this->downloader = $downloader;
        parent::__construct();
    }

    function fromPlayer(p $player)
    {
        $this->id = $player->getId();
        $this->code = $player->getCode();
        $this->age = $player->getAge();
        $this->average = $player->getAverage();
        $this->experience = $player->getExperience();
        $this->forecast = $player->getForecast();
        $this->position = $player->getPosition();
        $this->progression = $player->getProgression();
        $this->forecast = $player->getForecast();
        $this->skills = clone $player->getSkills();
        $this->stats = clone $player->getStats();
        $this->retrieved = $player->getRetrieved();
        $this->country = $player->getCountry();
        $this->name = $player->getName();
        $this->manager = $player->getManager();
    }

    function exists()
    {
        $this->download();
        return $this->exists;
    }

    function retrieve()
    {
        $this->download();
        if (!$this->exists) {
            throw new \Exception('Unknown user id: ' . $this->id);
        }
        return $this;
    }

    /**
     * Populate $_POST based on the retrieved values
     */
    function stackPOST()
    {
        $p = $_POST;
        $p['id'] = $this->id;
        $p['name'] = $this->name;
        $p['age'] = $this->age;
        $p['average'] = $this->average;
        $p['experience'] = $this->experience;
        $p['position'] = $this->position;
        $p['stats'] = array();
        $p['skills'] = array();
        foreach ($this->stats as $name => $value) {
            $p['stats'][$name] = $value;
        }
        foreach ($this->skills as $name => $value) {
            $p['skills'][$name] = $value;
        }
        return $p;
    }

    function download()
    {
        if (!$this->downloaded) {
            $this->rawdata = $this->downloader->download('http://en.strikermanager.com/jugador.php?id_jugador=' . $this->id,
                                                         $result);
            if ($result == 404) {
                $this->exists = false;
            }
        } else {
            return;
        }
        if (!preg_match('/<a href="equipo\.php\?id=(\d+)/', $this->rawdata, $matches)) {
            $this->exists = false;
            return;
        }
        $this->exists = true;
        $team = $matches[1];
        $data = $this->downloader->download('http://en.strikermanager.com/equipo.php?id=' . $matches[1]);
        if (!preg_match('/usuario\.php\?id=(\d+)">([^<]+)</', $data, $matches)) {
            
        }
        $this->manager = new Manager;
        $this->manager->name = $matches[2];
        if (preg_match('@/img/as/lock@', $this->rawdata)) {
          $this->onauction = true;
        }
        preg_match('/<img class="bandera" src="\/img\/paises\/[^\.]+.gif">\s+(.+)\s+<span/', $this->rawdata, $matches);
        $this->name = $matches[1];
        preg_match('/<td>Country<\/td>\s+<td>([^<]+)</', $this->rawdata, $matches);
        $this->country = $matches[1];
        preg_match('/<td>Position<\/td>\s+<td>([^<]+)<\/td>/', $this->rawdata, $matches);
        $pos = $matches[1];
        $this->isyouth = preg_match('/\(Youth\)/', $this->rawdata);
        preg_match_all('/<td>([a-zA-Z ]+)<\/td>\s+<td>\s+<span style="display: none;">(\d\d\d)' .
                   '<\/span>\s+<div class="jugbarra" style="width: 99px">\s+<div class="jugbarracar" ' .
                   'style="border: 1px outset #[a-f0-9]+; width: \d+px; background: #[a-f0-9]+;"><\/div>' .
                   '\s+<div class="jugbarranum">\d+%/', $this->rawdata, $stats);
        preg_match_all('/<td>([A-Za-z]+ (?:points|average))<\/td>\s+<td class="numerico">\s+(\d+)' .
                       '<span style="font-size: 0.7em;">[\.,](\d+)<\/span>/', $this->rawdata, $summaries);
        $positions = array(
            'Goalkeeper' => 'GK',
            'Left Back' => 'LB',
            'Left Def.' => 'LDF',
            'Cent. Def.' => 'CDF',
            'Right Def.' => 'RDF',
            'Right Back' => 'RB',
            'Left Mid.' => 'LM',
            'Left Inn. Mid.' => 'LIM',
            'Inn. Mid' => 'IM',
            'Right Inn. Mid.' => 'RIM',
            'Right Mid.' => 'RM',
            'Left Wing.' => 'LW',
            'Left Forw.' => 'LF',
            'Cent. Forw.' => 'CF',
            'Right Forw.' => 'RF',
            'Right Wing.' => 'RW',
            'Offve. Mid.' => 'OM',
            'Def. Mid.' => 'DFM'
        );
        $this->position = $positions[$pos[1]];
        $this->stats = array();
        $contArr = array(
            'Morale' => 1,
            'Stamina' => 1,
            'Fitness' => 1
        );
        $value = function($a) {
            $b = $a[2];
            if ($a[1]) {
                $b = $a[1] . $b;
            }
            if ($a[0]) {
                $b = $a[0] . $b;
            }
            return $b + 0;
        };
        foreach ($stats[0] as $i => $unused) {
            if (isset($contArr[$stats[1][$i]])) continue;
            $this->stats[$stats[1][$i]] = $value($stats[2][$i]);
        }
        preg_match('/<td>Experience<\/td>\s+<td>([0-9\.,]+)/', $this->rawdata, $matches);
        $this->experience = str_replace(',', '.', $matches[1]);
        preg_match('/<td>([0-9]+) years/', $this->rawdata, $matches);
        $this->age = $matches[1];
        preg_match('@<td>Total average</td>\s+<td class="numerico">\s+(\d+)<span style="font-size: 0.7em;">[\.,](\d+)<@',
                   $this->rawdata, $matches);
        $this->average = $matches[1] . '.' . $matches[2];
        $skillshtml = $this->downloader->download('http://en.strikermanager.com/powerups.php?id_jugador=' . $this->id);
        preg_match_all('@<img style="width: 68px;" src="/img/powerups/logo_([^\.]+).jpg" /></td>\s+' .
				'<td style="padding: 0; padding-left: 5px;">\s+' .
					'<div style="width: 90px;">\s+' .
		    '<div style="font-size: 9px; line-height: 10px; font-weight: normal; height: 20px; overflow: hidden;">[^<]+</div>\s+' .
		    '<div class="balones"><img src="/img/new/sport_soccer.png" title="(\d+)%@',
                    $skillshtml, $skills);
        $skillmap = array(
            'TIRO_PENALTIS' => 'Penalty expert',
            'CAMBIO_DE_RITMO' => 'Change of pace',
            'CORRER_CON_BALON' => 'Running with the ball',
            'PULMON_DE_ACERO' => 'Steel lung',
            'ROMPE_REDES' => 'Net-breaker',
            'PANTERA' => 'Panther save',
            'JUEGO_AEREO' => 'Aerial play',
            'ENTRADA_DESLIZANTE' => 'Sliding tackle',
            'PASE_PRECISO' => 'Precise Pass',
            'AS_DEL_ESLALOM' => 'Slalom Ace',
            'MEDIATICO' => 'Celebrity',
            'MURO_DEFENSIVO' => 'Defensive wall'
        );
        foreach ($skills[0] as $i => $unused) {
            $this->skills->{$skillmap[$skills[1][$i]]} = $skills[2][$i]/20;
        }
    }
}