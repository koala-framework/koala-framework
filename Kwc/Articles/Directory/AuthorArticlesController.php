<?php
class Kwc_Articles_Directory_AuthorArticlesController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_buttons = array('csv', 'xls');
    protected $_paging = 25;

    public function preDispatch()
    {
        parent::preDispatch();
        $this->setModel(Kwc_Abstract::createChildModel($this->_getParam('class')));
    }

    protected $_defaultOrder = array(
        array('field'=>'date', 'direction'=>'DESC'),
        array('field'=>'priority', 'direction'=>'DESC')
    );

    protected function _initColumns()
    {
        $this->_filters['date'] = array(
            'type'=>'DateRange',
            'width'=>80
        );
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
        parent::_initColumns();
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('deleted', 0);
        $ret->whereEquals('author_id', $this->_getParam('author_id'));
        return $ret;
    }
}
