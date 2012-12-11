<?php
namespace SMGregsList\Backend\DataLayer;
use SMGregsList\WriteablePlayer, SMGregsList\SearchablePlayer, SMGregsList\Player, SMGregsList\Backend\DataLayer;
class Sqlite3 extends DataLayer
{
    protected $db;
    function __construct()
    {
        $this->db = new \SQLite3(__DIR__ . '/../../gregslist.db');
        $this->db->enableExceptions(true);
    }

    function exists(Player $player)
    {
        return $this->db->querySingle("SELECT id FROM player WHERE id ='" . $this->db->escapeString($player->getId()) . "'");
    }

    function remove(Player $player)
    {
        if (!$player->id) {
            throw new \Exception('Internal error: player is uninitialized, cannot delete it');
        }
        $this->db->exec('BEGIN');
        try {
            $this->db->exec("DELETE FROM player WHERE id='" . $this->db->escapeString($player->id) . "'");
            $this->db->exec("DELETE FROM skills WHERE id='" . $this->db->escapeString($player->id) . "'");
            $this->db->exec("DELETE FROM stats WHERE id='" . $this->db->escapeString($player->id) . "'");
            $this->db->exec('COMMIT');
        } catch (\Exception $e) {
            $this->db->exec('ROLLBACK');
        }
    }

    function save(WriteablePlayer $player)
    {
        $this->db->exec('BEGIN');
        try {
            $db = $this->db;
            $t = function ($a) use ($db) {
                return $db->escapeString($a);
            };
            if ($this->exists($player)) {
                $this->db->exec("UPDATE player SET
                                age = '" . $t($player->getAge()) . "',
                                average = '" . $t($player->getAverage()) . "',
                                experience = '" . $t($player->getExperience()) . "',
                                forecast = '" . $t($player->getForecast()) . "',
                                position = '" . $t($player->getPosition()) . "',
                                progression = '" . $t($player->getProgression()) . "
                            WHERE id = '" . $t($player->getId()) . "'");
            } else {
                $this->db->exec("INSERT INTO player (id, age, average, experience, forecast, position, progression)
                                VALUES (
                                    '" . $t($player->getId()) . "'
                                    '" . $t($player->getAge()) . "',
                                    '" . $t($player->getAverage()) . "',
                                    '" . $t($player->getExperience()) . "',
                                    '" . $t($player->getForecast()) . "',
                                    '" . $t($player->getPosition()) . "',
                                    '" . $t($player->getProgression()) . "')");
            }
            $this->db->exec("DELETE FROM skills WHERE id='" . $t($player->getId()) . "'");
            $this->db->exec("DELETE FROM stats WHERE id='" . $t($player->getId()) . "'");
            foreach ($player->getSkills() as $skill => $value) {
                $this->db->exec("INSERT INTO skills (id, name, value) VALUES (
                    '" . $t($player->getId()) . "',
                    '" . $skill . "',
                    '" . $value . "'
                )");
            }
            foreach ($player->getStats() as $stat => $value) {
                $this->db->exec("INSERT INTO stats (id, name, value) VALUES (
                    '" . $t($player->getId()) . "',
                    '" . $stat . "',
                    '" . $value . "'
                )");
            }
            $this->db->exec('COMMIT');
        } catch (\Exception $e) {
            $this->db->exec('ROLLBACK');
            throw $e;
        }
    }

    function retrieve(Player $player)
    {
        if (!$this->exists($player)) {
            throw new \Exception("Error: player does not exist");
        }
        $data = $this->db->query("SELECT * FROM player WHERE id='" . $this->db->escapeString($player->id) . "'");
        $row = $data->fetchArray(SQLITE3_ASSOC);
        $data->finalize();
        foreach ($row as $name => $value) {
            $player->$name = $value;
        }
        $data = $this->db->query("SELECT * FROM skills WHERE id='" . $this->db->escapeString($player->id) . "'");
        $row = $data->fetchArray(SQLITE3_ASSOC);
        $data->finalize();
        foreach ($row as $name => $value) {
            $player->skills->$name = $value;
        }
        $data = $this->db->query("SELECT * FROM stats WHERE id='" . $this->db->escapeString($player->id) . "'");
        $row = $data->fetchArray(SQLITE3_ASSOC);
        $data->finalize();
        foreach ($row as $name => $value) {
            $player->stats->$name = $value;
        }
    }

    function search(SearchablePlayer $player)
    {
        $components = $player->getSearchComponents();
        $playersql = 'SELECT * FROM player WHERE 1=1';
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
                    break;
                case 'stats':
                    break;
            }
        }
        $ret = array();
        $sql = $playersql;
        var_dump($sql);
        $data = $db->query($sql);
        while ($row = $data->fetchArray(SQLITE3_ASSOC)) {
            $player = new Sqlite3\Player($this);
            $player->fromResult($row);
            $ret[] = $player;
        }
        return $ret;
    }

    function fillSkills(Player $player)
    {
        $data = $this->db->query("SELECT * FROM skills WHERE id='" . $this->db->escapeString($player->id) . "'");
        while ($row = $data->fetchArray($SQLITE3_ASSOC)) {
            $player->skills[$row['name']] = $row['value'];
        }
        $data->finalize();
    }

    function fillStats(Player $player)
    {
        $data = $this->db->query("SELECT * FROM stats WHERE id='" . $this->db->escapeString($player->id) . "'");
        while ($row = $data->fetchArray($SQLITE3_ASSOC)) {
            $player->stats[$row['name']] = $row['value'];
        }
        $data->finalize();
    }
}