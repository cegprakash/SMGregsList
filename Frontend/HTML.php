<?php
namespace SMGregsList;
class HTML extends Messager
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
            $this->displaySearchResults($content);
        }
    }

    function displayMainPage()
    {
        
    }
}