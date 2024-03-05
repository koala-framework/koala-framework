<?php
class Kwc_Basic_LinkTag_Event_EventsController extends Kwc_Basic_LinkTag_News_NewsController
{
    protected $_modelName = 'Kwc_Events_Directory_Model';
    protected $_defaultOrder = array('field'=>'start_date', 'direction'=>'DESC');

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column('title'));
        $this->_columns->add(new Kwf_Grid_Column('start_date'));
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('visible', 1);
        return $ret;
    }
}
