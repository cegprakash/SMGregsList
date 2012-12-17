<?php
namespace SMGregsList\Frontend;
use SMGregsList\Messager, SMGregsList\SearchPlayer, SMGregsList\Player, SMGregsList\SellPlayer;
class HTMLController extends Messager
{
    protected $retrieved;
    function __construct()
    {
        set_exception_handler(function ($e) {
            ?><h1>Error</h1>
        <p><?php echo $e->getMessage() ?></p>
        <a href="javascript:history.go(-1)"><< Return</a>
        <p><?php if ($_SERVER['HTTP_HOST'] == 'localhost'): ?>
        <pre>
            <?php throw $e; ?>
        </pre>
        <?php endif ?></p>
        
        <?php
        });
    }

    function listMessages(array $newmessages)
    {
        return parent::listMessages(array('detectSearch', 'detectSell', 'retrieved'));
    }

    function getParams($type)
    {
        if ($type == 'sell') {
            if (!isset($_POST) || !isset($_POST['id'])) {
                return false;
            }
            return $_POST;
        } else {
            return $_GET;
        }
    }

    function getMessage($type)
    {
        $params = $this->getParams($type);
        if (!$params) {
            return 'sellform';
        }
        if (isset($params['delete'])) {
            return 'delete';
        }
        if (isset($params['retrieve'])) {
            return 'retrieve';
        }
        if (!isset($params['verifytoken'])) {
            if (!isset($params['cancel'])) {
                return 'verify';
            }
        }
        if (isset($params['cancel'])) {
            unset($_POST['verifytoken']);
            return 'cancel';
        }
        if (isset($params['sellfinal'])) {
            return 'confirm';
        }
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
        $params = $this->getParams('sell');
        if ($this->getMessage('sell') == 'delete' && isset($params['code'])) {
            // find the player
            $player = new SellPlayer;
            if (isset($params['id']) && $params['id']) {
                if (is_numeric($params['id']) && $params['id'] == (int) $params['id']) {
                    $player->id = (int) $params['id'];
                } elseif (preg_match('/id_jugador(?:=|%3[dD])([0-9]+)/', $params['id'], $matches)) {
                    $player->id = (int) $matches[1];
                }
            }
            $player->code = $params['code'];
            $this->broadcast('deletePlayer', $player);
            return;
        }
        if ($this->getMessage('sell') == 'retrieve' && isset($params['code'])) {
            // find the player
            $player = new SellPlayer;
            if (isset($params['id']) && $params['id']) {
                if (is_numeric($params['id']) && $params['id'] == (int) $params['id']) {
                    $player->id = (int) $params['id'];
                } elseif (preg_match('/id_jugador(?:=|%3[dD])([0-9]+)/', $params['id'], $matches)) {
                    $player->id = (int) $matches[1];
                }
            }
            $player->code = $params['code'];
            $this->broadcast('retrieve', $player);
            $this->broadcast('sellDetected', $this->retrieved);
            return;
        }
        if (isset($params['code'])) {
            // find the player and verify the edit code before continuing
            $player = new SellPlayer;
            if (isset($params['id']) && $params['id']) {
                if (is_numeric($params['id']) && $params['id'] == (int) $params['id']) {
                    $player->id = (int) $params['id'];
                } elseif (preg_match('/id_jugador(?:=|%3[dD])([0-9]+)/', $params['id'], $matches)) {
                    $player->id = (int) $matches[1];
                }
            }
            $player->code = $params['code'];
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
        if ($this->getMessage('sell') == 'verify') {
            $this->broadcast('verify');
        } elseif ($this->getMessage('sell') == 'confirm') {
            $this->broadcast('confirm');
        }
        if (isset($params['id']) && $params['id']) {
            if (is_numeric($params['id']) && $params['id'] == (int) $params['id']) {
                $player->id = (int) $params['id'];
            } elseif (preg_match('/id_jugador(?:=|%3[dD])([0-9]+)/', $params['id'], $matches)) {
                $player->id = (int) $matches[1];
            }
        }
        if (isset($params['age']) && $params['age']) {
            $value = filter_var($params['age'], FILTER_SANITIZE_NUMBER_INT);
            if ($value > 13 && $value < 40) {
                $player->age = $value;
            }
        }
        if (isset($params['average']) && $params['average']) {
            $value = filter_var($params['average'], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_ALLOW_FRACTION);
            if ($value > 0 && $value < 100) {
                $player->average = $value;
            }
        }
        if (isset($params['position']) && $params['position']) {
            $player->position = $params['position'];
        }
        if (isset($params['forecast']) && $params['forecast']) {
            $value = filter_var($params['forecast'], FILTER_SANITIZE_NUMBER_INT);
            if ($value > 0 && $value < 100) {
                $player->forecast = $value;
            }
        }
        if (isset($params['progression']) && $params['progression']) {
            $value = filter_var($params['progression'], FILTER_SANITIZE_NUMBER_INT);
            if ($value > 0 && $value < 100) {
                $player->progression = $value;
            }
        }
        if (isset($params['experience']) && $params['experience']) {
            $value = filter_var($params['experience'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $player->experience = $value;
        }
        if (isset($params['skills'])) {
            if (is_array($params['skills'])) {
                foreach ($params['skills'] as $name => $amount) {
                    $player->getSkills()->$name = $amount; 
                }
            }
        }
        if (isset($params['stats'])) {
            if (is_array($params['stats'])) {
                foreach ($params['stats'] as $name => $amount) {
                    $player->getStats()->$name = $amount; 
                }
            }
        }
        $this->broadcast('sellDetected', $player);
        if ($this->getMessage('sell') == 'confirm') {
            $this->broadcast('addPlayer', $player);
        }
    }

    function detectSearch()
    {
        $params = $this->getParams('search');
        if (!isset($params) || !isset($params['id'])) {
            $this->broadcast('searchResults', array());
            return;
        }
        $player = new SearchPlayer;
        if (isset($params['minage']) || isset($params['maxage'])) {
            if (!isset($params['minage']) || !$params['minage']) {
                $player->age = $params['maxage'];
            } elseif (!isset($params['maxage']) || !$params['maxage']) {
                $player->age = $params['minage'] . '-99';
            } else {
                $player->age = str_replace('-', '', $params['minage']) . '-' . str_replace('-', '', $params['maxage']);
            }
        }
        if (isset($params['minaverage']) || isset($params['maxaverage'])) {
            if (!isset($params['minaverage']) || !$params['minaverage']) {
                $player->average = $params['maxaverage'];
            } elseif (!isset($params['maxaverage']) || !$params['maxaverage']) {
                $player->average = $params['minaverage'] . '-99';
            } else {
                $player->average = str_replace('-', '', $params['minaverage']) . '-' . str_replace('-', '', $params['maxaverage']);
            }
        }
        if (isset($params['position'])) {
            if (is_array($params['position'])) {
                $player->position = implode(',', $params['position']);
            }
        }
        if (isset($params['forecast']) && $params['forecast']) {
            $player->forecast = $params['forecast'];
        }
        if (isset($params['experience']) && $params['experience']) {
            $player->forecast = $params['experience'];
        }
        if (isset($params['progression']) && $params['progression']) {
            $player->progression = $params['progression'];
        }
        if (isset($params['skills'])) {
            if (is_array($params['skills'])) {
                foreach ($params['skills'] as $name => $amount) {
                    $player->getSkills()->$name = $amount; 
                }
            }
        }
        if (isset($params['stats'])) {
            if (is_array($params['stats'])) {
                foreach ($params['stats'] as $name => $amount) {
                    $player->getStats()->$name = $amount; 
                }
            }
        }
        $this->broadcast('search', $player);
    }
}