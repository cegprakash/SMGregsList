<?php
namespace SMGregsList\Frontend\Json;
use SMGregsList\Messager, SMGregsList\SearchPlayer, SMGregsList\Player, SMGregsList\SellPlayer, SMGregsList\Frontend\HTMLController;
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
    public static $id = 0;
    protected $retrieved;
    protected $input;
    function __construct()
    {
        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
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
        return parent::listMessages(array('reply', 'parseJson'));
    }

    function receive($message, $content)
    {
        if ($message == 'reply') {
            return $this->jsonReply($content['message'], $content['params'], self::$id);
        } elseif ($message == 'parseJson') {
            if ($this->getMessage('search') == 'search') {
                return $this->detectSearch();
            } elseif ($this->getMessage('search') == 'exists') {
                return $this->detectPlayer();
            } else {
                return $this->detectSell();
            }
        }
        return parent::receive($message, $content);
    }

    function detectPlayer()
    {
        $params = $this->getParams('search');
        if (isset($params['ids'])) {
            $players = array();
            foreach ($params['ids'] as $id) {
                $player = new SellPlayer;
                $player->id = $id;
                $players[] = $sell;
            }
            $this->broadcast('existsmultiple', $players);
        } else {
            $player = new SellPlayer;
            $player->id = $params['id'];
            $this->broadcast('exists', $player);
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