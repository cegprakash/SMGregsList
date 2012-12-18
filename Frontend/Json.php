<?php
namespace SMGregsList\Frontend;
use SMGregsList\Player;

class Json extends HTML
{
    function __construct(Json\Controller $controller = null)
    {
        if (null === $controller) {
            $controller = new Json\Controller;
        }
        $this->searchfor = new SearchPlayer;
        $this->sellplayer = new SellPlayer;
        $this->addDependency($controller);
        $this->template = new \PEAR2\Templates\Savant\Main(array(
            'template_path' => realpath(__DIR__ . '/../templates'),
            'escape' => 'htmlentities',
        ));
    }

    function receive($message, $content)
    {
        if ($message == 'ready') {
            $this->discoverSell();
            $this->discoverSearch();
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
        } elseif ($message == 'search') {
            $this->searchfor = $content;
        } elseif ($message == 'playerAdded' || $message == 'sellDetected') {
            $this->broadcast('reply', array('message' => 'playerAdded',
                                            'params' => array('id' => $content->id, 'code' => $content->code)));
        } elseif ($message == 'playerRemoved') {
            $this->broadcast('reply', array('message' => 'playerRemoved',
                                            'params' => array('id' => $content->id)));
        }
    }

    function toJsonContent(array $players) {
        $ret = array();
        foreach ($players as $player) {
            $ret[] = array(
                'id' => $player->id,
                'average' => $player->average,
                'experience' => $player->experience,
                'age' => $player->age,
                'position' => $player->position
            );
        }
        return $ret;
    }
}