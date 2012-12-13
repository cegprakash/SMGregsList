<?php
$chosen = $context->getRawObject()->getStats();
foreach ($context->listStats() as $position) {
    if ($chosen->$position) {
        $value = $chosen->$position;
    } else {
        $value = '';
    }?><?php
    echo '<div class="control-group"><span class="span5">' . $position . '</span><input class="input-mini" placeholder="0&rarr;99" name="stats[' .
    $position . ']" size="3" value="' . $value . '"/></div>';
}
?>
