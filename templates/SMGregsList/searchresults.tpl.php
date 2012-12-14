    <?php if ($context->getRawObject() instanceof SMGregsList\Player) {
        $context = new ArrayObject(array($context));
    }
    echo $savant->render($context->getRawObject()->getArrayCopy()); ?>
