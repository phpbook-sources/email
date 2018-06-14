<?php namespace PHPBook\Email\Driver;

abstract class Adapter {
    
    public abstract function dispatch(\PHPBook\Email\Message $message): Bool;

}