<?php
namespace SMGregsList\Frontend;
use SMGregsList\Messager, SMGregsList\Frontend;
class HTML extends Messager implements Frontend
{
    function listMessages()
    {
        return array('ready', 'searchResults');
    }

    function receive($message, $content)
    {
        if ($message == 'ready') {
            // display html output
            $this->displayMainPage();
        } elseif ($message == 'searchResults') {
            if (!is_array($content)) {
                throw new \Exception('internal Error: results of search is not an array');
            }
            foreach ($content as $player) {
                if (!($player instanceof Player)) {
                    throw new \Exception('internal Error: one of the results of search is not a Player object');
                }
            }
            $this->displaySearchResults($content);
        }
    }

    function displayMainPage()
    {
        
    }

    function displaySearchResults(array $results)
    {
        
    }
}