<?php
namespace SMGregsList;
class Messager
{
    protected $receivers = array();
    protected $controllers = array();

    /**
     * a list of message names that can be received by this object
     */
    function listMessages(array $newmessages)
    {
        return array_merge(array('attach'), $newmessages);
    }

    function attach(Messager $controller)
    {
        $this->controllers[] = $controller;
        $controller->addReceiver($this);
    }

    function addReceiver(Messager $receiver)
    {
        $this->receivers[] = $receiver;
    }

    function receive($message, $content)
    {
        
    }

    function broadcast($message, $content = false)
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