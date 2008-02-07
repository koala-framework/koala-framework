<?php
class Vpc_News_Titles_Component extends Vpc_News_List_Abstract_Component implements Vpc_News_Titles_Interface_Component
{

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => 'News.Titles',
            'componentIcon' => new Vps_Asset('newspaper'),
            'tablename'     => 'Vpc_News_Titles_Model',
            'hideInNews'        => true,
            'childComponentClasses' => array()
        ));
    }

    public function getNewsComponent()
    {
        $row = $this->_getRow();
        if ($row && $row->news_component_id) {
            $pc = $this->getPageCollection();
            return $pc->getComponentById($row->news_component_id);
        } else {
            return null;
        }
    }

    public function getNews()
    {
         return $this->getNewsComponent()->getNews();
/*        $where = array(
            'component_id = ?' => $this->getId()
        );

        if (!$this->showInvisible()) {
            $where['visible = 1'] = '';
        }
        $rows = $this->getTable()->fetchAll($where, 'publish_date DESC', 15);
*/
        $rows = array();
        return $rows;
    }

    public function getTemplateVars()
    {
        return parent::getTemplateVars();
    }

}
