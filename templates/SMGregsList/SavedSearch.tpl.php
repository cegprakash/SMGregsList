    <tr>
        <td>
            <?php echo $context->getCount() ?>
        </td>
        <td>
            <a href="/sm/index.php?executesearch=<?php echo $context->id ?>" title="Search for <?php
    echo $context->fullDescription() ?>"><?php echo $context->abridgedDescription() ?></a>
        </td>
        <td>
            <a class="btn btn-danger btn-mini" href="#" onclick="javascript:location.href='/sm/index.php?deletesearch=<?php echo $context->id ?>'" title="delete saved search">&times;</a>
        </td>
    </tr>