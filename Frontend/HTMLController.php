<?php
namespace SMGregsList\Frontend;
use SMGregsList\Messager, SMGregsList\SearchPlayer, SMGregsList\SellPlayer;
class HTMLController extends Messager
{
    function listMessages(array $newmessages)
    {
        return parent::listMessages(array('detectSearch', 'detectSell'));
    }

    function receive($message, $content)
    {
        if ($message == 'detectSearch') {
            return $this->detectSearch();
        } elseif ($message == 'detectSell') {
            return $this->detectSell();
        }
    }

    function detectSell()
    {
        if (!isset($_POST) || !isset($_POST['id'])) {
            return;
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
        $player = new SellPlayer;
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
            $value = filter_var($_POST['average'], FILTER_SANITIZE_NUMBER_INT);
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
            $value = filter_var($_POST['experience'], FILTER_SANITIZE_NUMBER_FLOAT);
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
        if (!isset($_POST) || !isset($_POST['id'])) {
            $this->broadcast('searchResults', array());
            return;
        }
        $player = new SearchPlayer;
        if (isset($_POST['minage']) || isset($_POST['maxage'])) {
            if (!isset($_POST['minage']) || !$_POST['minage']) {
                $player->age = $_POST['maxage'];
            } elseif (!isset($_POST['maxage']) || !$_POST['maxage']) {
                $player->age = $_POST['minage'] . '-99';
            } else {
                $player->age = str_replace('-', '', $_POST['minage']) . '-' . str_replace('-', '', $_POST['maxage']);
            }
        }
        if (isset($_POST['minaverage']) || isset($_POST['maxaverage'])) {
            if (!isset($_POST['minaverage']) || !$_POST['minaverage']) {
                $player->average = $_POST['maxaverage'];
            } elseif (!isset($_POST['maxaverage']) || !$_POST['maxaverage']) {
                $player->average = $_POST['minaverage'] . '-99';
            } else {
                $player->average = str_replace('-', '', $_POST['minaverage']) . '-' . str_replace('-', '', $_POST['maxaverage']);
            }
        }
        if (isset($_POST['position'])) {
            if (is_array($_POST['position'])) {
                $player->position = implode(',', $_POST['position']);
            }
        }
        if (isset($_POST['forecast']) && $_POST['forecast']) {
            $player->forecast = $_POST['forecast'];
        }
        if (isset($_POST['experience']) && $_POST['experience']) {
            $player->forecast = $_POST['experience'];
        }
        if (isset($_POST['progression']) && $_POST['progression']) {
            $player->progression = $_POST['progression'];
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
        $this->broadcast('search', $player);
    }
}