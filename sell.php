<?php
namespace SMGregsList;
include __DIR__ . '/autoload.php';

Backend\DataLayer\Sqlite3::$DATABASEPATH = __DIR__ . '/data/test.db';
// basic web frontend, chrome extension frontend is json.php
$frontend = new Frontend\HTML;
$main = new Main($frontend);
$main->start();