 <h1>Greg's List: Search Form</h1>
 <p>Search for Striker Manager players that are for sale by transfer agreement</p>
 <p>To sell a player <a href="sell.php">Click here</a>.</p>
 <?php
 $phpSelf = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL);
 ?>
<form name="search" action="<?php echo $phpSelf ?>" method="post">
<div><span>Player ID</span><input type="text" size="100" name="id" id="id" value="<?php
echo $context->getUrl() . $context->getId()
?>"/></div>
<div><span>Minimum Age</span><input type="text" size="3" name="minage" id="minage" value="<?php
if ($context->getMinage()) echo $context->getMinage()
?>"/><span>Maximum Age</span><input type="text" size="3" name="maxage" id="maxage" value="<?php
if ($context->getMaxage()) echo $context->getMaxage()
?>"/></div>
<div><span>Minimum Average</span><input type="text" size="3" name="minaverage" id="minaverage" value="<?php
if ($context->getMinaverage()) echo $context->getMinaverage()
?>"/><span>Maximum Average</span><input type="text" size="3" name="maxaverage" id="maxaverage" value="<?php
if ($context->getMaxaverage()) echo $context->getMaxaverage()
?>"/></div>
<div><span>Minimum Forecast</span><input type="text" size="3" name="forecast" id="forecast" value="<?php
if ($context->getForecast()) echo $context->getForecast()
?>"/></div>
<div><span>Minimum Experience</span><input type="text" size="3" name="experience" id="experience" value="<?php
if ($context->getExperience()) echo $context->getExperience()
?>"/></div>
<div><span>Minimum Progression</span><input type="text" size="3" name="progression" id="progression" value="<?php
if ($context->getProgression()) echo $context->getProgression()
?>"/></div>
<table width="100%">
<tr><td>Position</td><td>Stats</td><td>Skills</td></tr>
<tr>
 <td width="5%"><?php echo $savant->render($context, 'SMGregsList/positions.tpl.php') ?></td>
 <td valign="top" width="15%"><?php echo $savant->render($context, 'SMGregsList/stats.tpl.php') ?></td>
 <td valign="top"><?php echo $savant->render($context, 'SMGregsList/skills.tpl.php') ?></td>
</tr>
</table>
<input type="submit" value="Search"/>
</form>
<p>Found a bug? <a href="https://github.com/cellog/SMGregsList/issues">Please report it</a>.</p>