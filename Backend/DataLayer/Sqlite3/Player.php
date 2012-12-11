<?php
namespace SMGregsList\Backend\DataLayer\Sqlite3;
use SMGregsList\Player as p, SMGregsList\DataPlayer;
class Player extends p implements DataPlayer
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
        $this->id = $player->getId();
        $this->age = $player->getAge();
        $this->average = $player->getAverage();
        $this->experience = $player->getExperience();
        $this->forecast = $player->getForecast();
        $this->position = $player->getPosition();
        $this->progression = $player->getProgression();
        $this->forecast = $player->getForecast();
        $this->skills = clone $player->getSkills();
        $this->stats = clone $player->getStats();
    }

    function fromResult(array $result)
    {
        foreach ($result as $name => $value) {
            $this->$name = $value;
        }
        $this->fillSkills();
        $this->fillStats();
    }

    function fillSkills()
    {
        $data = $this->db->query("SELECT * FROM skills WHERE id='" . $this->db->escapeString($this->id) . "'");
        while ($row = $data->fetchArray(SQLITE3_ASSOC)) {
            $this->skills->{$row['name']}= $row['value'];
        }
        $data->finalize();
    }

    function fillStats()
    {
        $data = $this->db->query("SELECT * FROM stats WHERE id='" . $this->db->escapeString($this->id) . "'");
        while ($row = $data->fetchArray(SQLITE3_ASSOC)) {
            $this->stats->{$row['name']} = $row['value'];
        }
        $data->finalize();
    }

    function exists()
    {
        return $this->db->querySingle("SELECT id FROM player WHERE id ='" . $this->db->escapeString($this->getId()) . "'");
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