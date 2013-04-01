<?php
/**
 * Runner that executes update scripts
 */
class Kwf_Util_Update_Runner
{
    private $_updates;
    private $_debug = false;
    private $_skipClearCache = false;

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

    //TODO remove doneNames paramter, just return executed scripts
    //TODO move writing kwf_update somewhere else
    //TODO move checkSettings into own method
    //TODO eventually move maintenance?
    //TODO support a progess bar, including progress steps for a single update script
    //TODO don't output anything (use output buffer for eventual output in update scripts??)
    public function executeUpdates($doneNames)
    {
        if ($this->_executeUpdate('checkSettings')) {

            Kwf_Util_Maintenance::writeMaintenanceBootstrap();

            $this->_executeUpdate('preUpdate');
            $this->_executeUpdate('update');
            $this->_executeUpdate('postUpdate');
            if (!$this->_skipClearCache) {
                echo "\n";
                Kwf_Util_ClearCache::getInstance()->clearCache('all', true);
                echo "\n";
            }
            $this->_executeUpdate('postClearCache');
            foreach ($this->_updates as $k=>$u) {
                if (!in_array($u->getRevision(), $doneNames)) {
                    $doneNames[] = $u->getUniqueName();
                }
            }
            Kwf_Registry::get('db')->query("UPDATE kwf_update SET data=?", serialize($doneNames));
            if (file_exists('update')) {
                //move away old update file to avoid confusion
                rename('update', 'update.backup');
            }
            echo "\n\033[32mupdate finished\033[0m\n";

            Kwf_Util_Maintenance::restoreMaintenanceBootstrap();

        } else {
            echo "\nupdate stopped\n";
        }
        return $doneNames;
    }

    private function _executeUpdate($method)
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
                if ($method == 'update') {
                    echo "\nexecuting $method ".$update->getUniqueName();
                    echo "... ";
                    flush();
                }
            }
            $e = false;
            if (in_array('db', $update->getTags())) {
                $databases = Kwf_Registry::get('config')->server->databases->toArray();
            } else {
                $databases = array('web');
            }
            foreach ($databases as $db) {
                if (!$db) continue;
                if ($method == 'update') {
                    echo $db.' ';
                    flush();
                }
                try {
                    if (Kwf_Registry::get('dao')) {
                        $db = Kwf_Registry::get('dao')->getDb($db);
                    } else {
                        $db = null;
                    }
                } catch (Exception $e) {
                    if ($method == 'update') {
                        echo "skipping, invalid db\n";
                        flush();
                    }
                    continue;
                }
                Kwf_Registry::set('db', $db);
                try {
                    $res = $update->$method();
                } catch (Exception $e) {
                    if ($this->_debug) throw $e;
                    if ($method == 'checkSettings') {
                        echo get_class($update);
                    }
                    echo "\n\033[31mError:\033[0m\n";
                    echo $e->getMessage()."\n\n";
                    flush();
                    $ret = false;
                }
                if (!$e) {
                    if ($res) {
                        print_r($res);
                    }
                }

                //reset to default database
                $db = null;
                try {
                    if (Kwf_Registry::get('dao')) $db = Kwf_Registry::get('dao')->getDb();
                } catch (Exception $e) {}
                Kwf_Registry::set('db', $db);
            }
            if ($method == 'update' && $ret) {
                echo "\033[32 OK \033[0m\n";
            }
            flush();
        }
        return $ret;
    }
}
