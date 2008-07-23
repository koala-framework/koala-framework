<?php
class Vpc_News_Top_Component extends Vpc_News_List_Abstract_Component
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => 'News.Top',
            'componentIcon' => new Vps_Asset('newspaper'),
            'tablename'     => 'Vpc_News_Top_Model',
            'default'       => array(),
            'limit'         => 5
        ));
    }

    protected function _getNewsComponent()
    {
        $row = $this->_getRow();
        if ($row && $row->news_component_id) {
            return Vps_Component_Data_Root::getInstance()->getComponentByDbId($row->news_component_id);
        }
        return null;
    }
    protected function _selectNews()
    {
        $select = parent::_selectNews();
        if (!$select) return null;
        $select->limit($this->_getSetting('limit'));
        return $select;
    }

    public function getTemplateVars()
    {
        return parent::getTemplateVars();
    }

}
