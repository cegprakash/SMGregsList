<?php
namespace SMGregsList\Frontend;
use SMGregsList\Messager, SMGregsList\Frontend;
class HTML extends Messager implements Frontend
{
    protected $controller;

    function __construct()
    {
        $this->controller = new HTMLController;
    }

    function listMessages()
    {
        return parent::listMessages(array('ready', 'searchResults'));
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
        } elseif ($message == 'attach') {
            $this->controller->attach($this->controllers[count($this->controllers) - 1]);
        }
    }

    function displayMainPage()
    {
        $this->displaySearchForm();
        $this->discoverSearch();
    }

    function discoverSearch()
    {
        $this->broadcast('detectSearch');
    }

    function displaySearchForm()
    {
        echo "<p>Search Form</p>\n";
    }

    function displaySearchResults(array $results)
    {
        if (!count($results)) {
            echo "<p><strong>No results</strong></p>\n";
        }
    }
}