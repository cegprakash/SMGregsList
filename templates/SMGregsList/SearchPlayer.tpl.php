 <?php
 $phpSelf = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL);
 ?>
<div class="navbar">
  <div class="navbar-inner">
    <a class="brand" href="#">Merc's List: Striker Manager Transfer Market</a>
    <ul class="nav">
      <li class="active"><a href="<?php echo $phpSelf ?>">Search for Players</a></li>
<?php if (SMGregsList\Frontend\HTMLController::showSell()): ?>
      <li><a href="sell.php">Sell a Player</a></li>
<?php endif; ?>
      <li class="dropdown">
       <a href="#" class="dropdown-toggle" data-toggle="dropdown"><strong>Found a bug?</strong><b class="caret"></b></a>
       <ul class="dropdown-menu">
        <li><a href="https://github.com/cellog/SMGregsList/issues">Please report it</a>.</li>
       </ul>
      </li>
    </ul>
  </div>
</div>
 <p>Search for Striker Manager players that are for sale by transfer agreement</p>
<form name="search" action="<?php echo $phpSelf ?>" method="get" class="form-horizontal">
<div class="accordion" id="accordion2">
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#savedsearches">
        Saved Searches
      </a>
    </div>
    <div id="savedsearches" class="accordion-body collapse in">
      <div class="accordion-inner">
        <?php echo $savant->render($parent->context->savedSearches) ?>
      </div>
    </div>
  </div> <!-- saved searches -->
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#basic">
        Basic Information [Step 1]
      </a>
    </div>
    <div id="basic" class="accordion-body collapse in">
      <div class="accordion-inner">
      </div>
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
 <label class="control-label" for="manager">Manager</label>
 <div class="controls">
  <input placeholder="Manager selling this player" type="text" class="input" name="manager" id="manager" value="<?php
if ($context->getManager()) echo $context->getManager()
?>"/>
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
 <label class="control-label" for="position">Positions</label>
 <div class="controls">
  <?php echo $savant->render($context, 'SMGregsList/positions.tpl.php') ?>
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
</div>
</div>
</div> <!-- stats accordion -->
</div> <!-- accordion content -->
<div class="control-group">
 <div class="controls">
  <input type="submit" value="Search" class="btn btn-primary" name="searchbutton"/>
 </div>
</div>
