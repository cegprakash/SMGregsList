<?php
echo $savant->render($context->searchform->getRawObject()) ?>
<!-- Modal -->
<button type="button" data-toggle="modal" data-target="#myModal" class="btn btn-info">Show Search Results</button>
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
    <h3 id="myModalLabel">Search Results</h3>
  </div>
  <div class="modal-body">
    <div class="span6">
    <table class="table table-striped table-condensed">
    <thead>
        <tr><th>Name</th><th>Position</th><th>Average</th><th>Age</th><th>Country</th><th>Forecast</th><th>Progression</th></tr>
    </thead>
    <tbody>
    <?php
            echo $savant->render($context->searchresults, 'SMGregsList/searchresults.tpl.php');
            ?>
    </tbody>
    </table>
    </div>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>
