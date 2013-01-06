<?php
namespace SMGregsList\Backend\DataLayer\Sqlite3;
use SMGregsList\SearchablePlayer, SMGregsList\SearchPlayer as s;
class SearchPlayer extends s implements SearchablePlayer
{
    protected $db;
    protected $savedid;
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
        $this->country = $player->getCountry();
        $this->name = $player->getName();
        $this->manager = $player->getManager();
        $this->skills = clone $player->getSkills();
        $this->stats = clone $player->getStats();
        $this->searchmanager = $player->getSearchManager();
        $this->code = $player->getCode();
    }

    function humanReadableNamefromSavedSearch($id)
    {
        return $this->humanReadableName($this->componentsFromSavedSearch($id));
    }

    function savedSearchCount($id)
    {
        return $this->executeSavedSearch($id, true);
    }

    function executeSavedSearch($id, $countonly = false)
    {
        return $this->search($this->componentsFromSavedSearch($id), $countonly);
    }

    private $componentsCache = null;
    function componentsFromSavedSearch($id)
    {
        if (null !== $this->componentsCache) {
            return $this->componentsCache;
        }
        $data = $this->db->query("SELECT * FROM savedsearch WHERE id='" . $this->db->escapeString($id) . "'");
        if (!$data) {
            throw new \Exception('Unable to retrieve saved search id ' . $id);
        }
        $info = $data->fetchArray(SQLITE3_ASSOC);
        if (!$info) {
            throw new \Exception('Unable to retrieve saved search id ' . $id . ', does not exist');
        }
        $components = array();
        $positions = array();
        foreach ($info as $name => $value) {
            switch ($name) {
                case 'id' :
                case 'createstamp' :
                case 'lastaccess' :
                case 'manager' :
                    break;
                case 'sellingmanager' :
                    if ($value) {
                        $components['manager'] = $value;
                    }
                    break;
                case 'GK' :
                case 'LB' :
                case 'LDF' :
                case 'CDF' :
                case 'RDF' :
                case 'RB' :
                case 'DFM' :
                case 'LM' :
                case 'LIM' :
                case 'IM' :
                case 'RIM' :
                case 'RM' :
                case 'OM' :
                case 'LW' :
                case 'LF' :
                case 'CF' :
                case 'RF' :
                case 'RW' :
                    if ($value) {
                        $positions[] = $name;
                    }
                    break;
                default:
                    if ($value) {
                        $components[$name] = $value;
                    }
            }
        }
        if (count($positions)) {
            $components['positions'] = $positions;
        }

        $data = $this->db->query("SELECT * FROM savedskills WHERE id='" . $this->db->escapeString($id) . "'");
        if (!$data) {
            throw new \Exception('Unable to retrieve saved search skills id ' . $id);
        }
        $skills = array();
        while ($info = $data->fetchArray(SQLITE3_ASSOC)) {
            $skills[$info['name']] = $info['value'];
        }

        $data = $this->db->query("SELECT * FROM savedstats WHERE id='" . $this->db->escapeString($id) . "'");
        if (!$data) {
            throw new \Exception('Unable to retrieve saved search stats id ' . $id);
        }
        $stats = array();
        while ($info = $data->fetchArray(SQLITE3_ASSOC)) {
            $stats[$info['name']] = $info['value'];
        }
        if (count($skills)) {
            $components['skills'] = $skills;
        }
        if (count($stats)) {
            $components['stats'] = $stats;
        }
        $this->componentsCache = $components;
        return $components;
    }

    function savesearch()
    {
        if (!$this->searchmanager || !$this->code) {
            throw new \Exception('Internal error: Cannot save a search without both manager and code');
        }
        $manager = new Manager($this->db);
        $manager->name = $this->searchmanager;
        $manager->retrieve();
        if ($manager->getCode() != $this->code) {
            throw new \Exception("Error: Manager code does not match.  If you have forgotten your code, please send a message " .
                                 "to CelloG inside Striker Manager asking for it");
        }
        $components = $this->getSearchComponents();

        $db = $this->db;
        $t = function($a, $quote = true) use ($db) {
            if (!isset($components[$a])) {
                return "''";
            }
            $r = $db->escapeString($components[$a]);
            if (!$quote) return $r;
            return "'" . $r ."'";
        };
        $playersql = "INSERT INTO savedsearch (manager, minaverage, maxaverage, minage, maxage, country, sellingmanager, experience,
                        forecast, progression)
                     VALUES ('" .
                     $db->escapeString($this->searchmanager) . "',
                     " . $t('minaverage') . ",
                     " . $t('maxaverage') . ",
                     " . $t('minage') . ",
                     " . $t('maxage') . ",
                     " . $t('country') . ",
                     " . $t('manager') . ",
                     " . $t('experience') . ",
                     " . $t('forecast') . ",
                     " . $t('progression') . "
                     );";
        $this->db->exec('BEGIN');
        $this->db->exec($playersql);
        $this->savedid = $this->db->lastInsertRowID();
        if (isset($components['positions'])) {
            foreach ($components['positions'] as $position) {
                $this->db->exec('UPDATE savedsearch SET "' . $db->escapeString($position) . '"=1');
            }
        }
        if (isset($components['skills'])) {
            foreach ($components['skills'] as $skill => $value) {
                $this->db->exec("INSERT INTO savedskills (id, name, value) VALUES ('" . $this->savedid . "',
                                '" . $this->db->escapeString($skill) . "',
                                '" . $this->db->escapeString($value) . "')");
            }
        }
        if (isset($components['stats'])) {
            foreach ($components['stats'] as $stats => $value) {
                $this->db->exec("INSERT INTO savedstats (id, name, value) VALUES ('" . $this->savedid . "',
                                '" . $this->db->escapeString($stats) . "',
                                '" . $this->db->escapeString($value) . "')");
            }
        }
        $this->db->exec('COMMIT');
        return $this;
    }

    function search($components = null, $countonly = false)
    {
        if ($components === null) {
            $components = $this->getSearchComponents();
        }
        $playersql = '';
        $statssql = '';
        $skillssql = '';
        $db = $this->db;
        $t = function($a, $quote = true) use ($db) {
            $r = $db->escapeString($a);
            if (!$quote) return $r;
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
                case 'country':
                    $playersql .= " AND like('%" . $t(strtolower($value), false) . "%', lower(country))";
                    break;
                case 'manager':
                    $playersql .= " AND like('%" . $t(strtolower($value), false) . "%', lower(manager))";
                    break;
                case 'name':
                    $playersql .= " AND like('%" . $t(strtolower($value), false) . "%', lower(player.name))";
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
                    $skillssql = ', skills ss';
                    $playersql .= ' AND ss.id=player.id';
                    foreach ($value as $skill => $minvalue) {
                         $playersql .= " AND (ss.name='" . $skill . "' AND ss.value >= '" . $minvalue . "')";
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
        if ($countonly) {
            $start = 'COUNT (DISTINCT player.id)';
        } else {
            $start = 'DISTINCT player.*';
        }
        $sql = 'SELECT ' . $start . ' FROM player' . $skillssql . $statssql . ' WHERE 1=1' . $playersql;
        $data = $db->query($sql);
        if ($countonly) {
            $info = $data->fetchArray();
            return $info[0];
        }
        while ($row = $data->fetchArray(SQLITE3_ASSOC)) {
            $player = new Player($this->db);
            $player->fromResult($row);
            $ret[] = $player;
        }
        return $ret;
    }
}