<table><?php
$chosen = $context->getRawObject()->getSkills();
foreach ($context->listSkills() as $skill) {
    if ($chosen->$skill) {
        $value = $chosen->$skill;
    } else {
        $value = '';
    }
    echo '<div class="control-group"><span class="span5">' . $skill . '</span><input type="hidden" name="skills[' .
    $skill . ']" value="' . $value . '"/>' . $value . '</div>';
}
?>
</table>
