<?php
namespace SMGregsList;
class SavedSearch
{
    public $id;
    protected $description;
    protected $count;
    function __construct($info)
    {
        $this->id = $info[0];
        $this->description = $info[1];
        $this->count = $info[2];
    }

    function abridgedDescription()
    {
        if (strlen($this->description) <= 90) {
            return $this->description;
        }
        $w = wordwrap($this->description, 90, '...', false);
        $w = explode('...', $w);
        return $w[0] . '...';
    }

    function fullDescription()
    {
        return $this->description;
    }

    function getCount()
    {
        return $this->count;
    }
}