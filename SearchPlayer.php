<?php
namespace SMGregsList;
class SearchPlayer extends Player implements SearchablePlayer
{
    function search()
    {
        // can't search without a data source
        throw new \Exception("Internal error: attempt to search with a generic searchable player, no data source selected");
    }

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
                $ret['maxaverage'] = $this->average;
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
        if ($this->country) {
            $ret['country'] = $this->country;
        }
        if ($this->manager) {
            $ret['manager'] = $this->manager;
        }
        if ($this->name) {
            $ret['name'] = $this->name;
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
                $ret['skills'][$skill] = $minvalue;
            }
        }
        if (count($this->stats)) {
            $ret['stats'] = array();
            foreach ($this->stats as $stat => $minvalue) {
                $ret['stats'][$stat] = $minvalue;
            }
        }
        return $ret;
    }

    function humanReadableName()
    {
        $components = $this->getSearchComponents();
        $ret = 'Players with';
        $and = '';
        if (isset($components['minaverage']) && $components['maxaverage']) {
            $ret .= ' average between ' . $components['minaverage'] . '-' . $components['maxaverage'];
            $and = ' and';
        } elseif (isset($components['minaverage'])) {
            $ret .= ' minimum average ' . $components['minaverage'];
            $and = ' and';
        } elseif (isset($components['maxaverage'])) {
            $ret .= ' maximum average ' . $components['maxaverage'];
            $and = ' and';
        }
        if (isset($components['minage']) && $components['maxage']) {
            $ret .= $and . ' age between ' . $components['minage'] . '-' . $components['maxage'];
            $and = ' and';
        } elseif (isset($components['minage'])) {
            $ret .= $and . ' minimum age ' . $components['minage'];
            $and = ' and';
        } elseif (isset($components['maxage'])) {
            $ret .= $and . ' maximum age ' . $components['maxage'];
            $and = ' and';
        }
        foreach ($components as $component => $value) {
            switch ($component) {
                case 'positions':
                    $ret .= $and . ' one of these positions: ' . implode(',', $value);
                    $and = ' and';
                    break;
                case 'country':
                    $ret .= $and . ' country containing "' . $value . '"';
                    $and = ' and';
                    break;
                case 'manager':
                    $ret .= $and . ' selling manager containing "' . $value . '"';
                    $and = ' and';
                    break;
                case 'name':
                    $ret .= $and . ' player name containing "' . $value . '"';
                    $and = ' and';
                    break;
                case 'experience':
                case 'forecast':
                case 'progression':
                    $ret .= $and . ' ' . $component . ' >= ' . $value;
                    $and = ' and';
                    break;
                case 'skills':
                case 'stats':
                    foreach ($value as $skill => $minvalue) {
                        $ret .= $and . ' minimum ' . $minvalue . ' ' . $skill . ' ' . $component;
                        $and = ' and';
                    }
                    break;
            }
        }
        if ($ret == 'Players with') $ret = 'Any Player for sale';
        return $ret;
    }

    function getHashedId()
    {
        if (!$this->createstamp) {
            return 0;
        }
        return base_convert(strtotime($this->createstamp), 10, 25);
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