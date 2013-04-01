<?php
/**
 * Runner that executes update scripts
 */
class Kwf_Util_Update_Runner
{
    private $_updates;
    private $_debug = false;
    private $_skipClearCache = false;
    private $_verbose = false;
    /**
     * @var Zend_ProgressBar
     */
    private $_progressBar = null;

    public function __construct($updates)
    {
        $this->_updates = $updates;
    }

    public function setSkipClearCache($v)
    {
        $this->_skipClearCache = (bool)$v;
    }

    /**
     * Enabled debug mode. In debug mode when an update script causes an execption
     * the backtrace is shown and the update is stopped. This is useful for debugging
     * update scripts
     */
    public function setEnableDebug($v)
    {
        $this->_debug = (bool)$v;
    }

    /**
     * If verbose is enabled output is echoed while executing update scripts
     */
    public function setVerbose($v)
    {
        $this->_verbose = (bool)$v;
    }

    public function setProgressBar(Zend_ProgressBar $progressBar)
    {
        $this->_progressBar = $progressBar;
    }
    public function writeExecutedUpdates($doneNames)
    {
        Kwf_Registry::get('db')->query("UPDATE kwf_update SET data=?", serialize($doneNames));
        if (file_exists('update')) {
            //move away old update file to avoid confusion
            rename('update', 'update.backup');
        }
    }

    /**
     * Executes checkSettings method for all update scripts which should check if everything
     * is set up correctly to execute the update script
     */
    public function checkUpdatesSettings()
    {
        return $this->_executeUpdatesAction('checkSettings');
    }

    //TODO eventually move maintenance?
    //TODO support a progess bar, including progress steps for a single update script
    //TODO don't output anything (use output buffer for eventual output in update scripts??)
    public function executeUpdates()
    {
        $doneNames = array();

        Kwf_Util_Maintenance::writeMaintenanceBootstrap();

        $this->_executeUpdatesAction('preUpdate');
        $this->_executeUpdatesAction('update');
        $this->_executeUpdatesAction('postUpdate');
        if (!$this->_skipClearCache) {
            if ($this->_verbose) echo "\n";
            Kwf_Util_ClearCache::getInstance()->clearCache('all', !$this->_verbose);
            if ($this->_verbose) echo "\n";
        }
        $this->_executeUpdatesAction('postClearCache');
        foreach ($this->_updates as $k=>$u) {
            $doneNames[] = $u->getUniqueName();
        }

        Kwf_Util_Maintenance::restoreMaintenanceBootstrap();

        return $doneNames;
    }

    private function _executeUpdatesAction($method)
    {
        $ret = true;
        foreach ($this->_updates as $update) {
            if ($method != 'checkSettings') {
                if ($method != 'postClearCache' && !$this->_skipClearCache) {
                    Kwf_Util_ClearCache::getInstance()->clearCache('all', false, false);
                }
                Kwf_Model_Abstract::clearInstances(); //wegen eventueller meta-data-caches die sich geändert haben
                Kwf_Component_Generator_Abstract::clearInstances();
                Kwf_Component_Data_Root::reset();
            }
            if ($this->_progressBar && $method == 'update') {
                $this->_progressBar->next(1, $update->getUniqueName());
            }
            $e = false;
            try {
                if (!$this->_verbose) {
                    ob_start(); //update script should not output anything, if it still does discard it
                }
                $update->$method();
                if (!$this->_verbose) ob_end_clean();
            } catch (Exception $e) {
                if (!$this->_verbose) ob_end_clean();
                if ($this->_debug) throw $e;
                if ($method == 'checkSettings') {
                    echo get_class($update);
                }
                echo "\n\033[31mError:\033[0m\n";
                echo $e->getMessage()."\n\n";
                flush();
                $ret = false;
            }
        }
        return $ret;
    }
}
