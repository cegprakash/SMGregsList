<?php
namespace SMGregsList\Frontend;
use SMGregsList\Messager, SMGregsList\SearchPlayer, SMGregsList\Player, SMGregsList\SellPlayer, SMGregsList\Manager,
    SMGregsList\Backend\DataLayer\SMGrabber;
class HTMLController extends Messager
{
    protected $retrieved;
    protected $remote;
    protected $manager = false;
    protected $code = false;
    protected $state = 'normal';
    protected $existsResult = false;
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
        self::serverPrefix();
        $this->remote = new SMGrabber;
    }

    static function showSell()
    {
        if (strpos($_SERVER['PHP_SELF'], '/nosell')) {
            return false;
        }
        return true;
    }

    static function serverPrefix()
    {
        static $started = false;
        if (!$started) {
            session_start();
            $started = true;
        }
        if (strpos($_SERVER['PHP_SELF'], '/en1/')) {
            $_SESSION['server'] = 'en1';
            return 'en1';
        }
        if (strpos($_SERVER['PHP_SELF'], '/en2/')) {
            $_SESSION['server'] = 'en2';
            return 'en2';
        }
        if (strpos($_SERVER['PHP_SELF'], '/en/')) {
            $_SESSION['server'] = 'en';
            return 'en';
        }
        if (isset($_SESSION['server'])) {
            return $_SESSION['server'];
        }
        return 'en3';
    }

    function listMessages(array $newmessages)
    {
        return parent::listMessages(array_merge($newmessages, array('detectSearch', 'detectSell', 'retrieved', 'managerRetrieved', 'cookieRetrieved',
                                                                    'existsResult')));
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
        if (isset($params['deletesearch'])) {
            return 'deletesearch';
        }
        if (isset($params['executesearch'])) {
            return 'executesearch';
        }
        if (isset($params['savesearch'])) {
            return 'savesearch';
        }
        if (isset($params['searchbutton'])) {
            return 'search';
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
            $this->broadcast('retrieveCookie');
            return $this->detectSell();
        } elseif ($message == 'retrieved') {
            $this->retrieved = $content;
        } elseif ($message == 'managerRetrieved') {
            $this->retrieved->manager = $content;
        } elseif ($message == 'cookieRetrieved') {
            $this->manager = $content['manager'];
            $this->code = $content['code'];
        } elseif ($message == 'existsResult' && $this->state = 'existsCheck') {
            $this->existsResult = $content;
        }
    }

    function detectSell()
    {
        if ($this->getMessage('search') == 'search') {
            return;
        }
        $params = $this->getParams('sell');
        $player = new SellPlayer;
        // find the player
        if (isset($params['id']) && $params['id']) {
            if (is_numeric($params['id']) && $params['id'] == (int) $params['id']) {
                $player->id = (int) $params['id'];
            } elseif (preg_match('/id_jugador(?:=|%3[dD])([0-9]+)/', $params['id'], $matches)) {
                $player->id = (int) $matches[1];
            }
        }
        if (!isset($params['manager'])) {
            $this->broadcast('retrieveCookie');
            if ($this->manager) {
                $params['manager'] = $this->manager;
                $params['code'] = $this->code;
            }
        }
        if (isset($params['manager']) && $params['manager']) {
            $player->manager = $params['manager'];
        }
        $this->retrieved = $player;
        try {
            $this->broadcast('retrieveManager', $player);
        } catch (\Exception $e) {
            if ($this->getMessage('sell') == 'confirm' || $this->getMessage('sell') == 'verify') {
                throw $e;
            } else {
                $player->manager = new Manager;
            }
        }
        if ($this->getMessage('sell') == 'delete' && isset($params['code'])) {
            $player->code = $params['code'];
            $this->broadcast('deletePlayer', $player);
            return;
        }
        if ($this->getMessage('sell') == 'retrieve') {
            $testplayer = $this->remote->retrieve($player);
            if (isset($params['code']) && $params['code']) {
                $player->code = $params['code'];
                $this->broadcast('retrieve', $player);
            }
            $params = $testplayer->stackPOST();
            $this->broadcast('sellDetected', $this->retrieved);
            $this->broadcast('verify');
        } elseif (isset($params['code'])) {
            $player->code = $params['code'];
            try {
                if ($this->ask('exists', $player)) {
                    $this->broadcast('retrieve', $player);
                }
                // if we get to here, the code matched
            } catch (\Exception $e) {
                if ($e->getCode() == -2) {
                    throw $e; // code did not match
                }
            }
        }
        if ($this->getMessage('sell') == 'verify') {
            $this->broadcast('verify');
        } elseif ($this->getMessage('sell') == 'confirm') {
            $this->broadcast('confirm');
        }
        if (isset($params['age']) && $params['age']) {
            $value = filter_var($params['age'], FILTER_SANITIZE_NUMBER_INT);
            if ($value > 15 && $value < 40) {
                $player->age = $value;
            }
            if ($value > 13 && $value < 16 && isset($params['isyouth']) && $params['isyouth']) {
                throw new \Exception("Juniors younger than 16 cannot be sold on the transfer market");
            }
        }
        if (isset($params['average']) && $params['average']) {
            $value = filter_var($params['average'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            if ($value > 0 && $value < 100) {
                $player->average = $value;
            }
        } elseif ($this->getMessage('sell') == 'verify' || $this->getMessage('sell') == 'confirm') {
            throw new \Exception('Error: Player average must be set');
        }
        if (isset($params['position']) && $params['position']) {
            $player->position = $params['position'];
        }
        if (isset($params['country']) && $params['country']) {
            $player->country = $params['country'];
        }
        if (isset($params['name']) && $params['name']) {
            $player->name = trim($params['name']);
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
        //throw new \Exception($params['forecast']);
        //$player->showme();
        $this->broadcast('sellDetected', $player);
        if ($this->getMessage('sell') == 'confirm') {
            $this->broadcast('addPlayer', $player);
        }
    }

    function detectSearch()
    {
        if ($this->getMessage('search') == 'deletesearch') {
            $params = $this->getParams('search');
            $_GET = array();
            $this->broadcast('deleteSearch', $params['deletesearch']);
            return;
        }
        if ($this->getMessage('search') == 'executesearch') {
            $params = $this->getParams('search');
            $this->broadcast('executeSearch', $params['executesearch']);
            return;
        }
        if ($this->getMessage('search') !== 'search' && $this->getMessage('search') !== 'savesearch') {
            return;
        }
        $params = $this->getParams('search');
        if (!isset($params) || !isset($params['id'])) {
            $this->broadcast('searchResults', array());
            return;
        }
        if (!isset($params['managername']) || !$params['managername']) {
            $this->broadcast('retrieveCookie');
            if ($this->manager) {
                $params['managername'] = $this->manager;
                $params['managercode'] = $this->code;
            }
        }
        $player = new SearchPlayer;
        if ($this->getMessage('search') == 'savesearch' && isset($params['managername']) && $params['managername']) {
            if ($code = $this->ask('managerExists', $params['managername'])) {
                if (!isset($params['managercode']) || !$params['managercode']) {
                    throw new \Exception("Error: Manager code must be set to save a search.  If you have lost your code, please " .
                                         "send a message to CelloG from within Striker Manager asking him to retrieve it");
                }
                if ($code != $params['managercode']) {
                    throw new \Exception("Error: Cannot save search, code does not match.  If you have lost your code, please " .
                                         "send a message to CelloG from within Striker Manager asking him to retrieve it");
                }
            } else {
                // new manager, generate a code
                $p = new SellPlayer;
                $p->manager = $params['managername'];
                $test = $this->ask('retrieveManager', $p);
                $params['managercode'] = $test->getCode();
            }
            $player->searchmanager = $params['managername'];
            $player->code = $params['managercode'];
        }
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
        if (isset($params['manager']) && $params['manager']) {
            $player->manager = $params['manager'];
        }
        if (isset($params['country']) && $params['country']) {
            $player->country = $params['country'];
        }
        if (isset($params['name']) && $params['name']) {
            $player->name = $params['name'];
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
        if ($this->getMessage('search') == 'savesearch') {
            // remove possibility of unintended search
            $_GET = array();
            $this->broadcast('savesearch', $player);
        } else {
            $this->broadcast('search', $player);
        }
    }
}