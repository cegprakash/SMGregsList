<?php
namespace SMGregsList;
class Stats implements \Iterator, \Countable
{
    const TYPE = 'stat';
    protected $stats = array(
        'Pass' => null,
        'Shot' => null,
        'Dribbling' => null,
        'Speed' => null,
        'Ball Steal' => null,
        'Saves' => null,
        'Strength' => null,
        'Technique' => null,
        'Aggressiveness' => null,
        'Leadership' => null,
        'Versatility' => null);
    protected $valid = array(
        'Pass',
        'Shot',
        'Dribbling',
        'Speed',
        'Ball Steal',
        'Saves',
        'Strength',
        'Technique',
        'Aggressiveness',
        'Leadership',
        'Versatility',
    );

    function __get($name)
    {
        if (!in_array($name, $this->valid)) {
            throw new \Exception('Invalid ' . static::TYPE . ': ' . $name);
        }
        return $this->stats[$name];
    }

    function validName($name, $value)
    {
        if (!$value) return true;
        if (!is_numeric($value) || $value < 0 || $value >= 100) {
            return false;
        }
        return true;
    }

    function __set($name, $value)
    {
        if (!in_array($name, $this->valid)) {
            throw new \Exception('Invalid ' . static::TYPE . ': ' . $name);
        }
        if (!$this->validName($name, $value)) {
            throw new \Exception('Invalid ' . $name . ': ' . $value);
        }
        if (!$value) {
            $this->stats[$name] = null;
            return;
        }
        $this->stats[$name] = $value;
    }

    function count()
    {
        return count($this->stats);
    }

    function rewind()
    {
        reset($this->stats);
    }
  
    function current()
    {
        return current($this->stats);
    }
  
    function key() 
    {
        return key($this->stats);
    }
  
    function next() 
    {
        return next($this->stats);
    }
  
    function valid()
    {
        $key = key($this->stats);
        return $key !== NULL && $key !== FALSE;
    }
}