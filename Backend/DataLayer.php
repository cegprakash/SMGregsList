<?php
namespace SMGregsList\Backend;
use SMGregsList\Player,SMGregsList\WriteablePlayer,SMGregsList\SearchablePlayer,SMGregsList\Messager,SMGregsList\Manager;
abstract class DataLayer extends Messager
{
    abstract function exists(Player $player);
    abstract function checkManager(Player $player, Manager $manager);
    abstract function retrieve(Player $player);
    abstract function remove(Player $player);
    abstract function save(WriteablePlayer $player);
    abstract function search(SearchablePlayer $player);

    function listMessages(array $newmessages)
    {
        return parent::listMessages(array_merge($newmessages, array('addPlayer', 'deletePlayer', 'search', 'retrieve', 'exists',
                                                                    'existsmultiple', 'retrieveManager', 'checkDeleteAndExists')));
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
        } elseif ($message == 'checkDeleteAndExists') {
            // we reach here if viewing someone else's player.  Just make sure it isn't one of ours we listed and then sold
            if (!is_array($content) || !isset($content['player']) || !isset($content['manager'])
                || !($content['player'] instanceof Player) || !($content['manager'] instanceof Manager)) {
                throw new \Exception('Internal error: checkDeleteAndExists message received, but content was not an array(Player, Manager)');
            }
            $result = $this->checkManager($content['player'], $content['manager']);
            $this->broadcast('existsResult', $result);
        } elseif ($message == 'retrieveManager') {
            if (!($content instanceof Player)) {
                throw new \Exception('Internal error: retrieveManager message received, but content was not a Player object');
            }
            $result = $this->retrieveManager($content);
            $this->broadcast('managerRetrieved', $result);
        } elseif ($message == 'generateCode') {
        }
    }
}