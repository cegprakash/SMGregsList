    <?php if ($context->getRawObject() instanceof SMGregsList\Player) {
        echo $savant->render(array($context));
    }
    echo $savant->render($context->getRawObject()->getArrayCopy()); ?>
