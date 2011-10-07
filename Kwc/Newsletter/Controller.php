<?php
class Kwc_Newsletter_Controller extends Kwc_Directories_Item_Directory_Controller
{
    protected $_defaultOrder = array('field' => 'create_date', 'direction' => 'DESC');

    protected $_buttons = array(
        'save',
        'delete',
        'reload',
        'add',
        'duplicate'
    );

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column('subject', trlKwf('Subject'), 300));
        $this->_columns->add(new Kwf_Grid_Column('create_date', trlKwf('Creation Date'), 120))
            ->setRenderer('localizedDatetime');
        $this->_columns->add(new Kwf_Grid_Column('info_short', trlKwf('Status'), 400));
        parent::_initColumns();
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

            $newDetail = Kwf_Util_Component::duplicate($source, $parentTarget);

            $newDetailRow = $newDetail->row;
            $newDetailRow->create_date = date('Y-m-d H:i:s');
            $newDetailRow->status = null;
            $newDetailRow->save();

            $mailRow = $newDetail->getChildComponent('-mail')->getComponent()->getRow();
            $mailRow->subject = trlKwf('Copy of').' '.$mailRow->subject;
            $mailRow->save();
        }
    }
}
