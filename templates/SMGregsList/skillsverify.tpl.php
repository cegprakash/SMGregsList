<table><?php
$chosen = $context->getRawObject()->getSkills();
$i = 0;
foreach ($context->listSkills() as $skill) {
    if ($chosen->$skill) {
        $value = $chosen->$skill;
    } else {
        $value = '';
    }
    if ($i++%2) {
        $background = ' style="background-color:#CCCCCC"';
    } else {
        $background = ' style="background-color:#EEEEEE"';
    }
    echo '<tr' . $background . '><td><input type="hidden" name="skills[' . $skill . ']" value="' . $value . '"/> ' . $skill .
        '</td><td>' . $value .'</td></tr>';
}
?>
</table>
