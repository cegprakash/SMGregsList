    <tr><td><a href="<?php echo $context->getRawObject()->getUrl() . $context->getId() ?>" target="_blank" title="<?php
    echo "Owned by manager " . $context->getManager() ?>"><?php
    echo $context->getName() ?></a></td>
    <td><?php echo $context->getPosition() ?></td><td><?php echo $context->getAverage() ?></td>
    <td><?php echo $context->getAge() ?></td><td><?php echo $context->getCountry() ?></td>
    <td><?php echo $context->getForecast() ?></td><td><?php echo $context->getForecast() ?></td>
    </tr>
