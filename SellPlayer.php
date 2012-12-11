<?php
namespace SMGregsList;
class SellPlayer extends Player implements WriteablePlayer
{
    function remove()
    {
        throw new \Exception('Internal Error: SellPlayer must be converted to a data-compatible object to remove');
    }

    function save()
    {
        throw new \Exception('Internal Error: SellPlayer must be converted to a data-compatible object to save');
    }
}