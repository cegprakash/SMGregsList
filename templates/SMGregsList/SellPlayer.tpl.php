 <h1>Greg's List: Sell a Player</h1>
 <p>List a Striker Manager player as being available for transfer agreement</p>
 <p>To search for a player <a href="index.php">Click here</a>.</p>
 <?php
 $phpSelf = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL);
 ?>
<form name="search" action="<?php echo $phpSelf ?>" method="post">
<div style="background-color:#FFDDDD">
 <span>Edit Code</span><input type="text" size="100" name="code" id="code" value="<?php
 if ($context->getCode()) echo $context->getCode()
 ?>"/>
 <input type="submit" value="Retrieve Player" name="retrieve"/>
<?php
 if ($context->getRetrieved()):
?>
 <input type="submit" value="Update Player" name="edit" onclick="return confirm('This will update your player based on the current form.  Are you sure?');"/>
 <input type="submit" value="Stop Selling" name="delete" onclick="return confirm('This will remove your player from the for sale list.  Are you sure?');"/>
<?php endif;
?>
</div>
<div><span>Player ID</span><input type="text" size="100" name="id" id="id" value="<?php
echo $context->getUrl() . $context->getId()
?>"/></div>
<div><span>Age</span><input type="text" size="3" name="age" id="age" value="<?php
if ($context->getAge()) echo $context->getAge()
?>"/></div>
<div><span>Average</span><input type="text" size="3" name="average" id="average" value="<?php
if ($context->getAverage()) echo $context->getAverage()
?>"/></div>
<div><span>Forecast</span><input type="text" size="3" name="forecast" id="forecast" value="<?php
if ($context->getForecast()) echo $context->getForecast()
?>"/></div>
<div><span>Progression</span><input type="text" size="3" name="progression" id="progression" value="<?php
if ($context->getProgression()) echo $context->getProgression()
?>"/></div>
<div><span>Experience</span><input type="text" size="4" name="experience" id="experience" value="<?php
if ($context->getExperience()) echo $context->getExperience()
?>"/></div>
<div><span>Position</span><?php echo $savant->render($context, 'SMGregsList/sellpositions.tpl.php') ?></div>
<table width="100%">
<tr><td>Stats</td><td>Skills</td></tr>
<tr>
 <td valign="top" width="15%"><?php echo $savant->render($context, 'SMGregsList/stats.tpl.php') ?></td>
 <td valign="top"><?php echo $savant->render($context, 'SMGregsList/skills.tpl.php') ?></td>
</tr>
</table>
<input type="submit" value="Sell" name="verify" ?>
<input type="submit" value="Cancel" name="cancel"/>

</form>
<p>Found a bug? <a href="https://github.com/cellog/SMGregsList/issues">Please report it</a>.</p>
