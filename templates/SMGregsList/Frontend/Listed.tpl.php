<h1>Thank you, your player has been placed for sale</h1>
<p>If you wish to change this listing in the future you will need this code exactly as shown as well as the player's ID:</p>
<table>
    <tr><td><b>Player ID</b></td><td><b>Code</b></td></tr>
    <tr>
     <td style="background-color:#FFDDDD"><?php echo $context->getId() ?></td>
     <td style="background-color:#FFDDDD"><?php echo $context->getCreatestamp() ?></td>
    </tr>
</table>
<?php echo $savant->render($context, 'SMGregsList/Frontend/Sell.tpl.php') ?>
