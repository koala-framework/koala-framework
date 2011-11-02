<?php
class Kwf_Component_Events_Log extends Zend_Log
{
    public static function getInstance()
    {
        static $instance;
        if (!$instance) $instance = new Kwf_Component_Events_Log();
        return $instance;
    }

    public function __construct()
    {
        $writer = new Zend_Log_Writer_Stream('eventlog', 'w');
        $writer->setFormatter(new Zend_Log_Formatter_Simple('%message%' . PHP_EOL));
        parent::__construct($writer);
    }

    public function logEvent($indent, $callback, Kwf_Component_Event_Abstract $event)
    {
        $message =
            str_repeat(' ', ($indent - 1) * 2) .
            $event->__toString() . ': ' .
            ( $callback ? ($callback['class'] . '::' . $callback['method'] . '(' . _btArgsString($callback['config']) . ')') : '(none)' );
        $this->log($message, Zend_Log::INFO);
    }
}
