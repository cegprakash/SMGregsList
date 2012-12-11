<select name="position"><?php
foreach ($context->listPositions() as $position) {
    $selected = $position == $context->getPosition() ? ' selected="true"' : '';
    echo '<option value="' . $position . '"' . $selected . '>' . $position . "</option>\n";
}
?>
</select>