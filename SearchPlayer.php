<?php
namespace SMGregsList;
class SearchPlayer extends Player implements SearchablePlayer
{
    function getSearchComponents()
    {
        $ret = array();
        if ($this->age) {
            if (strpos($this->age, '-')) {
                $age = explode('-', $this->age);
                $start = $age[0];
                $end = $age[1];
                $ret['minage'] = $start;
                $ret['maxage'] = $end;
            } else {
                $ret['maxage'] = $this->age;
            }
        }
        if ($this->average) {
            if (strpos($this->average, '-')) {
                $age = explode('-', $this->average);
                $start = $age[0];
                $end = $age[1];
                $ret['minaverage'] = $start;
                $ret['maxaverage'] = $end;
            } else {
                $ret['minaverage'] = $this->average;
                if ($this->maxaverage) {
                    $ret['maxaverage'] = $this->average;
                }
            }
        }
        if ($this->position) {
            if (strpos($this->position, ',')) {
                $positions = explode(',', $this->position);
                $ret['positions'] = $positions;
            } else {
                $ret['positions'] = $this->position;
                settype($ret['positions'], 'array');
            }
        }
        if ($this->experience) {
            $ret['experience'] = $this->experience;
        }
        if ($this->forecast) {
            $ret['forecast'] = $this->forecast;
        }
        if ($this->progression) {
            $ret['progression'] = $this->progression;
        }
        if (count($this->skills)) {
            $ret['skills'] = array();
            foreach ($this->skills as $skill => $minvalue) {
                $ret['skills'][$skill]['minvalue'] = $minvalue;
            }
        }
        if (count($this->stats)) {
            $ret['stats'] = array();
            foreach ($this->stats as $stat => $minvalue) {
                $ret['stats'][$stat]['minvalue'] = $minvalue;
            }
        }
        return $ret;
    }

    function getMinage()
    {
        if (!$this->age) {
            return 0;
        }
        if (strpos($this->age, '-')) {
            $age = explode('-', $this->age);
            return $age[0];
        }
        return 0;
    }

    function getMaxage()
    {
        if (!$this->age) {
            return 0;
        }
        if (strpos($this->age, '-')) {
            $age = explode('-', $this->age);
            return $age[1];
        }
        return $this->age;
    }

    function getMinaverage()
    {
        if (!$this->average) {
            return 0;
        }
        if (strpos($this->average, '-')) {
            $average = explode('-', $this->average);
            return $average[0];
        }
        return 0;
    }

    function getMaxaverage()
    {
        if (!$this->average) {
            return 0;
        }
        if (strpos($this->average, '-')) {
            $average = explode('-', $this->average);
            return $average[1];
        }
        return $this->average;
    }

    function getPositions()
    {
        if ($this->position) {
            if (strpos($this->position, ',')) {
                $positions = explode(',', $this->position);
                $ret = $positions;
            } else {
                $ret = $this->position;
                settype($ret, 'array');
            }
        } else {
            return array();
        }
        return $ret;
    }
}