<?php
namespace SMGregsList;
class Skills extends Stats
{
    const TYPE = 'skill';
    protected $stats = array();
    protected $valid = array(
        'Penalty expert',
        'Change of pace',
        'Running with the ball',
        'Steel lung',
        'Net-breaker',
        'Panther save',
        'Aerial play',
        'Sliding tackle',
        'Precise Pass',
        'Slalom Ace',
        'Celebrity',
        'Defensive wall',
    );

    function validName($name, $value)
    {
        return !$value || is_numeric($value) && ($value >= 0 || $value < 5);
    }
}