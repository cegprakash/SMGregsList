<?php
$chosen = $context->getRawObject()->getSkills();
foreach ($context->listSkills() as $position) {
    $value = $chosen->$position;
    if (!$value) $value = '0';
    settype($value, 'string');
    echo '<div class="control-group"><span class="span2">' . $position . '</span><select class="span1" name="skills[' . $position . ']">';
    foreach (array('0','1','2','3','4','5') as $min) {
        $selected = $value === $min ? ' selected="true"' : '';
        echo '<option value="' . $min . '"' . $selected . '>' . $min . '</option>';
    }
    echo '</select>' . "</div>\n";
}
?>
