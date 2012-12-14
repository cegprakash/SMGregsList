<?php
namespace SMGregsList\Frontend;
use SMGregsList\Messager, SMGregsList\SearchPlayer, SMGregsList\Player, SMGregsList\SellPlayer;
class HTMLController extends Messager
{
    protected $retrieved;
    function listMessages(array $newmessages)
    {
        return parent::listMessages(array('detectSearch', 'detectSell', 'retrieved'));
    }

    function receive($message, $content)
    {
        if ($message == 'detectSearch') {
            return $this->detectSearch();
        } elseif ($message == 'detectSell') {
            return $this->detectSell();
        } elseif ($message == 'retrieved') {
            $this->retrieved = $content;
        }
    }
    
    function detectSell()
    {
        if (!isset($_POST) || !isset($_POST['id'])) {
            return;
        }
        if (isset($_POST['delete']) && isset($_POST['code'])) {
            // find the player
            $player = new SellPlayer;
            if (isset($_POST['id']) && $_POST['id']) {
                if (is_numeric($_POST['id']) && $_POST['id'] == (int) $_POST['id']) {
                    $player->id = (int) $_POST['id'];
                } elseif (preg_match('/id_jugador(?:=|%3[dD])([0-9]+)/', $_POST['id'], $matches)) {
                    $player->id = (int) $matches[1];
                }
            }
            $player->code = $_POST['code'];
            $this->broadcast('deletePlayer', $player);
            return;
        }
        if (isset($_POST['retrieve']) && isset($_POST['code'])) {
            // find the player
            $player = new SellPlayer;
            if (isset($_POST['id']) && $_POST['id']) {
                if (is_numeric($_POST['id']) && $_POST['id'] == (int) $_POST['id']) {
                    $player->id = (int) $_POST['id'];
                } elseif (preg_match('/id_jugador(?:=|%3[dD])([0-9]+)/', $_POST['id'], $matches)) {
                    $player->id = (int) $matches[1];
                }
            }
            $player->code = $_POST['code'];
            $this->broadcast('retrieve', $player);
            $this->broadcast('sellDetected', $this->retrieved);
            return;
        }
        if (isset($_POST['code'])) {
            // find the player and verify the edit code before continuing
            $player = new SellPlayer;
            if (isset($_POST['id']) && $_POST['id']) {
                if (is_numeric($_POST['id']) && $_POST['id'] == (int) $_POST['id']) {
                    $player->id = (int) $_POST['id'];
                } elseif (preg_match('/id_jugador(?:=|%3[dD])([0-9]+)/', $_POST['id'], $matches)) {
                    $player->id = (int) $matches[1];
                }
            }
            $player->code = $_POST['code'];
            try {
                $this->broadcast('retrieve', $player);
                // if we get to here, the code matched
            } catch (\Exception $e) {
                if ($e->getCode() == -2) {
                    throw $e; // code did not match
                }
            }
        } else {
            $player = new SellPlayer;
        }
        if (!isset($_POST['verifytoken'])) {
            if (!isset($_POST['cancel'])) {
                $this->broadcast('verify');
            }
        } else {
            if (isset($_POST['cancel'])) {
                unset($_POST['verifytoken']);
            } elseif (isset($_POST['sellfinal'])) {
                $this->broadcast('confirm');
            }
        }
        if (isset($_POST['id']) && $_POST['id']) {
            if (is_numeric($_POST['id']) && $_POST['id'] == (int) $_POST['id']) {
                $player->id = (int) $_POST['id'];
            } elseif (preg_match('/id_jugador(?:=|%3[dD])([0-9]+)/', $_POST['id'], $matches)) {
                $player->id = (int) $matches[1];
            }
        }
        if (isset($_POST['age']) && $_POST['age']) {
            $value = filter_var($_POST['age'], FILTER_SANITIZE_NUMBER_INT);
            if ($value > 13 && $value < 40) {
                $player->age = $value;
            }
        }
        if (isset($_POST['average']) && $_POST['average']) {
            $value = filter_var($_POST['average'], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_ALLOW_FRACTION);
            if ($value > 0 && $value < 100) {
                $player->average = $value;
            }
        }
        if (isset($_POST['position']) && $_POST['position']) {
            $player->position = $_POST['position'];
        }
        if (isset($_POST['forecast']) && $_POST['forecast']) {
            $value = filter_var($_POST['forecast'], FILTER_SANITIZE_NUMBER_INT);
            if ($value > 0 && $value < 100) {
                $player->forecast = $value;
            }
        }
        if (isset($_POST['progression']) && $_POST['progression']) {
            $value = filter_var($_POST['progression'], FILTER_SANITIZE_NUMBER_INT);
            if ($value > 0 && $value < 100) {
                $player->progression = $value;
            }
        }
        if (isset($_POST['experience']) && $_POST['experience']) {
            $value = filter_var($_POST['experience'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $player->experience = $value;
        }
        if (isset($_POST['skills'])) {
            if (is_array($_POST['skills'])) {
                foreach ($_POST['skills'] as $name => $amount) {
                    $player->getSkills()->$name = $amount; 
                }
            }
        }
        if (isset($_POST['stats'])) {
            if (is_array($_POST['stats'])) {
                foreach ($_POST['stats'] as $name => $amount) {
                    $player->getStats()->$name = $amount; 
                }
            }
        }
        $this->broadcast('sellDetected', $player);
        if (isset($_POST['sellfinal'])) {
            $this->broadcast('addPlayer', $player);
        }
    }

    function detectSearch()
    {
        if (!isset($_GET) || !isset($_GET['id'])) {
            $this->broadcast('searchResults', array());
            return;
        }
        $player = new SearchPlayer;
        if (isset($_GET['minage']) || isset($_GET['maxage'])) {
            if (!isset($_GET['minage']) || !$_GET['minage']) {
                $player->age = $_GET['maxage'];
            } elseif (!isset($_GET['maxage']) || !$_GET['maxage']) {
                $player->age = $_GET['minage'] . '-99';
            } else {
                $player->age = str_replace('-', '', $_GET['minage']) . '-' . str_replace('-', '', $_GET['maxage']);
            }
        }
        if (isset($_GET['minaverage']) || isset($_GET['maxaverage'])) {
            if (!isset($_GET['minaverage']) || !$_GET['minaverage']) {
                $player->average = $_GET['maxaverage'];
            } elseif (!isset($_GET['maxaverage']) || !$_GET['maxaverage']) {
                $player->average = $_GET['minaverage'] . '-99';
            } else {
                $player->average = str_replace('-', '', $_GET['minaverage']) . '-' . str_replace('-', '', $_GET['maxaverage']);
            }
        }
        if (isset($_GET['position'])) {
            if (is_array($_GET['position'])) {
                $player->position = implode(',', $_GET['position']);
            }
        }
        if (isset($_GET['forecast']) && $_GET['forecast']) {
            $player->forecast = $_GET['forecast'];
        }
        if (isset($_GET['experience']) && $_GET['experience']) {
            $player->forecast = $_GET['experience'];
        }
        if (isset($_GET['progression']) && $_GET['progression']) {
            $player->progression = $_GET['progression'];
        }
        if (isset($_GET['skills'])) {
            if (is_array($_GET['skills'])) {
                foreach ($_GET['skills'] as $name => $amount) {
                    $player->getSkills()->$name = $amount; 
                }
            }
        }
        if (isset($_GET['stats'])) {
            if (is_array($_GET['stats'])) {
                foreach ($_GET['stats'] as $name => $amount) {
                    $player->getStats()->$name = $amount; 
                }
            }
        }
        $this->broadcast('search', $player);
    }
}