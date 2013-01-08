 <?php
 $phpSelf = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL);
 ?>
<div class="navbar">
  <div class="navbar-inner">
    <a class="brand" href="#">Merc's List: Striker Manager Transfer Market</a>
    <ul class="nav">
      <li><a href="/sm/index.php">Search for Players</a></li>
      <li class="active"><a href="<?php echo $phpSelf ?>">Sell a Player</a></li>
      <li class="dropdown">
       <a href="#" class="dropdown-toggle" data-toggle="dropdown"><strong>Found a bug?</strong><b class="caret"></b></a>
       <ul class="dropdown-menu">
        <li><a href="https://github.com/cellog/SMGregsList/issues">Please report it</a>.</li>
       </ul>
      </li>
    </ul>
  </div>
</div>
 <p>Please verify submitted information</p>
<form name="search" action="<?php echo $phpSelf ?>" method="post" class="form-horizontal">
<input type="hidden" name="verifytoken" value="1">
<div class="control-group">
 <label class="control-label" for="id">Player ID</label>
 <div class="controls">
  <input type="hidden" name="id" id="id" value="<?php
if ($context->getId()) echo $context->getUrl() . $context->getId()
?>"/><?php if ($context->getId()) echo $context->getUrl() . $context->getId()?>
 </div>
</div>
<div class="control-group">
 <label class="control-label" for="manager">Your Manager name</label>
 <div class="controls">
  <input type="hidden" name="manager" id="manager" value="<?php
if ($context->getManager()) echo $context->getManager()->getName()
?>"/><?php
if ($context->getManager()) echo $context->getManager()->getName()
?>
 </div>
</div>
<div class="control-group">
 <label class="control-label" for="id">Your Code</label>
 <div class="controls">
  <input type="hidden" name="code" id="code" value="<?php
 if ($context->getCode()) echo $context->getCode()
 ?>"/><?php
 if ($context->getCode()) echo $context->getCode()
 ?><br>
 
 </div>
</div>
<div class="control-group">
 <label class="control-label" for="name">Player Name</label>
 <div class="controls">
  <input type="hidden" name="name" id="name" value="<?php
if ($context->getName()) echo $context->getName()
?>"/><?php
if ($context->getName()) echo $context->getName()
?>
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
<div class="control-group">
 <div class="controls">
  <input type="submit" class="btn btn-primary" value="<?php if ($context->getRetrieved()) echo 'Update'; else echo 'Sell' ?>" name="sellfinal" ?>
  <input type="submit" class="btn btn-warning" value="Cancel<?php if ($context->getRetrieved()) echo ' Update' ?>" name="cancel"/>
 </div>
</div>

</form>