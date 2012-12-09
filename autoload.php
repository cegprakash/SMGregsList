<?php
function __autoload($class)
{
    include(__DIR__ . '/' . str_replace(array('SMGregsList\\', '\\'), array('', '/'), $class) . '.php');
}