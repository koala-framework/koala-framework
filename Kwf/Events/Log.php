<?php
class Kwf_Events_Log extends Zend_Log
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
                $instance = new Kwf_Events_Log();
                $instance->resetTimer();
            }
        }
        return $instance;
    }

    public function __construct()
    {
        $writer = new Kwf_Log_Writer_Stream(APP_PATH . '/eventlog', 'w');
        $writer->setFormatter(new Zend_Log_Formatter_Simple('%message%' . PHP_EOL));
        parent::__construct($writer);
    }

    
    public function log($message, $priority, $extras = null)
    {
        $message = str_repeat(' ', $this->indent * 2) . $message;
        $time = round((microtime(true) - $this->_start) * 1000);
        if (strlen($time) < 4) $time = str_repeat(' ', 4-strlen($time)).$time;
        $message = $time.' '.$message;
        parent::log($message, $priority, $extras);
    }

    public function resetTimer()
    {
        $this->_start = microtime(true);
    }
}
