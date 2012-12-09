<?php
namespace SMGregsList\DataLayer\Sqlite3;
use SMGregsList\Player as p, SMGregsList\WriteablePlayer, SMGregsList\DataLayer\Sqlite3;
class Player extends p implements WriteablePlayer
{
    protected $db;
    function __construct(Sqlite3 $database)
    {
        $this->id = 0;
        $this->db = $database;
        parent::__construct();
    }

    function fromResult(array $result)
    {
        foreach ($result as $name => $value) {
            $this->name = $value;
        }
        $this->db->fillSkills($this);
        $this->db->fillStats($this);
    }
}