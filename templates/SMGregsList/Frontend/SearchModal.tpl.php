<div class="span6">
<table class="table table-striped table-condensed">
<thead>
    <tr><th>id</th><th>Position</th><th>Average</th><th>Age</th><th>Experience</th><th>Forecast</th><th>Progression</th></tr>
</thead>
<tbody>
<?php
        echo $savant->render($context->searchresults, 'SMGregsList/searchresults.tpl.php');
        ?>
</tbody>
</table>
</div>