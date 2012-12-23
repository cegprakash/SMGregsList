 <h1>Merc's List: Striker Manager Transfer Market</h1>
 <p>Search for Striker Manager players that are for sale by transfer agreement</p>
 <?php
 $phpSelf = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL);
 ?>
<form name="search" action="<?php echo $phpSelf ?>" method="get" class="form-horizontal">
<ul class="nav nav-tabs">
 <li class="active"><a href="#basic" data-toggle="tab">Basic Information</a></li>
 <li><a href="#stats" data-toggle="tab">Stats and Skills</a></li>
 <li><a href="sell.php">Sell a Player</a></li>
</ul>
<div class="tab-content">
<div class="tab-pane active" id="basic">
<div class="control-group">
 <label class="control-label" for="id">Player ID</label>
 <div class="controls">
  <input placeholder="Enter the ID or the full URL" type="text" name="id" id="id" value="<?php
if ($context->getId()) echo $context->getUrl() . $context->getId()
?>"/>
 </div>
</div>
<div class="control-group">
 <label class="control-label" for="minage">Age</label>
 <div class="controls">
  <input placeholder="Min" type="text" class="input-mini" name="minage" id="minage" value="<?php
if ($context->getMinage()) echo $context->getMinage()
?>"/>&rarr;<input placeholder="Max" type="text" class="input-mini" name="maxage" id="maxage" value="<?php
if ($context->getMaxage()) echo $context->getMaxage()
?>"/>
 </div>
</div>
<div class="control-group">
 <label class="control-label" for="minaverage">Average</label>
 <div class="controls">
  <input placeholder="Min" type="text" class="input-mini" name="minaverage" id="minaverage" value="<?php
if ($context->getMinaverage()) echo $context->getMinaverage()
?>"/>&rarr;<input placeholder="Max" type="text" class="input-mini" name="maxaverage" id="maxaverage" value="<?php
if ($context->getMaxaverage()) echo $context->getMaxaverage()
?>"/>
 </div>
</div>
<div class="control-group">
 <label class="control-label" for="forecast">Forecast (Minimum)</label>
 <div class="controls">
  <input placeholder="Forecast (1-99)" type="text" class="input-mini" name="forecast" id="forecast" value="<?php
if ($context->getForecast()) echo $context->getForecast()
?>"/>
 </div>
</div>
<div class="control-group">
 <label class="control-label" for="progression">Progression (Minimum)</label>
 <div class="controls">
  <input placeholder="Progression (1-100)" type="text" class="input-mini" name="progression" id="progression" value="<?php
if ($context->getProgression()) echo $context->getProgression()
?>"/>
 </div>
</div>
<div class="control-group">
 <label class="control-label" for="country">Country</label>
 <div class="controls">
  <input placeholder="Country name or fragment" type="text" class="input" name="country" id="country" value="<?php
if ($context->getCountry()) echo $context->getCountry()
?>"/>
 </div>
</div>
<div class="control-group">
 <label class="control-label" for="position">Positions</label>
 <div class="controls">
  <?php echo $savant->render($context, 'SMGregsList/positions.tpl.php') ?>
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
</div> <!-- stats tab -->
</div> <!-- tab content -->
<div class="control-group">
 <div class="controls">
  <input type="submit" value="Search" class="btn btn-primary" name="searchbutton"/>
 </div>
</div>
</form>

<p>Found a bug? <a href="https://github.com/cellog/SMGregsList/issues">Please report it</a>.</p>