<?php
namespace SMGregsList\Frontend\Json;
use SMGregsList\Messager, SMGregsList\SearchPlayer, SMGregsList\Player, SMGregsList\SellPlayer, SMGregsList\Frontend\HTMLController,
    SMGregsList\Manager;
function __handler($exception)
{
    if ($exception instanceof JsonRpcException) {
        header('Content-Type: application/json');
        echo json_encode(array(
            'result' => null,
            'error' => array(
                            'origin' => $exception->origin,
                            'code' => $exception->getCode(),
                            'message' => $exception->getMessage()
                            ),
            'id' => Controller::$id,
        ));
    } else {
        header('Content-Type: application/json');
        echo json_encode(array(
            'result' => null,
            'error' => array(
                            'origin' => 2,
                            'code' => $exception->getCode(),
                            'message' => $exception->getMessage()
                            ),
            'id' => Controller::$id,
        ));
    }
    exit;
}
function __error($level, $err, $file, $line)
{
    if (error_reporting() === 0) return;
    header('Content-Type: application/json');
    echo json_encode(array(
        'result' => null,
        'error' => array(
                        'origin' => 2,
                        'code' => -1,
                        'message' => $err . ' in ' . $file . ' on line ' . $line
                        ),
        'id' => Controller::$id,
    ));
    exit;
}
set_exception_handler(__NAMESPACE__ . '\\__handler');
set_error_handler(__NAMESPACE__ . '\\__error');
class Controller extends HTMLController
{
    const APIVERSION = 2;
    const minExtVersion = '0.5.0';
    public static $id = 0;
    protected $retrieved;
    protected $input;
    function __construct()
    {
        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: *");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }
    
        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
    
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    
            exit(0);
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!preg_match('/^http:\/\/en[1-3]?\.strikermanager\.com/', $_SERVER['HTTP_REFERER'])) {
                echo "Nice try";
                exit;
            }
            $content = explode(';', $_SERVER['CONTENT_TYPE']);
            $content = $content[0];
            if ($content == 'application/json') {
                $input = file_get_contents('php://input');
            } elseif ($content == 'application/x-www-form-urlencoded' && isset($params['_data_'])) {
                $input = $params['_data_'];
            } else {
                echo "This JSON-RPC server should only be accessed via programmatic API\n";
                exit;
            }
            $this->input = json_decode($input, true);
            if (null === $this->input) {
                echo "Invalid JSON\n";
                exit;
            }
            set_time_limit(60);
            if (!is_array($this->input) || !isset($this->input['message'])
                || !isset($this->input['params'])
                || !isset($this->input['id'])) {
                throw new JsonRpcException(1, JsonRpcException::ILLEGALSERVICE,
                                           'Missing message, params or id');
            }
            if (!isset($this->input['apiversion'])
                || $this->input['apiversion'] != self::APIVERSION) {
                throw new JsonRpcException('Your Chrome extension is out of date, you need version ' .
                                           self::minExtVersion . ' minimum to use this service');
            }
            if (!is_string($this->input['message'])
                || !is_int($this->input['id'])) {
                throw new JsonRpcException(1, JsonRpcException::ILLEGALSERVICE,
                                           'Invalid service, method or id');
            }
            self::$id = $this->input['id'];
//          throw new \Exception(json_encode($this->input['params']));
        } else {
            throw new \Exception("This JSON-RPC server should only be accessed via programmatic API");
        }
    }

    function listMessages(array $newmessages)
    {
        return parent::listMessages(array('reply', 'parseJson', 'getManager', 'managerRetrievedFromName'));
    }

    function receive($message, $content)
    {
        if ($message == 'reply') {
            return $this->jsonReply($content['message'], $content['params'], self::$id);
        } elseif ($message == 'parseJson') {
            if ($this->getMessage('search') == 'search') {
                return $this->detectSearch();
            } elseif ($this->getMessage('search') == 'exists') {
                $params = $this->getParams('search');
                if (isset($params['code']) && isset($params['manager']) && !isset($params['ids'])) {
                    $player = new SellPlayer;
                    $player->id = $params['id'];
                    $player->code = $params['code'];
                    $player->manager = $params['manager'];
                    $this->retrieved = $player;
                    try {
                        $this->broadcast('retrieveManager', $player);
                    } catch (\Exception $e) {
                        return $this->detectPlayer();
                    }
                    return $this->detectPlayer($player);
                }
                return $this->detectPlayer();
            } elseif ($this->getMessage('search') == 'getmanager') {
                $params = $this->getParams('search');
                $this->broadcast('retrieveManagerFromName', $params);
            } elseif ($this->getMessage('search') == 'findplayers') {
                $params = $this->getParams('search');
                $players = $this->ask('newMatches', $params);
                $newplayers = array();
                foreach ($players as $player) {
                    $newplayers[] = array('id' => $player->getId(), 'name' => $player->getName(), 'age' => $player->getAge(),
                                          'average' => $player->getAverage(), 'position' => $player->getPosition());
                }
                $this->broadcast('reply', array('message' => 'newplayers',
                                                'params' => array('players' => $newplayers)));
            } else {
                return $this->detectSell();
            }
        }
        // STOP - add new json API to the parseJson message handler above
        return parent::receive($message, $content);
    }

    function detectPlayer(SellPlayer $player = null)
    {
        $params = $this->getParams('search');
        if (isset($params['ids'])) {
            $players = array();
            if (isset($params['manager']) && isset($params['code'])) {
                $players['manager'] = $params['manager'];
                $players['code'] = $params['code'];
            }
            foreach ($params['ids'] as $id) {
                $player = new SellPlayer;
                $player->id = $id;
                $players[] = $player;
            }
            $this->broadcast('existsmultiple', $players);
        } else {
            if (null === $player) {
                $player = new SellPlayer;
                $player->id = $params['id'];
            }
            try {
                $this->broadcast('checkDeleteAndExists', array('player' => $player, 'manager' => $player->getManager()));
            } catch (\Exception $e) {
                $this->broadcast('exists', $player);
            }
        }
    }

    function jsonReply($message, $params, $id)
    {
        $info = json_encode(array(
            'id' => $id,
            'message' => $message,
            'params' => $params
        ));
        header('Content-type: application/json');
        header('Content-length: ' . strlen($info));
        echo $info;
        return true;
    }

    function getParams($type)
    {
        return $this->input['params'];
    }

    function getMessage($type)
    {
        return $this->input['message'];
    }
}