<?php
namespace SMGregsList\Backend\DataLayer\Sqlite3;
use SMGregsList\Player as p, SMGregsList\WriteablePlayer;
class Player extends p implements WriteablePlayer
{
    protected $db;
    function __construct(\Sqlite3 $database)
    {
        $this->id = 0;
        $this->db = $database;
        parent::__construct();
    }

    function fromPlayer(p $player)
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

    function fromResult(array $result)
    {
        foreach ($result as $name => $value) {
            $this->$name = $value;
        }
        $this->db->fillSkills($this);
        $this->db->fillStats($this);
    }

    function exists()
    {
        return $this->db->querySingle("SELECT id FROM player WHERE id ='" . $this->db->escapeString($this->getId()) . "'");
    }

    function remove()
    {
        if (!$this->id) {
            throw new \Exception('Internal error: player is uninitialized, cannot delete it');
        }
        $this->db->exec('BEGIN');
        try {
            $this->db->exec("DELETE FROM player WHERE id='" . $this->db->escapeString($this->id) . "'");
            $this->db->exec("DELETE FROM skills WHERE id='" . $this->db->escapeString($this->id) . "'");
            $this->db->exec("DELETE FROM stats WHERE id='" . $this->db->escapeString($this->id) . "'");
            $this->db->exec('COMMIT');
        } catch (\Exception $e) {
            $this->db->exec('ROLLBACK');
        }
    }

    function save()
    {
        $this->db->exec('BEGIN');
        try {
            $db = $this->db;
            $t = function ($a) use ($db) {
                return $db->escapeString($a);
            };
            if ($this->exists()) {
                $this->db->exec("UPDATE player SET
                                age = '" . $t($this->getAge()) . "',
                                average = '" . $t($this->getAverage()) . "',
                                experience = '" . $t($this->getExperience()) . "',
                                forecast = '" . $t($this->getForecast()) . "',
                                position = '" . $t($this->getPosition()) . "',
                                progression = '" . $t($this->getProgression()) . "
                            WHERE id = '" . $t($this->getId()) . "'");
            } else {
                $this->db->exec("INSERT INTO player (id, age, average, experience, forecast, position, progression)
                                VALUES (
                                    '" . $t($this->getId()) . "'
                                    '" . $t($this->getAge()) . "',
                                    '" . $t($this->getAverage()) . "',
                                    '" . $t($this->getExperience()) . "',
                                    '" . $t($this->getForecast()) . "',
                                    '" . $t($this->getPosition()) . "',
                                    '" . $t($this->getProgression()) . "')");
            }
            $this->db->exec("DELETE FROM skills WHERE id='" . $t($this->getId()) . "'");
            $this->db->exec("DELETE FROM stats WHERE id='" . $t($this->getId()) . "'");
            foreach ($this->getSkills() as $skill => $value) {
                $this->db->exec("INSERT INTO skills (id, name, value) VALUES (
                    '" . $t($this->getId()) . "',
                    '" . $skill . "',
                    '" . $value . "'
                )");
            }
            foreach ($this->getStats() as $stat => $value) {
                $this->db->exec("INSERT INTO stats (id, name, value) VALUES (
                    '" . $t($this->getId()) . "',
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

    function retrieve()
    {
        if (!$this->exists()) {
            throw new \Exception("Error: player does not exist");
        }
        $data = $this->db->query("SELECT * FROM player WHERE id='" . $this->db->escapeString($this->id) . "'");
        $row = $data->fetchArray(SQLITE3_ASSOC);
        $data->finalize();
        foreach ($row as $name => $value) {
            $this->$name = $value;
        }
        $data = $this->db->query("SELECT * FROM skills WHERE id='" . $this->db->escapeString($this->id) . "'");
        $row = $data->fetchArray(SQLITE3_ASSOC);
        $data->finalize();
        foreach ($row as $name => $value) {
            $this->skills->$name = $value;
        }
        $data = $this->db->query("SELECT * FROM stats WHERE id='" . $this->db->escapeString($this->id) . "'");
        $row = $data->fetchArray(SQLITE3_ASSOC);
        $data->finalize();
        foreach ($row as $name => $value) {
            $this->stats->$name = $value;
        }
    }
}