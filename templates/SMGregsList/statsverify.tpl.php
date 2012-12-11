<table><?php
$chosen = $context->getRawObject()->getStats();
$i = 0;
foreach ($context->listStats() as $stat) {
    if ($chosen->$stat) {
        $value = $chosen->$stat;
    } else {
        $value = '';
    }
    if ($i++%2) {
        $background = ' style="background-color:#CCCCCC"';
    } else {
        $background = ' style="background-color:#EEEEEE"';
    }
    echo '<tr' . $background . '><td><input type="hidden" name="stats[' . $stat . ']" value="' . $value . '"/> ' . $stat .
        '</td><td>' . $value .'</td></tr>';
}
?>
</table>