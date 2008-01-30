<?php
class Vpc_News_Component extends Vpc_News_List_Abstract_Component implements Vpc_News_Interface_Component
{
    public $content;

    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName'     => 'News',
            'tablename'         => 'Vpc_News_Model',
            'hideInNews'        => true,
            'childComponentClasses' => array(
                'details'       => 'Vpc_News_Details_Component'
            )
        ));

        $ret['assetsAdmin']['files'][] = 'vps/Vpc/News/Panel.js';
        return $ret;
    }

    public function getNews()
    {
        $ret = array();
        $ret['news'] = array();
        $where = array(
            'component_id = ?' => $this->getId()
        );

        if (!$this->showInvisible()) {
            $where['visible = 1'] = '';
        }
        $rows = $this->getTable()->fetchAll($where, 'publish_date DESC', 15);

        return $rows;
    }

    public function getTemplateVars()
    {
        return parent::getTemplateVars();
    }

}
