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
 <p>List a Striker Manager player as being available for transfer agreement</p>
<form name="sell" action="<?php echo $phpSelf ?>" method="post" class="form-horizontal">
<div class="accordion" id="accordion2">
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#basic">
        Basic Information [Step 1]
      </a>
    </div>
    <div id="basic" class="accordion-body collapse in">
      <div class="accordion-inner">
<div class="control-group">
 <label class="control-label" for="id">Player ID</label>
 <div class="controls">
  <input placeholder="Enter ID or URL (http://en3.strikermanager.com/jugador.php?id_jugador=12345)" type="text" class="span5" name="id" id="id" value="<?php
if ($context->getId()) echo $context->getUrl() . $context->getId()
?>"/>
 </div>
</div>
<div class="control-group">
 <label class="control-label" for="manager">Your Manager name<br><small>(such as <a href="http://en.strikermanager.com/usuario.php?id=10460314">CelloG</a>)</small></label>
 <div class="controls">
  <input placeholder="Manager name" type="text" class="span5" name="manager" id="manager" value="<?php
if ($context->getManager()) echo $context->getManager()->getName()
?>"/><br>
<small>Note: if you have ever sold a player before, you will need to enter your code</small>
 </div>
</div>
<div class="control-group">
 <label class="control-label" for="id">Your Code</label>
 <div class="controls">
  <input placeholder="your code" type="text" name="code" id="code" value="<?php
 if ($context->getCode()) echo $context->getCode()
 ?>"/> <input type="submit" value="Retrieve Player" class="btn btn-primary" name="retrieve"/><br>
 <span class="help-block">Note: You must click the "Retrieve Player" button in order to update or delete a listing<br>
 The update code was given when you added the player</span>
 
 </div>
</div>
<div class="control-group">
 <label class="control-label" for="name">Player Name</label>
 <div class="controls">
  <input placeholder="Player's name" type="text" class="input" name="name" id="name" value="<?php
if ($context->getName()) echo $context->getName()
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
 <label class="control-label" for="age">Age</label>
 <div class="controls">
  <input placeholder="Age" type="text" class="input-mini" name="age" id="age" value="<?php
if ($context->getAge()) echo $context->getAge()
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
</div>
</div>
</div> <!-- basic accordion -->
 <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#stats">
        Advanced (stats and skills) [Step 2]
      </a> 
    </div>
    <div id="stats" class="accordion-body collapse">
      <div class="accordion-inner">
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
</div>
</div>
</div> <!-- stats accordion -->
<div class="alert alert-info">
 <strong>Notice!</strong> If you are updating or deleting a listing, you must retrieve the player info first
 <button type="button" class="close" data-dismiss="alert">x</button>
</div>
<div class="control-group">
 <div class="controls">
<input type="submit" class="btn btn-primary" value="<?php if ($context->getRetrieved()) echo 'Update'; else echo 'Sell' ?>" name="verify" ?>
<input type="submit" class="btn btn-warning" value="Cancel<?php if ($context->getRetrieved()) echo ' Update' ?>" name="cancel"/>
<?php
 if ($context->getRetrieved()):
?>
 <input type="submit" class="btn btn-danger" value="Stop Selling" name="delete" onclick="return confirm('This will remove your player from the for sale list.  Are you sure?');"/>
<?php endif;
?>
 </div>
</div>
</form>
</div> <!-- accordion content -->
