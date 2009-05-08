<?php
class Vpc_Advanced_DownloadsTree_ViewDownloadsControllerDownloadData extends Vps_Data_Abstract
{
    public function load($row)
    {
        $url = Vps_Media::getUrlByRow($row, 'File');
        return '<a class="vpcAdvancedDownloadsTreeLink" href="'.$url.'" target="_blank">'.$row->text.'</a>';
    }
}

class Vpc_Advanced_DownloadsTree_ViewDownloadsController extends Vps_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Vpc_Advanced_DownloadsTree_Downloads';
    protected $_buttons = array();
    protected $_defaultOrder = array('field'=>'date', 'direction'=>'DESC');
    protected function _initColumns()
    {
        parent::_initColumns();

        $this->_columns->add(new Vps_Grid_Column('type', trl('Typ'), 30))
            ->setData(new Vpc_Advanced_DownloadsTree_Data_Fileicon())
            ->setRenderer('image');
        $this->_columns->add(new Vps_Grid_Column('text', trl('Dokument'), 320))
            ->setData(new Vpc_Advanced_DownloadsTree_ViewDownloadsControllerDownloadData());
        $this->_columns->add(new Vps_Grid_Column('filesize', trl('Größe'), 60))
            ->setData(new Vpc_Advanced_DownloadsTree_Data_Filesize())
            ->setRenderer('fileSize');
        $this->_columns->add(new Vps_Grid_Column_Date('date', trl('Datum')));
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        if (!$this->_getParam('project_id')) return null;
        $ret->whereEquals('project_id', $this->_getParam('project_id'));
        $ret->whereEquals('visible', 1);
        return $ret;
    }

    protected function _isAllowedComponent()
    {
        return true;
    }
}
