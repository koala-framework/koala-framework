<?php
class Vps_Component_Events_Log extends Zend_Log
{
    public static function getInstance()
    {
        static $instance;
        if (!$instance) $instance = new Vps_Component_Events_Log();
        return $instance;
    }

    public function __construct()
    {
        $writer = new Zend_Log_Writer_Stream('eventlog', 'w');
        $writer->setFormatter(new Zend_Log_Formatter_Simple('%message%' . PHP_EOL));
        parent::__construct($writer);
    }

    public function logEvent($indent, $callback, $event)
    {
        $message =
            str_repeat(' ', ($indent - 1) * 2) .
            get_class($event) . ': ' .
            $callback['class'] . '::' . $callback['method'];
        $this->log($message, Zend_Log::INFO);
    }
}
