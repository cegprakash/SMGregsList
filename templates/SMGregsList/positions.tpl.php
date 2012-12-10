<?php
$chosen = $context->getRawObject()->getPositions();
foreach ($context->listPositions() as $position) {
    $selected = in_array($position, $chosen) ? ' checked="true"' : '';
    echo '<input type="checkbox" value="' . $position . '" name="position"' . $selected . '>' . $position . "<br />\n";
}
?>
