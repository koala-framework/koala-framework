<?php
class Kwf_Controller_Action_Cli_Web_MaintenanceController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return 'maintenance (interal)';
    }

    public function clearCacheAction()
    {
        $options = array();
        if ($this->_getParam('skip-other-servers')) {
            $options['skipOtherServers'] = true;
        }

        $c = new Kwf_Util_ProgressBar_Adapter_Cache($this->_getParam('progressNum'));
        $options['progressAdapter'] = $c;
        Kwf_Util_ClearCache::getInstance()->clearCache($this->_getParam('type'), false, true, $options);
        $out = array(
            'success' => true
        );
        echo json_encode($out);
        exit;
    }

    public function updateAction()
    {
        $doneNames = Kwf_Util_Update_Helper::getExecutedUpdatesNames();
        $updates = Kwf_Util_Update_Helper::getUpdates(0, 9999999);
        $data = array();
        $id = 0;
        foreach ($updates as $k=>$u) {
            if (in_array($u->getUniqueName(), $doneNames)) {
                unset($updates[$k]);
            }
        }

        $c = new Kwf_Util_ProgressBar_Adapter_Cache($this->_getParam('progressNum'));

        $runner = new Kwf_Util_Update_Runner($updates);
        $progress = new Zend_ProgressBar($c, 0, $runner->getProgressSteps());
        $runner->setProgressBar($progress);
        if (!$runner->checkUpdatesSettings()) {
            throw new Kwf_Exception_Client("checkSettings failed, update stopped");
        }
        $doneNames = array_merge($doneNames, $runner->executeUpdates());
        $runner->writeExecutedUpdates($doneNames);
 
        $errMsg = '';
        $errors = $runner->getErrors();
        if ($errors) {
            $errMsg .= count($errors)." setup script(s) failed:\n";
            foreach ($errors as $error) {
                $errMsg .= $error['name'].": \n";
                $errMsg .= $error['message']."\n\n";
            }
        }
        
        $message = 'Executed '.count($updates)." update scripts";

        $out = array(
            'success' => true,
            'errMsg' => $errMsg,
            'message' => $message
        );
        echo json_encode($out);
        exit;
    }
}
