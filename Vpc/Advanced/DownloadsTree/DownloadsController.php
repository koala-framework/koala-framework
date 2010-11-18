<?php

class Vpc_Advanced_DownloadsTree_DownloadsController extends Vps_Controller_Action_Auto_Grid
{
    protected $_buttons = array('save', 'add', 'edit', 'delete');
    protected $_defaultOrder = array('field'=>'date', 'direction'=>'DESC');

    public function preDispatch()
    {
        $this->_modelName = Vpc_Abstract::getSetting($this->_getParam('class'), 'downloadsModel');
        parent::preDispatch();
    }

    public function init()
    {
        $class = $this->_getParam('class');
        $this->_editDialog = array(
            'controllerUrl'=> Vpc_Admin::getInstance($class)->getControllerUrl('Download'),
            'width'=>500,
            'height'=>240
        );
        parent::init();
    }

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Vps_Grid_Column('type', trlVps('Typ'), 30))
            ->setData(new Vpc_Advanced_DownloadsTree_Data_Fileicon())
            ->setRenderer('image');
        $this->_columns->add(new Vps_Grid_Column('text', trlVps('Document'), 350));
        $this->_columns->add(new Vps_Grid_Column('filename', trlVps('Filename'), 100))
            ->setData(new Vpc_Advanced_DownloadsTree_Data_Filename());
        $this->_columns->add(new Vps_Grid_Column('filesize', trlVps('Size'), 100))
            ->setData(new Vpc_Advanced_DownloadsTree_Data_Filesize())
            ->setRenderer('fileSize');
        $this->_columns->add(new Vps_Grid_Column_Date('date', trlVps('Date')));
        $this->_columns->add(new Vps_Grid_Column_Visible());
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        if (!$this->_getParam('project_id')) return null;
        $ret->whereEquals('project_id', $this->_getParam('project_id'));
        return $ret;
    }
}
