 <?php
 $phpSelf = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL);
 ?>
<div class="navbar">
  <div class="navbar-inner">
    <a class="brand" href="#">Merc's List: Striker Manager Transfer Market</a>
    <ul class="nav">
      <li><a href="/sm/index.php">Search for Players</a></li>
      <li class="active"><a href="<?php echo $phpSelf ?>">Sell a Player</a></li>
    </ul>
  </div>
</div>
 <h1>Merc's List: Sell Your Player</h1>
 <p>List a Striker Manager player as being available for transfer agreement</p>
<form name="sell" action="<?php echo $phpSelf ?>" method="post" class="form-horizontal">
<ul class="nav nav-tabs">
 <li class="active"><a href="#basic" data-toggle="tab">Basic Information</a></li>
 <li><a href="#stats" data-toggle="tab">Stats and Skills</a></li>
 <li><a href="#update" data-toggle="tab">Update a listing</a></li>
 <li><a href="index.php">Search for players</a></li>
</ul>
<div class="tab-content">
<div class="tab-pane active" id="basic">
<div class="control-group">
 <label class="control-label" for="id">Player ID</label>
 <div class="controls">
  <input placeholder="Enter the ID or the full URL" type="text" class="span5" name="id" id="id" value="<?php
if ($context->getId()) echo $context->getUrl() . $context->getId()
?>"/>
 </div>
</div>
<div class="control-group">
 <label class="control-label" for="age">Age</label>
 <div class="controls">
  <input placeholder="Age" type="text" class="input-mini" name="age" id="age" value="<?php
if ($context->getAge()) echo $context->getAge()
?>"/>
 </div>
</div>
<div class="control-group">
 <label class="control-label" for="average">Average</label>
 <div class="controls">
  <input placeholder="Average" type="text" class="input-mini" name="average" id="average" value="<?php
if ($context->getAverage()) echo $context->getAverage()
?>"/>
 </div>
</div>
<div class="control-group">
 <label class="control-label" for="forecast">Forecast</label>
 <div class="controls">
  <input placeholder="Forecast (1-99)" type="text" class="input-mini" name="forecast" id="forecast" value="<?php
if ($context->getForecast()) echo $context->getForecast()
?>"/>
 </div>
</div>
<div class="control-group">
 <label class="control-label" for="progression">Progression</label>
 <div class="controls">
  <input placeholder="Progression (1-100)" type="text" class="input-mini" name="progression" id="progression" value="<?php
if ($context->getProgression()) echo $context->getProgression()
?>"/>
 </div>
</div>
<div class="control-group">
 <label class="control-label" for="experience">Experience</label>
 <div class="controls">
  <input placeholder="Experience" type="text" class="input-mini" name="experience" id="experience" value="<?php
if ($context->getExperience()) echo $context->getExperience()
?>"/>
 </div>
</div>
<div class="control-group">
 <label class="control-label" for="position">Position</label>
 <div class="controls">
  <?php echo $savant->render($context, 'SMGregsList/sellpositions.tpl.php') ?>
 </div>
</div>
</div> <!-- basic tab pane -->
<div class="tab-pane" id="stats">
<div class="control-group">
 <label class="control-label" for="stats">Stats</label>
 <div class="controls">
 <span class="span3">
 <?php echo $savant->render($context, 'SMGregsList/stats.tpl.php') ?>
 </span>
 <span class="span8">
 <div class="control-group">
  <label id="adjust-label" class="control-label" for="skills">Skills</label>
  <div class="controls" id="adjust-controls">
  <?php echo $savant->render($context, 'SMGregsList/skills.tpl.php') ?>
  </div>
 </div>
 </span>
 </div>
</div>
<input type="submit" class="btn btn-primary" value="<?php if ($context->getRetrieved()) echo 'Update'; else echo 'Sell' ?>" name="verify" ?>
</div> <!-- stats tab -->
<div class="tab-pane" id="update">
<div class="control-group">
 <label class="control-label" for="id">Player ID</label>
 <div class="controls">
  <input placeholder="Enter the ID or the full URL" class="span5" type="text" name="pid" id="pid" value="<?php
if ($context->getId()) echo $context->getUrl() . $context->getId()
?>"/>
 </div>
</div>
<div class="control-group">
 <label class="control-label" for="id">Edit Code</label>
 <div class="controls">
  <input placeholder="To retrieve player details, enter a code" type="text" name="code" id="code" value="<?php
 if ($context->getCode()) echo $context->getCode()
 ?>"/> <input type="submit" value="Retrieve Player" class="btn btn-primary" name="retrieve"/>
 
 </div>
</div>
</div> <!-- update tab -->
</div> <!-- tab content -->
<input type="submit" class="btn btn-primary" value="<?php if ($context->getRetrieved()) echo 'Update'; else echo 'Sell' ?>" name="verify" ?>
<input type="submit" class="btn btn-warning" value="Cancel<?php if ($context->getRetrieved()) echo ' Update' ?>" name="cancel"/>
<?php
 if ($context->getRetrieved()):
?>
 <input type="submit" class="btn btn-danger" value="Stop Selling" name="delete" onclick="return confirm('This will remove your player from the for sale list.  Are you sure?');"/>
<?php endif;
?>
</form>
<p>Found a bug? <a href="https://github.com/cellog/SMGregsList/issues">Please report it</a>.</p>
