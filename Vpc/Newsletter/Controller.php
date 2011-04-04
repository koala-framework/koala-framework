<?php
class Vpc_Newsletter_Controller extends Vpc_Directories_Item_Directory_Controller
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
        $this->_columns->add(new Vps_Grid_Column('subject', trlVps('Subject'), 300));
        $this->_columns->add(new Vps_Grid_Column('create_date', trlVps('Creation Date'), 120))
            ->setRenderer('localizedDatetime');
        $this->_columns->add(new Vps_Grid_Column('info_short', trlVps('Status'), 400));
        $this->_columns->add(new Vps_Grid_Column_Button('edit', ' ', 20))
            ->setButtonIcon('/assets/silkicons/newspaper_go.png')
            ->setTooltip(trlVps('Edit or send Newsletter'));
        parent::_initColumns();
    }

    public function jsonDuplicateAction()
    {
        if (!isset($this->_permissions['duplicate']) || !$this->_permissions['duplicate']) {
            throw new Vps_Exception("Duplicate is not allowed.");
        }

        $ids = $this->getRequest()->getParam($this->_primaryKey);
        $ids = explode(';', $ids);
        $this->view->data = array('duplicatedIds' => array());

        $parentTarget = Vps_Component_Data_Root::getInstance()
            ->getComponentById($this->_getParam('componentId'));

        foreach ($ids as $id) {
            $sourceId = $this->_getParam('componentId').'_'.$id;
            $source = Vps_Component_Data_Root::getInstance()
                ->getComponentById($sourceId);

            $newDetail = Vps_Util_Component::duplicate($source, $parentTarget);

            $newDetailRow = $newDetail->row;
            $newDetailRow->create_date = date('Y-m-d H:i:s');
            $newDetailRow->status = null;
            $newDetailRow->save();

            $mailRow = $newDetail->getChildComponent('-mail')->getComponent()->getRow();
            $mailRow->subject = trlVps('Copy of').' '.$mailRow->subject;
            $mailRow->save();
        }
    }
}
