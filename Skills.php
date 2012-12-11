<?php
namespace SMGregsList;
class Skills extends Stats
{
    const TYPE = 'skill';
    protected $stats = array(
        'Penalty Expert' => null,
        'Change of Pace' => null,
        'Running with the Ball' => null,
        'Steel Lung' => null,
        'Net Breaker' => null,
        'Panther Save' => null,
        'Aerial Play' => null,
        'Sliding Tackle' => null,
        'Precise Pass' => null,
        'Slalom Ace' => null,
        'Celebrity' => null,
        'Defensive Wall' => null,
    );
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
        return !$value || is_numeric($value) && ($value >= 0 || $value < 5);
    }
}