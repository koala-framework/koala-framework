<?php
class Kwc_Articles_Directory_Trl_Controller extends Kwc_Directories_Item_Directory_Trl_Controller
{
    protected $_buttons = array('save', 'add');
    protected $_paging = 25;
    protected $_filters = array('text'=>true);
    protected $_defaultOrder = array('field'=>'date', 'direction'=>'DESC');

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column_Visible('visible'));
        $this->_columns->add(new Kwf_Grid_Column('title', trlKwf('Title'), 200));
        $this->_columns->add(new Kwf_Grid_Column_Date('date', trlKwf('Publication')));
        $this->_columns->add(new Kwf_Grid_Column('vi_nr', trlKwf('VI-Nr'), 50));
        $this->_columns->add(new Kwf_Grid_Column('is_top', '&nbsp', 25))
            ->setRenderer('booleanIcon')
            ->setIcon('/assets/silkicons/exclamation.png')
            ->setTooltip('Top-Thema');
        $this->_columns->add(new Kwf_Grid_Column('read_required', '&nbsp', 25))
            ->setRenderer('booleanIcon')
            ->setIcon('/assets/silkicons/stop.png')
            ->setTooltip('Lesepflichtig');
        $this->_columns->add(new Kwf_Grid_Column('only_intern', '&nbsp', 25))
            ->setRenderer('booleanIcon')
            ->setIcon('/assets/silkicons/eye.png')
            ->setTooltip('Nur Intern');
    }
}
