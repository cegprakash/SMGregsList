<?php
namespace SMGregsList\Backend;
use SMGregsList\Player,SMGregsList\WriteablePlayer,SMGregsList\SearchablePlayer,SMGregsList\Messager;
abstract class DataLayer extends Messager
{
    abstract function exists(Player $player);
    abstract function retrieve(Player $player);
    abstract function remove(Player $player);
    abstract function save(WriteablePlayer $player);
    abstract function search(SearchablePlayer $player);

    function listMessages(array $newmessages)
    {
        return parent::listMessages(array_merge($newmessages, array('addPlayer', 'deletePlayer', 'search', 'retrieve', 'exists',
                                                                    'existsmultiple')));
    }

    function receive($message, $content)
    {
        if ($message == 'addPlayer') {
            if (!($content instanceof WriteablePlayer)) {
                throw new \Exception('Internal error: addPlayer message received, but content was not a WriteablePlayer object');
            }
            $result = $this->save($content);
            if (!$result instanceof WriteablePlayer) {
                throw new \Exception('Internal error: data driver did not return a WriteablePlayer object after saving');
            }
            $this->broadcast('playerAdded', $result);
        } elseif ($message == 'deletePlayer') {
            if (!($content instanceof WriteablePlayer)) {
                throw new \Exception('Internal error: deletePlayer message received, but content was not a WriteablePlayer object');
            }
            $result = $this->remove($content);
            if (!$result instanceof WriteablePlayer) {
                throw new \Exception('Internal error: data driver did not return a WriteablePlayer object after deleting');
            }
            $this->broadcast('playerRemoved', $result);
        } elseif ($message == 'search') {
            if (!($content instanceof SearchablePlayer)) {
                throw new \Exception('Internal error: search message received, but content was not a SearchablePlayer object');
            }
            $result = $this->search($content);
            $this->broadcast('searchResult', $result);
        } elseif ($message == 'retrieve') {
            if (!($content instanceof Player)) {
                throw new \Exception('Internal error: retrieve message received, but content was not a Player object');
            }
            $result = $this->retrieve($content);
            $this->broadcast('retrieved', $result);
        } elseif ($message == 'exists') {
            if (!($content instanceof Player)) {
                throw new \Exception('Internal error: retrieve message received, but content was not a Player object');
            }
            $this->broadcast('existsResult', $this->exists($content) ? true : false);
        } elseif ($message == 'existsmultiple') {
            $result = array();
            foreach ($content as $player) {
                if (!($player instanceof Player)) {
                    throw new \Exception('Internal error: retrieve message received, but content was not a Player object');
                }
                $result[$player->getId()] = $this->exists($player) ? true : false;
            }
            $this->broadcast('existsResult', $result);
        }
    }
}