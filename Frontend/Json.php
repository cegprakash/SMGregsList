<?php
namespace SMGregsList\Frontend;
use SMGregsList\Player;

class Json extends Messager implements Frontend
{
    function __construct(Json\Controller $controller = null)
    {
        if (null === $controller) {
            $controller = new HTMLController;
        }
        $this->searchfor = new SearchPlayer;
        $this->sellplayer = new SellPlayer;
        $this->addDependency($controller);
        $this->template = new \PEAR2\Templates\Savant\Main(array(
            'template_path' => realpath(__DIR__ . '/../templates'),
            'escape' => 'htmlentities',
        ));
    }

    function listMessages(array $newmessages)
    {
        return parent::listMessages(array('ready', 'searchResult', 'search', 'playerAdded', 'sellDetected',
                                          'verify', 'confirm', 'playerRemoved'));
    }
}