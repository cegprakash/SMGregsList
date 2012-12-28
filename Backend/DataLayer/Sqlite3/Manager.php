<?php
namespace SMGregsList\Backend\DataLayer\Sqlite3;
use SMGregsList\Manager as m, SMGregsList\Player;
class Manager extends m
{
    protected $db;
    function __construct(\Sqlite3 $database)
    {
        $this->name = '';
        $this->db = $database;
    }

    function fromPlayer(Player $player)
    {
        $this->name = $player->getManager();
        if ($this->name instanceof $this) {
            $this->name = $this->name->getName();
        }
    }

    function exists()
    {
        return $this->db->querySingle("SELECT name FROM manager WHERE name ='" . $this->db->escapeString($this->getName()) . "'");
    }

    function retrieve()
    {
        if (!$this->exists()) {
            // create on the fly
            $this->generateCode();
            $this->save();
            return $this;
        }
        $data = $this->db->query("SELECT * FROM manager WHERE name='" . $this->db->escapeString($this->name) . "'");
        $row = $data->fetchArray(SQLITE3_ASSOC);
        $data->finalize();
        foreach ($row as $name => $value) {
            $this->$name = $value;
        }
        return $this;
    }

    function save()
    {
        //$this->showme();
        if (!$this->name) {
            throw new \Exception('Manager name must be set, please try again');
        }
        $db = $this->db;
        $t = function ($a) use ($db) {
            return $db->escapeString($a);
        };
        $this->db->exec("INSERT INTO manager (name, code)
                        VALUES (
                            '" . $t($this->getName()) . "',
                            '" . $t($this->getCode()) . "'
                            )");
        return $this;
    }
}