<?php
class Kwf_Controller_Action_Maintenance_UpdateController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_buttons = array();

    public function indexAction()
    {
        if (!file_exists('build/assets')) {
            throw new Kwf_Exception_Client("Installation incomplete: 'build' folder does not exist. You can generate it by calling 'php bootstrap.php build' on commandline. On production servers you should upload locally generated build.");
        }

        $this->view->typeNames = Kwf_Util_ClearCache::getInstance()->getTypeNames();
        $this->view->assetsPackage = Kwf_Assets_Package_Maintenance::getInstance('Maintenance');
        $this->view->xtype = 'kwf.maintenance.update';
        $this->view->useBackgroundProcess = !!Kwf_Config::getValue('server.phpCli');
    }

    protected function _initColumns()
    {
        $doneNames = Kwf_Util_Update_Helper::getExecutedUpdatesNames();
        $updates = Kwf_Util_Update_Helper::getUpdates();
        $data = array();
        $id = 0;
        foreach ($updates as $k=>$u) {
            $data[] = array(
                'id' => ++$id,
                'name' => $u->getUniqueName(),
                'executed' => in_array($u->getUniqueName(), $doneNames) || ($u->getLegacyName() && in_array($u->getLegacyName(), $doneNames))
            );
        }
        $this->_model = new Kwf_Model_FnF(array(
            'data' => $data
        ));
        $this->_columns->add(new Kwf_Grid_Column('revision', 'Number', 100));
        $this->_columns->add(new Kwf_Grid_Column('name', 'Name', 200));
        $this->_columns->add(new Kwf_Grid_Column_Checkbox('executed', 'Executed'));
    }

    public function jsonExecuteUpdatesAction()
    {
    
        if (Kwf_Config::getValue('server.phpCli')) {
            $cmd = Kwf_Config::getValue('server.phpCli')." bootstrap.php maintenance update ";
            $cmd .= " --progressNum=".escapeshellarg($this->_getParam('progressNum'));
            $procData = Kwf_Util_BackgroundProcess::start($cmd, $this->view);
            $this->view->assign($procData);
        } else {
            self::executeUpdates($this->getRequest(), $this->view);
        }
    }

    public static function executeUpdates($request, $view)
    {
        $doneNames = Kwf_Util_Update_Helper::getExecutedUpdatesNames();
        $updates = Kwf_Util_Update_Helper::getUpdates();
        foreach ($updates as $k=>$u) {
            if (in_array($u->getUniqueName(), $doneNames) || ($u->getLegacyName() && in_array($u->getLegacyName(), $doneNames))) {
                unset($updates[$k]);
            }
        }

        $c = new Kwf_Util_ProgressBar_Adapter_Cache($request->getParam('progressNum'));

        $runner = new Kwf_Util_Update_Runner($updates);
        $progress = new Zend_ProgressBar($c, 0, $runner->getProgressSteps());
        $runner->setProgressBar($progress);
        if (!$runner->checkUpdatesSettings()) {
            throw new Kwf_Exception_Client("checkSettings failed, update stopped");
        }
        $executedUpdates = $runner->executeUpdates();
        $runner->writeExecutedUpdates($executedUpdates);

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

        $view->errMsg = $errMsg;
        $view->message = $message;
    }
}
