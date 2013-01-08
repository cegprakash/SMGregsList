<?php
namespace SMGregsList\Backend;
use SMGregsList\Player,SMGregsList\WriteablePlayer,SMGregsList\SearchablePlayer,SMGregsList\Messager,SMGregsList\Manager;
abstract class DataLayer extends Messager
{
    abstract function managerExists($name);
    abstract function exists(Player $player);
    abstract function checkManager(Player $player, Manager $manager);
    abstract function retrieve(Player $player);
    abstract function remove(Player $player);
    abstract function save(WriteablePlayer $player);
    abstract function search(SearchablePlayer $player);
    abstract function savesearch(SearchablePlayer $player);
    abstract function retrieveSavedSearch($manager);
    abstract function deleteSearch($id);
    abstract function executeSearch($id);
    abstract function findNewPlayers($manager, $code);

    function listMessages(array $newmessages)
    {
        return parent::listMessages(array_merge($newmessages, array('addPlayer', 'deletePlayer', 'search', 'retrieve', 'exists',
                                                                    'existsmultiple', 'retrieveManager', 'checkDeleteAndExists',
                                                                    'retrieveManagerFromName', 'savesearch', 'managerExists',
                                                                    'getAllSavedSearches', 'deleteSearch', 'executeSearch',
                                                                    'newMatches')));
    }

    function reply($message, $content)
    {
        if ($message == 'managerExists') {
            if (!is_string($content)) {
                throw new \Exception('Internal error: managerExists query received, but content was not a string');
            }
            return $this->managerExists($content);
        } elseif ($message == 'exists') {
            if (!($content instanceof Player)) {
                throw new \Exception('Internal error: retrieve query received, but content was not a Player object');
            }
            return $this->exists($content) ? true : false;
        } elseif ($message == 'retrieveManager') {
            if (!($content instanceof Player)) {
                throw new \Exception('Internal error: retrieveManager query received, but content was not a Player object');
            }
            return $this->retrieveManager($content);
        } elseif ($message == 'getAllSavedSearches') {
            if (!($content instanceof Manager)) {
                throw new \Exception('Internal error: getAllSavedSearches query received, but content was not a Manager object');
            }
            return $this->retrieveSavedSearch($content);
        } elseif ($message == 'newMatches') {
            if (!is_array($content)) {
                throw new \Exception('Internal error: newMatches query received, but content was not an array');
            }
            return $this->findNewPlayers($content['manager'], $content['code']);
        }
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
            if (isset($content['manager']) && isset($content['code'])) {
                $manager = $content['manager'];
                unset($content['manager']);
                $code = $content['code'];
                unset($content['code']);
                $manager = $this->retrieveManagerFromName($manager);
                if ($manager->getCode() != $code) {
                    // we don't check out, don't remove old listings
                    $manager = null;
                }
            }
            
            foreach ($content as $player) {
                if (!($player instanceof Player)) {
                    throw new \Exception('Internal error: retrieve message received, but content was not a Player object');
                }
                if (isset($manager)) {
                    $this->removeOldListings($manager, $player);
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
            $this->broadcast('existsResult', $result ? true : false);
        } elseif ($message == 'retrieveManager') {
            if (!($content instanceof Player)) {
                throw new \Exception('Internal error: retrieveManager message received, but content was not a Player object');
            }
            $result = $this->retrieveManager($content);
            $this->broadcast('managerRetrieved', $result);
        } elseif ($message == 'retrieveManagerFromName') {
            if (!is_string($content)) {
                throw new \Exception('Internal error: retrieveManagerFromName message received, but content was not a string');
            }
            $result = $this->retrieveManagerFromName($content);
            $this->broadcast('managerRetrievedFromName', $result);
        } elseif ($message == 'savesearch') {
            if (!($content instanceof SearchablePlayer)) {
                throw new \Exception('Internal error: search message received, but content was not a SearchablePlayer object');
            }
            $result = $this->savesearch($content);
            $this->broadcast('searchSaved', $result);
        } elseif ($message == 'deleteSearch') {
            if (!is_string($content)) {
                throw new \Exception('Internal error: deleteSearch message received, but content was not a string');
            }
            $this->deleteSearch($content);
            $this->broadcast('searchDeleted', true);
        } elseif ($message == 'executeSearch') {
            if (!is_string($content)) {
                throw new \Exception('Internal error: deleteSearch message received, but content was not a string');
            }
            $result = $this->executeSearch($content);
            $this->broadcast('searchResult', $result);
        }
    }
}