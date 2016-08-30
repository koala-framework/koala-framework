<?php

class Kwc_Advanced_DownloadsTree_DownloadsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_buttons = array('save', 'add', 'edit', 'delete');
    protected $_defaultOrder = array('field'=>'date', 'direction'=>'DESC');

    public function preDispatch()
    {
        $this->_modelName = Kwc_Abstract::getSetting($this->_getParam('class'), 'downloadsModel');
        parent::preDispatch();
    }

    public function init()
    {
        $class = $this->_getParam('class');
        $this->_editDialog = array(
            'controllerUrl'=> Kwc_Admin::getInstance($class)->getControllerUrl('Download'),
            'width'=>500,
            'height'=>240
        );
        parent::init();
    }

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Kwf_Grid_Column_Visible());
        $this->_columns->add(new Kwf_Grid_Column('type', trlKwf('Typ'), 30))
            ->setData(new Kwc_Advanced_DownloadsTree_Data_Fileicon())
            ->setRenderer('image');
        $this->_columns->add(new Kwf_Grid_Column('text', trlKwf('Document'), 350));
        $this->_columns->add(new Kwf_Grid_Column('filename', trlKwf('Filename'), 100))
            ->setData(new Kwc_Advanced_DownloadsTree_Data_Filename());
        $this->_columns->add(new Kwf_Grid_Column('filesize', trlKwf('Size'), 100))
            ->setData(new Kwc_Advanced_DownloadsTree_Data_Filesize())
            ->setRenderer('fileSize');
        $this->_columns->add(new Kwf_Grid_Column_Date('date', trlKwf('Date')));
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        if (!$this->_getParam('project_id')) return null;
        $ret->whereEquals('project_id', $this->_getParam('project_id'));
        return $ret;
    }
}
