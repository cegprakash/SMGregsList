<?php
echo $savant->render($context->searchform->getRawObject()) ?>
<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
    <h3 id="myModalLabel">Search Results<br><small>for <?php echo $context->searchform->humanReadableName() ?></small></h3>
  </div>
  <div class="modal-body">
    <div class="alert fade in">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Sorting Tip:</strong> Click the column headers.  Hold Shift and click to sort by more than 1 header at a time.
    </div>
    <table class="tablesorter" id="searchresultstable">
    <thead>
        <tr><th>Name</th><th>Pos.</th><th>Av.</th><th>Age</th><th>Country</th><th>FC</th><th>Prog.</th></tr>
    </thead>
    <tbody>
    <?php
            echo $savant->render($context->searchresults, 'SMGregsList/searchresults.tpl.php');
            ?>
    </tbody>
    </table>
  </div>
  <div class="modal-footer">
    <table class="table">
        <tr><th>Manager Name</th><th>Code</th></tr>
        <tr><td>
    <input type="text" placeholder="Your Manager Name" name="managername" value="<?php if ($context->manager) echo $context->manager ?>">
    </td><td>
    <input type="text" placeholder="Your Code" name="managercode" value="<?php if ($context->code) echo $context->code ?>">
    </td></tr>
    </table>
    <input type="submit" class="btn btn-success" value="Save Search" id="savesearch" name="savesearch" data-placement="top"
    rel="popover" data-trigger="hover" data-content="Saving a search will notify you whenever anyone lists a player for sale that matches the search.  You need to enter your manager name on Striker Manager, and the manager code (leave the code blank if you don't have one, and one will be generated for you)" data-title="Save a search">
  </div>
</div>
</form>
