<?php
namespace SMGregsList;
class Skills extends Stats
{
    const TYPE = 'skill';
    protected $stats = array();
    protected $valid = array(
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
        'Defensive Wall',
    );

    function validName($name, $value)
    {
        return is_numeric($value) && ($value < 1 || $value > 5);
    }
}