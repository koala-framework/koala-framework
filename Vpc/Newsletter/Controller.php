<?php
class Vpc_Newsletter_Controller extends Vpc_Directories_Item_Directory_Controller
{
    protected $_defaultOrder = array('field' => 'create_date', 'direction' => 'DESC');

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
}
