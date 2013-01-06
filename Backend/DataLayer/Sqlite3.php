<?php
namespace SMGregsList\Backend\DataLayer;
use SMGregsList\WriteablePlayer, SMGregsList\SearchablePlayer, SMGregsList\Player, SMGregsList\Backend\DataLayer;
class Sqlite3 extends DataLayer
{
    public static $DATABASEPATH = '';
    protected $db;
    function __construct($database = null)
    {
        if (!$database) {
            $database = self::$DATABASEPATH;
        }
        $this->db = new \SQLite3($database);
        $this->db->enableExceptions(true);
    }

    function getManagerSchema()
    {
        return 'CREATE TABLE manager (
    name TEXT NOT NULL PRIMARY KEY,
    code TEXT NOT NULL
        )';
    }

    function getPlayerSchema()
    {
        return 'CREATE TABLE player (
  id TEXT NOT NULL PRIMARY KEY,
  average REAL NOT NULL,
  age INT NOT NULL,
  position TEXT NOT NULL,
  experience REAL NOT NULL,
  forecast INT default 0,
  progression INT default 0,
  country TEXT,
  manager TEXT,
  name TEXT,
  lastmodified DATE NOT NULL default CURRENT_TIMESTAMP,
  createstamp DATE NOT NULL default CURRENT_TIMESTAMP);
CREATE TABLE skills (id NOT NULL, name NOT NULL, value NOT NULL, PRIMARY KEY (id, name));
CREATE TABLE stats (id NOT NULL, name NOT NULL, value NOT NULL, PRIMARY KEY (id, name));';
    }

    function getSearchSchema()
    {
        return 'CREATE TABLE playersearch (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  manager TEXT NOT NULL,
  createstamp DATE NOT NULL default CURRENT_TIMESTAMP,
  lastaccess DATE NOT NULL default CURRENT_TIMESTAMP
  minaverage INT,
  maxaverage INT
  minage INT,
  maxage INT,
  country TEXT,
  sellingmanager TEXT,
  experience REAL,
  forecast INT,
  progression INT,
  GK NUMBER NOT NULL default 0,
  LB NUMBER NOT NULL default 0,
  LDF NUMBER NOT NULL default 0,
  CDF NUMBER NOT NULL default 0,
  RDF NUMBER NOT NULL default 0,
  RB NUMBER NOT NULL default 0,
  DFM NUMBER NOT NULL default 0,
  LM NUMBER NOT NULL default 0,
  LIM NUMBER NOT NULL default 0,
  IM NUMBER NOT NULL default 0,
  RIM NUMBER NOT NULL default 0,
  RM NUMBER NOT NULL default 0,
  OM NUMBER NOT NULL default 0,
  LW NUMBER NOT NULL default 0,
  LF NUMBER NOT NULL default 0,
  CF NUMBER NOT NULL default 0,
  RF NUMBER NOT NULL default 0,
  RW NUMBER NOT NULL default 0
  );
CREATE TABLE skills (id NOT NULL, name NOT NULL, value NOT NULL, PRIMARY KEY (id, name));
CREATE TABLE stats (id NOT NULL, name NOT NULL, value NOT NULL, PRIMARY KEY (id, name));';
    }

    function exists(Player $player)
    {
        return $this->db->querySingle("SELECT id FROM player WHERE id ='" . $this->db->escapeString($player->getId()) . "'");
    }

    function checkManager(\SMGregsList\Player $player, \SMGregsList\Manager $manager)
    {
        if (!$this->exists($player)) {
            return false;
        }
        $new = new Sqlite3\SellPlayer($this->db);
        $new->fromPlayer($player);
        // get the manager and other data
        $player = $new;
        $player->privateRetrieve();
        if ($player->getManager()->getName() == $manager->getName() && $player->getManager()->getCode() == $manager->getCode()) {
            // this player was put up for sale, and then sold.  we must remove it
            $this->remove($player);
        }
        return $this->exists($player);
    }

    function remove(Player $player)
    {
        $new = new Sqlite3\SellPlayer($this->db);
        $new->fromPlayer($player);
        return $new->remove();
    }

    function save(WriteablePlayer $player)
    {
        $new = new Sqlite3\SellPlayer($this->db);
        $new->fromPlayer($player);
        return $new->save();
    }

    function retrieve(Player $player, $private = false)
    {
        $new = new Sqlite3\Player($this->db);
        $new->fromPlayer($player);
        if ($private) {
            return $new->privateRetrieve();
        }
        return $new->retrieve();
    }

    function removeOldListings(\SMGregsList\Manager $manager, \SMGregsList\Player $player)
    {
        if ($this->exists($player)) {
            $player = $this->retrieve($player);
            if ($player->getManager()->getName() != $manager->getName()) {
                $player->code = $player->getManager()->getCode(); // this allows us to remove the player
                $this->remove($player);
            }
        }
    }

    function retrieveManagerFromName($name)
    {
        $new = new Sqlite3\Manager($this->db);
        $new->name = $name;
        return $new->retrieve();
    }

    function retrieveManager(Player $player)
    {
        $new = new Sqlite3\Manager($this->db);
        $new->fromPlayer($player);
        return $new->retrieve();
    }

    function search(SearchablePlayer $player)
    {
        $new = new Sqlite3\SearchPlayer($this->db);
        $new->fromPlayer($player);
        return $new->search();
    }

    function savesearch(SearchablePlayer $player)
    {
        $new = new Sqlite3\SearchPlayer($this->db);
        $new->fromPlayer($player);
        return $new->savesearch();
    }
}