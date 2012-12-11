<?php
namespace SMGregsList\Backend\DataLayer\Sqlite3;
use SMGregsList\SearchablePlayer, SMGregsList\SearchPlayer as s;
class SearchPlayer extends s implements SearchablePlayer
{
    protected $db;
    function __construct(\Sqlite3 $database)
    {
        $this->db = $database;
        parent::__construct();
    }

    function fromPlayer(s $player)
    {
        $this->age = $player->getAge();
        $this->average = $player->getAverage();
        $this->experience = $player->getExperience();
        $this->forecast = $player->getForecast();
        $this->position = $player->getPosition();
        $this->progression = $player->getProgression();
        $this->skills = clone $player->getSkills();
        $this->stats = clone $player->getStats();
    }

    function search()
    {
        $components = $this->getSearchComponents();
        $playersql = '';
        $statssql = '';
        $skillssql = '';
        $db = $this->db;
        $t = function($a, $quote = true) use ($db) {
            $r = $db->escapeString($a);
            return "'" . $r ."'";
        };
        foreach ($components as $component => $value) {
            switch ($component) {
                case 'minaverage' :
                    $playersql .= " AND average >= " . $t($value);
                    break;
                case 'maxaverage' :
                    $playersql .= " AND average <= " . $t($value);
                    break;
                case 'minage':
                    $playersql .= " AND age >= " . $t($value);
                    break;
                case 'maxage':
                    $playersql .= " AND age <= " . $t($value);
                    break;
                case 'positions':
                    foreach ($value as $i => $val) {
                        $value[$i] = "'" . $db->escapeString($val) . "'";
                    }
                    $playersql .= " AND position IN (" . implode(',', $value) . ")";
                    break;
                case 'experience':
                    $playersql .= " AND experience >= " . $t($value);
                    break;
                case 'forecast':
                    $playersql .= " AND forecast >= " . $t($value);
                    break;
                case 'progression':
                    $playersql .= " AND progression >= " . $t($value);
                    break;
                case 'skills':
                    $skillssql = ', skills s';
                    $playersql .= ' AND s.id=player.id';
                    foreach ($value as $skill => $minvalue) {
                         $playersql .= " AND (s.name='" . $skill . "' AND s.value >= '" . $minvalue . "')";
                    }
                    break;
                case 'stats':
                    $statssql = ', stats a';
                    $playersql .= ' AND a.id=player.id';
                    foreach ($value as $stat => $minvalue) {
                         $playersql .= " AND (a.name='" . $stat . "' AND a.value >= '" . $minvalue . "')";
                    }
                    break;
            }
        }
        $ret = array();
        $sql = 'SELECT DISTINCT player.* FROM player' . $skillssql . $statssql . ' WHERE 1=1' . $playersql;
        $data = $db->query($sql);
        while ($row = $data->fetchArray(SQLITE3_ASSOC)) {
            $player = new Sqlite3\Player($this);
            $player->fromResult($row);
            $ret[] = $player;
        }
        return $ret;
    }
}