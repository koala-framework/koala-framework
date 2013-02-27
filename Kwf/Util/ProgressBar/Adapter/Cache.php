<?php
class Kwf_Util_ProgressBar_Adapter_Cache extends Zend_ProgressBar_Adapter
{
    private $_progressNum = null;
    private $_lastWrittenPercent = null;
    private $_lastWrittenTime = null;

    public function __construct($cfg = array())
    {
        if (!is_array($cfg)) {
            $cfg = array('progressNum' => $cfg);
        }

        if (!empty($cfg['progressNum'])) {
            $this->setProgressNum($cfg['progressNum']);
        }
    }

    public function setProgressNum($num)
    {
        $this->_progressNum = $num;
        return $this;
    }

    public function getProgressNum()
    {
        if (is_null($this->_progressNum)) {
            throw new Kwf_Exception("this progressbar adapter cannot be used without a progressNum");
        }
        return $this->_progressNum;
    }

    private function _getCache()
    {
        return Kwf_Cache::factory('Core', 'File',
            array(
                'lifetime' => 3600,
                'automatic_serialization' => true
            ),
            array(
                'cache_dir' => 'cache/model',
                'file_name_prefix' => 'progressbar'
            )
        );
    }

    public function getStatus()
    {
        $cache = $this->_getCache();
        return $cache->load($this->getProgressNum().'progressbarAdapterJson');
    }

    private function _saveStatus($data)
    {
        $cache = $this->_getCache();
        return $cache->save($data, $this->getProgressNum().'progressbarAdapterJson');
    }

    // the following methods must be overwritten
    public function notify($current, $max, $percent, $timeTaken, $timeRemaining, $text)
    {
        //lastWrittenPercent and lastWrittenTime are used to prevent performance issues if
        //many progresses are written. (the filesystem access at nfs can slow that down)
        //we just update the progressbar if the percentage increases and
        //the last request was at least 500ms ago
        if (!$this->_lastWrittenPercent) {
            $this->_lastWrittenPercent = (int)($percent*100);
        }
        if (!$this->_lastWrittenTime) {
            $this->_lastWrittenTime = microtime(true);
        }
        if ($this->_lastWrittenPercent < (int)($percent*100)
            && $this->_lastWrittenTime+0.5 <= microtime(true)) {
            $arguments = array(
                'current'       => $current,
                'max'           => $max,
                'percent'       => ($percent * 100),
                'timeTaken'     => $timeTaken,
                'timeRemaining' => $timeRemaining,
                'text'          => $text,
                'finished'      => false
            );
            $this->_saveStatus($arguments);
            $this->_lastWrittenPercent = (int)($percent*100);
            $this->_lastWrittenTime = microtime(true);
        }
    }

    public function finish()
    {
        $this->_saveStatus(array('finished' => true));
    }
}
