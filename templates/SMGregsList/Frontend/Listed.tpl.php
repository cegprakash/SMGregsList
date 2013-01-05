<div class="alert alert-success">
<strong>Thank you, your player has been placed for sale</strong>
<p>To update this listing you will need all three of the following:</p>
<table class="table">
    <tr><th>Player ID</th><th>Code</th><th>Manager</th></tr>
    <tr>
     <td><?php echo $context->getId() ?></td>
     <td><?php echo $context->getCode() ?></td>
     <td><?php echo $context->getManager()->getName() ?></td>
    </tr>
</table>
</div>
<?php echo $savant->render($context, 'SMGregsList/Frontend/Sell.tpl.php') ?>
