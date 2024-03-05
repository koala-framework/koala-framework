<?php
class Kwf_Util_BruteForceCheck
{
    protected $_bruteForceCheckId;
    protected $_resetTimeInSeconds;

    public function __construct($bruteForceCheckId, $resetTimeInSeconds = 60)
    {
        $this->_bruteForceCheckId = preg_replace('/[^0-9a-z_]/', '_', $bruteForceCheckId);
        $this->_resetTimeInSeconds = $resetTimeInSeconds;
    }

    public static function shouldBlockIp($allowedCount = 10, $resetTimeInSeconds = 60, $bruteForceCheckId = false)
    {
        if (!$bruteForceCheckId) $bruteForceCheckId = 'globalBruteForceCheck';

        $bruteForceCheckId .= $_SERVER['REMOTE_ADDR'];
        $bruteForceCheck = new Kwf_Util_BruteForceCheck($bruteForceCheckId, $resetTimeInSeconds);

        if ($bruteForceCheck->getTriesCount() > $allowedCount) return true;

        $bruteForceCheck->increaseTriesCount();
        return false;
    }

    private function _getCacheId()
    {
        return 'brute-force-check-'.$this->_bruteForceCheckId;
    }

    public function getTriesCount()
    {
        $triesCount = Kwf_Cache_Simple::fetch($this->_getCacheId());
        if (!$triesCount) $triesCount = 0;
        return $triesCount;
    }

    public function increaseTriesCount()
    {
        $triesCount = $this->getTriesCount();
        Kwf_Cache_Simple::add($this->_getCacheId(), $triesCount+1, $this->_resetTimeInSeconds);
    }
}
