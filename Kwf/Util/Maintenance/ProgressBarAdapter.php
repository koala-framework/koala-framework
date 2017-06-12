<?php
class Kwf_Util_Maintenance_ProgressBarAdapter extends Zend_ProgressBar_Adapter
{
    private $_jobRun;
    public function __construct(Kwf_Model_Row_Interface $jobRun, $options = null)
    {
        parent::__construct($options);
        $this->_jobRun = $jobRun;
    }

    public function notify($current, $max, $percent, $timeTaken, $timeRemaining, $text)
    {
        $percent = round($percent*100);
        if ($this->_jobRun->progress != $percent) {
            $this->_jobRun->progress = $percent;
            $this->_jobRun->save();
        }
    }

    public function finish()
    {
        $this->_jobRun->progress = 100;
        $this->_jobRun->save();
    }
}

