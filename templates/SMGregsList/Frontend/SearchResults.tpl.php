<?php
echo $savant->render($context->searchform->getRawObject()) ?>
<table border="1">
    <tr><td>id</td><td>Position</td><td>Average</td><td>Age</td><td>Experience</td><td>Forecast</td><td>Progression</td></tr>
<?php
        echo $savant->render($context->searchresults, 'SMGregsList/searchresults.tpl.php');
        ?></table>