<?php
class Kwf_Controller_Action_Cli_Web_UpdateController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return 'Update to current version';
    }
    public static function getHelpOptions()
    {
        return array(
            array(
                'param'=> 'name',
                'value' => '20150310Foo',
                'allowBlank' => true,
                'help' => 'Executes specific update'
            ),
            array(
                'param'=> 'skip-clear-cache',
                'help' => 'Don\'t clear cache after update'
            )
        );
    }
    public function indexAction()
    {
        Kwf_Util_MemoryLimit::set(512);
        Kwf_Events_ModelObserver::getInstance()->disable();

        //try to update old-style db config
        if (file_exists('config.db.ini')) {
            $db = file_get_contents('config.db.ini');
            if (file_exists('config.local.ini')) {
                $c = file_get_contents('config.local.ini');
            } else {
                $c = "[production]\n";
            }
            $c .= "\n";
            $db = str_replace("[database]\n", '', $db);
            foreach (explode("\n", trim($db)) as $line) {
                if (trim($line)) $c .= "database.".$line."\n";
            }
            file_put_contents('config.local.ini', $c);
            unlink('config.db.ini');
        }

        if ($this->_getParam('rev')) {
            throw new Kwf_Exception("rev parameter is not supported anymore");
        }
        if ($this->_getParam('class')) {
            throw new Kwf_Exception("class parameter is not supported anymore");
        }

        $skipClearCache = $this->_getParam('skip-clear-cache');
        $excludeClearCacheType = $this->_getParam('exclude-clear-cache-type');

        $doneNames = Kwf_Util_Update_Helper::getExecutedUpdatesNames();

        if ($this->_getParam('name')) {
            $updates = Kwf_Util_Update_Helper::getUpdates();
            foreach ($updates as $k=>$u) {
                $n = $u->getUniqueName();
                $n = substr($n, strrpos($n, '_')+1);
                if ($n != $this->_getParam('name')) {
                    unset($updates[$k]);
                }
            }
        } else {

            if (!$skipClearCache) {
                $excludeTypes = $excludeClearCacheType;
                if ($excludeTypes) $excludeTypes .= ',';
                $excludeTypes .= 'componentView,componentUrl'; //never clear before updating (table structure might have changed)
                Kwf_Util_ClearCache::getInstance()->clearCache(array('types'=>'all', 'output'=>true, 'refresh'=>false, 'excludeTypes'=>$excludeTypes));
            }

            echo "Looking for update-scripts...";
            $updates = Kwf_Util_Update_Helper::getUpdates();
            foreach ($updates as $k=>$u) {
                if (in_array($u->getUniqueName(), $doneNames) || ($u->getLegacyName() && in_array($u->getLegacyName(), $doneNames))) {
                    unset($updates[$k]);
                }
            }
            echo " found ".count($updates)."\n\n";
        }


        $c = new Zend_ProgressBar_Adapter_Console();
        $c->setElements(array(Zend_ProgressBar_Adapter_Console::ELEMENT_PERCENT,
            Zend_ProgressBar_Adapter_Console::ELEMENT_BAR,
            Zend_ProgressBar_Adapter_Console::ELEMENT_TEXT));
        $c->setTextWidth(50);

        $runner = new Kwf_Util_Update_Runner($updates);
        $progress = new Zend_ProgressBar($c, 0, $runner->getProgressSteps());
        $runner->setProgressBar($progress);
        $runner->setVerbose(true);
        $runner->setEnableDebug($this->_getParam('debug'));
        $runner->setSkipClearCache($skipClearCache);
        $runner->setExcludeClearCacheTypes($excludeClearCacheType);

        $checkUpdatesSettings = $runner->checkUpdatesSettings();
        if (!$checkUpdatesSettings) {
            echo "\ncheckSettings failed, update stopped\n";
        } else {
            $executedUpdates = $runner->executeUpdates();
            echo "\n\033[32mupdate finished\033[0m\n";
            $runner->writeExecutedUpdates($executedUpdates);
        }

        if ($checkUpdatesSettings) {
            $runner->executePostMaintenanceBootstrapUpdates();
            echo "\n\033[32mpost maintenance bootstrap update finished\033[0m\n";
        }

        $errors = $runner->getErrors();
        if ($errors) {
            echo "\n\n".str_repeat('=', 16)."\n";
            echo count($errors)." update script(s) failed:\n";
            foreach ($errors as $error) {
                echo $error['name'].": \n";
                echo $error['message']."\n\n";
            }
            exit(1);
        } else {
            echo "\n".count($updates)." update script(s) successfully executed.\n";
            exit(0);
        }
    }
}
