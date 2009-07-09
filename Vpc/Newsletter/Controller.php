<?php
class Vpc_Newsletter_Controller extends Vpc_Directories_Item_Directory_Controller
{
    protected $_defaultOrder = array('field' => 'create_date', 'direction' => 'DESC');

    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Grid_Column('subject', trlVps('Subject'), 300))
            ->setData(new Vpc_Newsletter_Controller_Data());
        $this->_columns->add(new Vps_Grid_Column('create_date', trlVps('Creation Date'), 120))
            ->setRenderer('localizedDatetime');
        $this->_columns->add(new Vps_Grid_Column('stat', trlVps('Status'), 400))
            ->setData(new Vpc_Newsletter_Controller_Info());
        $this->_columns->add(new Vps_Grid_Column_Button('edit', ' ', 20))
            ->setButtonIcon('/assets/silkicons/newspaper_go.png')
            ->setTooltip(trlVps('Edit and/or send Newsletter'));
        parent::_initColumns();
    }
}

class Vpc_Newsletter_Controller_Data extends Vps_Data_Table
{
    public function load($row)
    {
        $model = Vps_Model_Abstract::getInstance('Vpc_Mail_Model');
        $id = $row->component_id . '_' . $row->id . '-mail';
        $mailRow = $model->getRow($id);
        if ($mailRow) return $mailRow->subject;
        return '';
    }
}

class Vpc_Newsletter_Controller_Info extends Vps_Data_Table
{
    public function load($row)
    {
        $info = Vpc_Newsletter_Queue::getInfo($row);
        return $info['shortText'];
    }
}
