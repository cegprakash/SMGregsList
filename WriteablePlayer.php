<?php
namespace SMGregsList;
interface WriteablePlayer
{
    function exists();
    function retrieve();
    function remove();
    function save();
}