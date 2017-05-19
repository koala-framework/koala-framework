<?php
class Kwf_Util_ProgressBar_Adapter_Composite extends Zend_ProgressBar_Adapter
{
    private $_adapters;
    public function __construct(array $adapters, $options = null)
    {
        $this->_adapters = $adapters;
        parent::__construct($options);
    }

    public function notify($current, $max, $percent, $timeTaken, $timeRemaining, $text)
    {
        foreach ($this->_adapters as $adapter) {
            $adapter->notify($current, $max, $percent, $timeTaken, $timeRemaining, $text);
        }
    }

    public function finish()
    {
        foreach ($this->_adapters as $adapter) {
            $adapter->finish();
        }
    }
}
