<?php
namespace SMGregsList;
class Messager
{
    protected $receivers = array();
    protected $controllers = array();
    protected $dependencies = array();

    /**
     * a list of message names that can be received by this object
     */
    function listMessages(array $newmessages)
    {
        return array_merge(array('attach', 'detach'), $newmessages);
    }

    function addDependency(Messager $object)
    {
        $this->dependencies[spl_object_hash($object)] = $object;
    }

    function attach(Messager $controller)
    {
        $this->controllers[spl_object_hash($controller)] = $controller;
        $controller->addReceiver($this);
        if (!count($this->dependencies)) {
            return;
        }
        foreach ($this->dependencies as $dependency) {
            $dependency->attach($controller);
        }
    }

    function detach(Messager $controller)
    {
        unset($this->controllers[spl_object_hash($controller)]);
        $controller->removeReceiver($this);
        if (!count($this->dependencies)) {
            return;
        }
        foreach ($this->dependencies as $dependency) {
            $controller->removeReceiver($dependency);
        }
    }

    function addReceiver(Messager $receiver)
    {
        $this->receivers[spl_object_hash($receiver)] = $receiver;
    }

    function removeReceiver(Messager $receiver)
    {
        unset($this->receivers[spl_object_hash($receiver)]);
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