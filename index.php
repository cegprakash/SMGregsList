<?php
namespace SMGregsList;
include __DIR__ . '/autoload.php';
// basic web frontend, chrome extension frontend is json.php
$frontend = new Frontend\HTML;
$main = new Main($frontend);
$main->start();