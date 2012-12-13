<h1>Sell a Player</h1>
<h2>Please verify submitted information</h2>
 <?php
 $phpSelf = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL);
 ?>
<form name="search" action="<?php echo $phpSelf ?>" method="post">
<table>
<?php if ($context->getCode()): ?>
<tr style="background-color:#FFDDDD"><td>Edit Code</td><td><input type="hidden" name="code" value="<?php echo $context->getCode() ?>"/>
<?php
echo $context->getCode()
?>
</td></tr>
<?php endif ?>
<input type="hidden" name="verifytoken" value="verify"/>
<tr style="background-color:#EEEEEE"><td>Player ID</td><td><input type="hidden" name="id" id="id" value="<?php
echo $context->getUrl() . $context->getId()
?>"/><a href="<?php
echo $context->getUrl() . $context->getId()
?>" target="_blank"><?php
echo $context->getUrl() . $context->getId()
?></a></td></tr>
<tr style="background-color:#EEEEEE"><td>Age</td><td><input type="hidden" name="age" id="age" value="<?php
if ($context->getAge()) echo $context->getAge()
?>"/><?php
if ($context->getAge()) echo $context->getAge()
?></td></tr>
<tr style="background-color:#EEEEEE"><td>Average</td><td><input type="hidden" name="average" id="average" value="<?php
if ($context->getAverage()) echo $context->getAverage()
?>"/><?php
if ($context->getAverage()) echo $context->getAverage()
?></td></tr>
<tr style="background-color:#EEEEEE"><td>Forecast</td><td><input type="hidden" name="forecast" id="forecast" value="<?php
if ($context->getForecast()) echo $context->getForecast()
?>"/><?php
if ($context->getForecast()) echo $context->getForecast()
?></td></tr>
<tr style="background-color:#EEEEEE"><td>Progression</td><td><input type="hidden" name="progression" id="progression" value="<?php
if ($context->getProgression()) echo $context->getProgression()
?>"/><?php
if ($context->getProgression()) echo $context->getProgression()
?></td></tr>
<tr style="background-color:#EEEEEE"><td>Experience</td><td><input type="hidden" name="experience" id="experience" value="<?php
if ($context->getExperience()) echo $context->getExperience()
?>"/><?php
if ($context->getExperience()) echo $context->getExperience()
?></td></tr>
<tr style="background-color:#EEEEEE"><td>Position</td><td><input type="hidden" name="position" id="position" value="<?php
if ($context->getPosition()) echo $context->getPosition()
?>"/><?php
if ($context->getPosition()) echo $context->getPosition()
?></td></tr>
</table>
<table>
<tr><td>Stats</td><td>Skills</td></tr>
<tr>
 <td valign="top"><?php echo $savant->render($context, 'SMGregsList/statsverify.tpl.php') ?></td>
 <td valign="top"><?php echo $savant->render($context, 'SMGregsList/skillsverify.tpl.php') ?></td>
</tr>
</table>
<input type="submit" value="Sell" name="sellfinal"/><input type="submit" value="Cancel" name="cancel"/>

</form>