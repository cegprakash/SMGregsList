<?php
namespace SMGregsList\Backend\DataLayer\SMGrabber;
use SMGregsList\Player as p, SMGregsList\DataPlayer, SMGregsList\Backend\DataLayer\SMGrabber, SMGregsList\Manager;
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

    function download()
    {
        if (!$this->downloaded) {
            $this->rawdata = $this->downloader->download('http://en.strikermanager.com/jugador.php?id_jugador=' . $this->id,
                                                         $result);
            if ($result == 404) {
                $this->exists = false;
            }
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
            'Versatility' => 1,
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
        preg_match('/<td>Total average</td>\s+<td class="numerico">\s+(\d+)<span style="font-size: 0.7em;">[\.,](\d+)</',
                   $this->rawdata, $matches);
        $this->average = $matches[1] . '.' . $matches[2];
    }
}