<?php
class Kwf_Controller_Action_Maintenance_UpdateController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_buttons = array();

    public function indexAction()
    {
        $this->view->typeNames = Kwf_Util_ClearCache::getInstance()->getTypeNames();
        $this->view->assetsType = 'Kwf_Controller_Action_Maintenance:Update';
        $this->view->xtype = 'kwf.maintenance.update';
    }

    protected function _initColumns()
    {
        $doneNames = Kwf_Util_Update_Helper::getExecutedUpdatesNames();
        $updates = Kwf_Util_Update_Helper::getUpdates(0, 9999999);
        $data = array();
        $id = 0;
        foreach ($updates as $k=>$u) {
            $data[] = array(
                'id' => ++$id,
                'revision' => $u->getRevision(),
                'name' => $u->getUniqueName(),
                'executed' => in_array($u->getUniqueName(), $doneNames)
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
 
        $errors = $runner->getErrors();
        if ($errors) {
            $errMsg = count($errors)." setup script(s) failed:\n";
            foreach ($errors as $error) {
                $errMsg .= $error['name'].": \n";
                $errMsg .= $error['message']."\n\n";
            }
            throw new Kwf_Exception_Client(nl2br($errMsg));
        }
    }
}
