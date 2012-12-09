<?php
namespace SMGregsList;
class Messager
{
    protected $receivers = array();
    protected $controllers = array();

    /**
     * a list of message names that can be received by this object
     */
    function listMessages()
    {
        return array();
    }

    function attach(Messager $controller)
    {
        $this->controllers[] = $controller;
    }

    function receive($message, $content)
    {
        
    }

    function broadcast($message, $content)
    {
        foreach ($this->controllers as $controller) {
            $controller->message($message, $content);
        }
    }

    protected function message($message, $content = false)
    {
        foreach ($this->receivers as $receiver) {
            if (!in_array($message, $receiver->listMessages())) {
                continue;
            }
            $receiver->receive($message, $content);
        }
    }
}