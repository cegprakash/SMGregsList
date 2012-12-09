<?php
namespace SMGregsList\Frontend;
use SMGregsList\Messager, SMGregsList\SearchPlayer;
class HTMLController extends Messager
{
    function listMessages()
    {
        return parent::listMessages(array('detectSearch'));
    }

    function receive($message, $content)
    {
        if ($message == 'detectSearch') {
            return $this->detectSearch();
        }
    }

    function detectSearch()
    {
        if (!isset($_POST) || !isset($_POST['search'])) {
            $this->broadcast('searchResults', array());
            return;
        }
        $player = new SearchPlayer;
        if (isset($_POST['minage']) || isset($_POST['maxage'])) {
            if (!isset($_POST['minage'])) {
                $player->age = $_POST['maxage'];
            } elseif (!isset($_POST['maxage'])) {
                $player->age = $_POST['minage'] . '-99';
            } else {
                $player->age = str_replace('-', '', $_POST['minage']) . '-' . str_replace('-', '', $_POST['maxage']);
            }
        }
        if (isset($_POST['minaverage']) || isset($_POST['maxaverage'])) {
            if (!isset($_POST['minaverage'])) {
                $player->average = $_POST['maxaverage'];
            } elseif (!isset($_POST['maxaverage'])) {
                $player->average = $_POST['minaverage'] . '-99';
            } else {
                $player->average = str_replace('-', '', $_POST['minaverage']) . '-' . str_replace('-', '', $_POST['maxaverage']);
            }
        }
        if (isset($_POST['positions'])) {
            $player->position = $_POST['positions'];
        }
        if (isset($_POST['forecast'])) {
            $player->forecast = $_POST['forecast'];
        }
        if (isset($_POST['progression'])) {
            $player->forecast = $_POST['progression'];
        }
        if (isset($_POST['skills'])) {
            
        }
        if (isset($_POST['stats'])) {
            
        }
        $this->broadcast('search', $player);
    }
}