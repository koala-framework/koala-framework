<?php
class Vps_Util_ProgressBar_Adapter_Cache extends Zend_ProgressBar_Adapter
{
    private $_progressNum = null;

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
            throw new Vps_Exception("this progressbar adapter cannot be used without a progressNum");
        }
        return $this->_progressNum;
    }

    private function _getCache()
    {
        return Vps_Cache::factory('Core', 'File',
            array(
                'lifetime' => 3600,
                'automatic_serialization' => true
            ),
            array(
                'cache_dir' => 'application/cache/model'
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
    }

    public function finish()
    {
        $this->_saveStatus(array('finished' => true));
    }
}
