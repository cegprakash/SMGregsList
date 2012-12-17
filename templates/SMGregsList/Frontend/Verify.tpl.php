<h1>Sell a Player</h1>
<h2>Please verify submitted information</h2>
 <?php
 $phpSelf = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL);
 ?>
<form name="search" action="<?php echo $phpSelf ?>" method="post" class="form-horizontal">
<ul class="nav nav-tabs">
 <li class="active"><a href="#basic" data-toggle="tab">Basic Information</a></li>
 <li><a href="#stats" data-toggle="tab">Stats and Skills</a></li>
<?php
 if ($context->getRetrieved()):
?>
 <li><a href="#update" data-toggle="tab">Update a listing</a></li>
<?php endif ?>
 <li><a href="index.php">Search for players</a></li>
</ul>
<div class="tab-content">
<div class="tab-pane active" id="basic">
<div class="control-group">
 <label class="control-label" for="id">Player ID</label>
 <div class="controls">
  <input type="hidden" name="id" id="id" value="<?php
if ($context->getId()) echo $context->getUrl() . $context->getId()
?>"/><?php if ($context->getId()) echo $context->getUrl() . $context->getId()?>
 </div>
</div>
<div class="control-group">
 <label class="control-label" for="age">Age</label>
 <div class="controls">
  <input type="hidden" name="age" id="age" value="<?php
if ($context->getAge()) echo $context->getAge()
?>"/><?php
if ($context->getAge()) echo $context->getAge()
?>
 </div>
</div>
<div class="control-group">
 <label class="control-label" for="average">Average</label>
 <div class="controls">
  <input type="hidden" name="average" id="average" value="<?php
if ($context->getAverage()) echo $context->getAverage()
?>"/><?php
if ($context->getAverage()) echo $context->getAverage()
?>
 </div>
</div>
<div class="control-group">
 <label class="control-label" for="forecast">Forecast</label>
 <div class="controls">
  <input type="hidden" name="forecast" id="forecast" value="<?php
if ($context->getForecast()) echo $context->getForecast()
?>"/><?php
if ($context->getForecast()) echo $context->getForecast()
?>
 </div>
</div>
<div class="control-group">
 <label class="control-label" for="progression">Progression</label>
 <div class="controls">
  <input type="hidden" name="progression" id="progression" value="<?php
if ($context->getProgression()) echo $context->getProgression()
?>"/><?php
if ($context->getProgression()) echo $context->getProgression()
?>
 </div>
</div>
<div class="control-group">
 <label class="control-label" for="experience">Experience</label>
 <div class="controls">
  <input type="hidden" name="experience" id="experience" value="<?php
if ($context->getExperience()) echo $context->getExperience()
?>"/><?php
if ($context->getExperience()) echo $context->getExperience()
?>
 </div>
</div>
<div class="control-group">
 <label class="control-label" for="position">Position</label>
 <div class="controls">
  <input type="hidden" name="position" id="position" value="<?php
if ($context->getPosition()) echo $context->getPosition()
?>"/><?php
if ($context->getPosition()) echo $context->getPosition()
?>
 </div>
</div>
</div> <!-- basic tab pane -->
<div class="tab-pane" id="stats">
<div class="control-group">
 <label class="control-label" for="stats">Stats</label>
 <div class="controls">
 <span class="span3">
 <?php echo $savant->render($context, 'SMGregsList/statsverify.tpl.php') ?>
 </span>
 <span class="span8">
 <div class="control-group">
  <label id="adjust-label" class="control-label" for="skills">Skills</label>
  <div class="controls" id="adjust-controls">
  <?php echo $savant->render($context, 'SMGregsList/skillsverify.tpl.php') ?>
  </div>
 </div>
 </span>
 </div>
</div>
<input type="submit" class="btn btn-primary" value="<?php if ($context->getRetrieved()) echo 'Update'; else echo 'Sell' ?>" name="sellfinal" ?>
<input type="submit" class="btn btn-warning" value="Cancel<?php if ($context->getRetrieved()) echo ' Update' ?>" name="cancel"/>
</div> <!-- stats tab -->
<?php
 if ($context->getRetrieved()):
?>
<div class="tab-pane" id="update">
<div class="control-group">
 <label class="control-label" for="id">Player ID</label>
 <div class="controls">
  <input type="hidden" name="pid" id="pid" value="<?php
if ($context->getId()) echo $context->getUrl() . $context->getId()
?>"/><?php
if ($context->getId()) echo $context->getUrl() . $context->getId()
?>
 </div>
</div>
<div class="control-group">
 <label class="control-label" for="id">Edit Code</label>
 <div class="controls">
  <input type="hidden" name="code" id="code" value="<?php
 if ($context->getCode()) echo $context->getCode()
 ?>"/><?php
 if ($context->getCode()) echo $context->getCode()
 ?>
 
 </div>
</div>
<input type="submit" class="btn btn-primary" value="<?php if ($context->getRetrieved()) echo 'Update'; else echo 'Sell' ?>" name="sellfinal" ?>
<input type="submit" class="btn btn-warning" value="Cancel<?php if ($context->getRetrieved()) echo ' Update' ?>" name="cancel"/>
</div> <!-- update tab -->
<?php endif; ?>
</div> <!-- tab content -->

</form>