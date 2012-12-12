<?php
function __autoload($class)
{
    if (strpos($class, 'PEAR2') !== false) {
        include (str_replace('\\', '/', $class) . '.php');
        return;
    }
    include(__DIR__ . '/' . str_replace(array('SMGregsList\\', '\\'), array('', '/'), $class) . '.php');
}
set_exception_handler(function ($e) {
    ?><h1>Error</h1>
<p><?php echo $e->getMessage() ?></p>
<a href="javascript:history.go(-1)"><< Return</a><?php
});