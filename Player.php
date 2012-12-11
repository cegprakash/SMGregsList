<?php
namespace SMGregsList;
abstract class Player
{
    const PLAYERURL = 'http://en3.strikermanager.com/jugador.php?id_jugador=';
    protected
        $average = 0,
        $id = 0,
        $forecast = 0,
        $progression = 0,
        $age = 0,
        $position = false,
        $stats,
        $experience = 0,
        $skills,
        $createstamp;

    function __construct()
    {
        $this->skills = new Skills;
        $this->stats = new Stats;
    }

    function __set($name, $value)
    {
        $this->$name = $value;
    }

    function getId()
    {
        return $this->id;
    }

    function getUrl()
    {
        if ($this->id) {
            return static::PLAYERURL;
        }
    }

    function getCreatestamp()
    {
        if ($this->createstamp) {
            return $this->createstamp;
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

    function listPositions()
    {
        return array(
            'GK',
            'LB',
            'LDF',
            'CDF',
            'RDF',
            'RB',
            'DFM',
            'LM',
            'LIM',
            'IM',
            'RIM',
            'RM',
            'OM',
            'RW',
            'RF',
            'CF',
            'LF',
            'LW'
        );
    }

    function listStats()
    {
        return array(
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
            'Versatility'
        );
    }

    function listSkills()
    {
        return array(
            'Penalty Expert',
            'Change of Pace',
            'Running with the Ball',
            'Steel Lung',
            'Net Breaker',
            'Panther Save',
            'Aerial Play',
            'Sliding Tackle',
            'Precise Pass',
            'Slalom Ace',
            'Celebrity',
            'Defensive Wall'
        );
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