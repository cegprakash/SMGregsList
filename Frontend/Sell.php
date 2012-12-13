<?php
namespace SMGregsList\Frontend;
use SMGregsList\Player;
/**
 * a proxy, used for selecting templates
 */
class Sell
{
    private $player;
    function __construct(Player $player)
    {
        $this->player = $player;
    }

    function __get($name)
    {
        return $this->player->$name;
    }

    function __set($name, $value)
    {
        $this->player->$name = $value;
    }

    function __call($func, $args)
    {
        return call_user_func_array(array($this->player, $func), $args);
    }
}