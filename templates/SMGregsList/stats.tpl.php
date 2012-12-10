<?php
$chosen = $context->getRawObject()->getStats();
foreach ($context->listStats() as $position) {
    if ($chosen->$position) {
        $value = $chosen->$position;
    } else {
        $value = '';
    }
    echo '<input name="stats[' . $position . ']" size="3" value="' . $value . '"/> ' . $position . '<br />';
}
?>
