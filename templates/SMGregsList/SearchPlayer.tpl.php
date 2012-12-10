<h1>Search Form</h1>
<form name="search" action="<?php echo basename(__FILE__) ?>">
<div><span>Player ID</span><input type="text" size="100" name="id" id="id" value="<?php
echo $context->getFormattedId()
?>"/></div>
<div><span>Age</span><input type="text" size="100" name="id" id="id" value="<?php
if ($context->getAge()) echo $context->getAge()
?>"/></div>
<div><span>Average</span><input type="text" size="100" name="id" id="id" value="<?php
if ($context->getAverage()) echo $context->getAverage()
?>"/></div>
<div><span>Forecast</span><input type="text" size="100" name="id" id="id" value="<?php
if ($context->getForecast()) echo $context->getForecast()
?>"/></div>
<div><span>Progression</span><input type="text" size="100" name="id" id="id" value="<?php
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