<?php
class Vpc_Advanced_DownloadsTree_ViewDownloadsControllerDownloadData extends Vps_Data_Abstract
{
    private $_componentId;
    public function __construct($componentId)
    {
        $this->_componentId = $componentId;
    }
    public function load($row)
    {
        $pk = $row->getModel()->getPrimaryKey();
        $url = Vps_Media::getUrl(get_class($row->getModel()), $this->_componentId.'_'.$row->$pk, 'File', $row->getParentRow('File'));
        return '<a class="vpcAdvancedDownloadsTreeLink" href="'.$url.'" target="_blank">'.$row->text.'</a>';
    }
}

class Vpc_Advanced_DownloadsTree_ViewDownloadsController extends Vps_Controller_Action_Auto_Grid
{
    protected $_buttons = array();
    protected $_defaultOrder = array('field'=>'date', 'direction'=>'DESC');

    public function preDispatch()
    {
        $this->_modelName = Vpc_Abstract::getSetting($this->_getParam('class'), 'downloadsModel');
        parent::preDispatch();
    }

    protected function _initColumns()
    {
        parent::_initColumns();

        $this->_columns->add(new Vps_Grid_Column('type', trlVps('Typ'), 30))
            ->setData(new Vpc_Advanced_DownloadsTree_Data_Fileicon())
            ->setRenderer('image');
        $this->_columns->add(new Vps_Grid_Column('text', trlVps('Document'), 320))
            ->setData(new Vpc_Advanced_DownloadsTree_ViewDownloadsControllerDownloadData($this->_getParam('componentId')));
        $this->_columns->add(new Vps_Grid_Column('filesize', trlVps('Size'), 60))
            ->setData(new Vpc_Advanced_DownloadsTree_Data_Filesize())
            ->setRenderer('fileSize');
        $this->_columns->add(new Vps_Grid_Column_Date('date', trlVps('Date')));
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        if (!$this->_getParam('project_id')) return null;
        $ret->whereEquals('project_id', $this->_getParam('project_id'));
        $ret->whereEquals('visible', 1);
        return $ret;
    }

    protected function _hasPermissions($row, $action)
    {
        //damit keine gefakte project_id übergeben werden kann
        //isAllowedComponent schaut ja nur auf componentId
        $p = $row->getParentRow('Project');
        if ($p->component_id != $this->_getParam('componentId')) {
            return false;
        }
        if (!$p->visible) return false;
        return parent::_hasPermissions($row, $action);
    }

    protected function _isAllowedComponent()
    {
        $c = Vps_Component_Data_Root::getInstance()->getComponentByDbId($this->_getParam('componentId'));
        while($c) {
            foreach (Vpc_Abstract::getSetting($c->componentClass, 'plugins') as $p) {
                if (is_instance_of($p, 'Vps_Component_Plugin_Interface_Login')) {
                    $p = new $p($c->componentId);
                    if (!$p->isLoggedIn()) {
                        return false;
                    }
                }
            }
            if ($c->isPage) break;
            $c = $c->parent;
        }
        return true;
    }
}
