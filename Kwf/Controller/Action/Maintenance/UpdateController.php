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
        $cmd = "php bootstrap.php maintenance update ";
        $cmd .= " --progressNum=".escapeshellarg($this->_getParam('progressNum'));
        $procData = Kwf_Util_BackgroundProcess::start($cmd, $this->view);
        $this->view->assign($procData);
    }
}
