<?php
namespace SMGregsList\Frontend;
use SMGregsList\Messager, SMGregsList\SearchPlayer, SMGregsList\Player, SMGregsList\SellPlayer;

class Json extends HTML
{
    function __construct(Json\Controller $controller = null)
    {
        if (null === $controller) {
            $controller = new Json\Controller;
        }
        parent::__construct($controller);
    }

    function listMessages(array $newmessages)
    {
        return parent::listMessages(array_merge($newmessages, array('existsResult', 'managerRetrievedFromName')));
    }

    function receive($message, $content)
    {
        if ($message == 'ready') {
            $this->broadcast('parseJson');
        } elseif ($message == 'searchResult') {
            if (!is_array($content)) {
                throw new \Exception('internal Error: results of search is not an array');
            }
            foreach ($content as $player) {
                if (!($player instanceof Player)) {
                    throw new \Exception('internal Error: one of the results of search is not a Player object');
                }
            }
            $this->searchresults = $content;
            $this->broadcast('reply', array('message' => 'searchResult',
                                            'params' => array('players' => $this->toJsonContent($content))));
        } elseif ($message == 'existsResult') {
            $this->broadcast('reply', array('message' => 'existsResult',
                                            'params' => array('exists' => $content)));
        } elseif ($message == 'playerAdded') {
            $this->broadcast('reply', array('message' => 'playerAdded',
                                            'params' => array('id' => $content->getId(), 'code' => $content->getCode(),
                                                              'manager' => $content->getManager()->getName())));
        } elseif ($message == 'playerRemoved') {
            $this->broadcast('reply', array('message' => 'playerRemoved',
                                            'params' => array('id' => $content->getId())));
        } elseif ($message = 'managerRetrievedFromName') {
            $this->broadcast('reply', array('message' => 'manager',
                                            'params' => array('manager' => $content->getName(), 'code' => $content->getCode())));
        }
    }

    function toJsonContent(array $players) {
        $ret = array();
        foreach ($players as $player) {
            $ret[] = array(
                'id' => $player->getId(),
                'average' => $player->getAverage(),
                'experience' => $player->getExperience(),
                'age' => $player->getAge(),
                'position' => $player->getPosition()
            );
        }
        return $ret;
    }
}