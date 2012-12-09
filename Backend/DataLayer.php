<?php
namespace SMGregsList;
abstract class DataLayer extends Messager
{
    abstract function exists(Player $player);
    abstract function retrieve(Player $player);
    abstract function remove(Player $player);
    abstract function store(WriteablePlayer $player);
    abstract function search(SearchablePlayer $player);

    function listMessages()
    {
        return array('addPlayer', 'deletePlayer', 'search');
    }

    function receive($message, $content)
    {
        if ($message == 'addPlayer') {
            if (!($content instanceof WriteablePlayer)) {
                throw new \Exception('Internal error: addPlayer message received, but content was not a WriteablePlayer object');
            }
            $this->store($content);
        } elseif ($message == 'deletePlayer') {
            if (get_class($content) !== __NAMESPACE__ . '\\Player') {
                throw new \Exception('Internal error: addPlayer message received, but content was not a WriteablePlayer object');
            }
            $this->remove($content);
        } elseif ($message == 'search') {
            if (!($content instanceof SearchablePlayer)) {
                throw new \Exception('Internal error: search message received, but content was not a SearchablePlayer object');
            }
            $result = $this->search($player);
            $this->broadcast('searchResult', $result);
        }
    }
}