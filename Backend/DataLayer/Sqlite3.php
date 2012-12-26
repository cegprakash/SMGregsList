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
  createstamp DATE NOT NULL default CURRENT_TIMESTAMP,
  minaverage INT,
  maxaverage INT
  minage INT,
  maxage INT,
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
  RW NUMBER NOT NULL default 0,
  lastmodified DATE NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (email, lastmodified));
CREATE TABLE skills (id NOT NULL, name NOT NULL, value NOT NULL, PRIMARY KEY (id, name));
CREATE TABLE stats (id NOT NULL, name NOT NULL, value NOT NULL, PRIMARY KEY (id, name));';
    }

    function exists(Player $player)
    {
        if ($player->code) {
            // this only happens if the player was sold, but somehow was not removed from being for sale
            // in which case we have to see if the code matches and remove the player
            try {
                $this->remove($player);
            } catch (\Exception $e) {
                // fail silently if the code does not match
            }
        }
        return $this->db->querySingle("SELECT id FROM player WHERE id ='" . $this->db->escapeString($player->getId()) . "'");
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

    function retrieve(Player $player)
    {
        $new = new Sqlite3\Player($this->db);
        $new->fromPlayer($player);
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
}