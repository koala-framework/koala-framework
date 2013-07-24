<?php
class Kwc_Newsletter_Controller extends Kwc_Directories_Item_Directory_Controller
{
    protected $_defaultOrder = array('field' => 'create_date', 'direction' => 'DESC');
    protected $_buttons = array('add', 'duplicate');

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column('subject', trlKwf('Subject'), 300));
        $this->_columns->add(new Kwf_Grid_Column('create_date', trlKwf('Creation Date'), 120))
            ->setRenderer('localizedDatetime');
        $this->_columns->add(new Kwf_Grid_Column('info_short', trlKwf('Status'), 400));
        parent::_initColumns();
        $countEditButtons = 0;
        foreach($this->_columns as $column) {
            if ($column instanceof Kwc_Directories_Item_Directory_ControllerEditButton) {
                $countEditButtons++;
            }
        }
        if ($countEditButtons == 1) {
            $this->_columns->getByName('edit_0')
                ->setEditName(trlKwf('Edit'))
                ->setRenderer('cellButtonText')
                ->setWidth(85);
        }
    }

    public function jsonDuplicateAction()
    {
        if (!isset($this->_permissions['duplicate']) || !$this->_permissions['duplicate']) {
            throw new Kwf_Exception("Duplicate is not allowed.");
        }

        $ids = $this->getRequest()->getParam($this->_primaryKey);
        $ids = explode(';', $ids);
        $this->view->data = array('duplicatedIds' => array());

        $parentTarget = Kwf_Component_Data_Root::getInstance()
            ->getComponentById($this->_getParam('componentId'));

        foreach ($ids as $id) {
            $sourceId = $this->_getParam('componentId').'_'.$id;
            $source = Kwf_Component_Data_Root::getInstance()
                ->getComponentById($sourceId);

            // Switch off observer due to performance - it's not necessary here
            Kwf_Component_ModelObserver::getInstance()->disable();
            $newDetail = Kwf_Util_Component::duplicate($source, $parentTarget);
            Kwf_Util_Component::afterDuplicate($source, $newDetail);
            Kwf_Component_ModelObserver::getInstance()->enable();

            $newDetailRow = $newDetail->row;
            $newDetailRow->create_date = date('Y-m-d H:i:s');
            $newDetailRow->last_sent_date = null;
            $newDetailRow->count_sent = 0;
            $newDetailRow->status = null;
            $newDetailRow->save();

            $mailRow = $newDetail->getChildComponent('-mail')->getComponent()->getRow();
            $mailRow->subject = trlKwf('Copy of').' '.$mailRow->subject;
            $mailRow->save();
        }
    }

    public function preDispatch()
    {
        parent::preDispatch();
        $this->_editDialog = false;
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row, $submitRow)
    {
        parent::_beforeInsert($row, $submitRow);
        $row->component_id = $this->_getParam('componentId');
        $row->mails_per_minute = 'normal';
        $row->create_date = date('Y-m-d H:i:s');
    }

    protected function _afterInsert(Kwf_Model_Row_Interface $row, $submitRow)
    {
        parent::_afterInsert($row, $submitRow);
        Kwf_Model_Abstract::getInstance('Kwc_Mail_Model')->createRow(array(
            'component_id' => $row->component_id . '_' . $row->id . '-mail'
        ))->save();
    }
}
