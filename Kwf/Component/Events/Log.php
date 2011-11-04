<?php
class Kwf_Component_Events_Log extends Zend_Log
{
    public $indent = 0;

    /**
     * @return self
     */
    public static function getInstance()
    {
        static $instance;
        if (is_null($instance)) {
            $instance = false;
            if (Kwf_Config::getValue('debug.eventlog')) {
                $instance = new Kwf_Component_Events_Log();
            }
        }
        return $instance;
    }

    public function __construct()
    {
        $writer = new Zend_Log_Writer_Stream('eventlog', 'w');
        $writer->setFormatter(new Zend_Log_Formatter_Simple('%message%' . PHP_EOL));
        parent::__construct($writer);
    }

    
    public function log($message, $priority, $extras = null)
    {
        $message = str_repeat(' ', $this->indent * 2) . $message;
        parent::log($message, $priority, $extras);
    }
}
