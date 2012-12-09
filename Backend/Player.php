<?php
namespace SMGregsList;
abstract class Player
{
    protected
        $average = 0,
        $id,
        $forecast = 0,
        $progression = 0,
        $age = 0,
        $position = false,
        $stats,
        $experience = 0,
        $skills,
        $datasource;

    function __construct()
    {
        $this->skills = new Skills;
        $this->stats = new Stats;
    }

    function __set($name, $value)
    {
        $this->$name = $value;
    }

    function retrieve()
    {
        if (!$this->datasource) {
            throw new \Exception("Internal error: no data source selected");
        }
        return $this->datasource->retrieve($this);
    }

    function save()
    {
        if ($this instanceof WriteablePlayer) {
            return $this->datasource->save($this);
        } else {
            throw new \Exception("Internal error: This is not a writeable player");
        }
    }

    function getAverage()
    {
        return $this->average;
    }

    function getAge()
    {
        return $this->age;
    }

    function getForecast()
    {
        return $this->forecast;
    }

    function getProgression()
    {
        return $this->progression;
    }

    function getPosition()
    {
        return $this->position;
    }

    function getStats()
    {
        return $this->stats;
    }

    function getExperience()
    {
        return $this->experience;
    }

    function getSkills()
    {
        return $this->skills;
    }
}