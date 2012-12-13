<?php
$chosen = $context->getRawObject()->getPositions();
foreach ($context->listPositions() as $position) {
    $selected = in_array($position, $chosen) ? ' checked="true"' : '';
    echo '<label class="checkbox inline">
    <input type="checkbox" value="' . $position . '" name="position[]"' . $selected . '>' . $position . "</label>\n";
}
?>
