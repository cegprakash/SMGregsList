<table><?php
$chosen = $context->getRawObject()->getStats();
foreach ($context->listStats() as $stat) {
    if ($chosen->$stat) {
        $value = $chosen->$stat;
    } else {
        $value = '';
    }
    echo '<div class="control-group"><span class="span5">' . $stat . '</span><input type="hidden" name="stats[' .
    $stat . ']" value="' . $value . '"/>' . $value . '</div>';
}
?>
</table>