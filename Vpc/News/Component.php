<?php
class Vpc_News_Component extends Vpc_Abstract
{
    public $content;

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName'     => 'News',
            'tablename'         => 'Vpc_News_Model',
            'hideInNews'        => true,
            'childComponentClasses' => array(
                'details'       => 'Vpc_News_Details_Component'
            )
        ));
    }

    public function generateHierarchy($filename = '')
    {
        parent::generateHierarchy($filename);
        $pages = array();
        $where = array(
            'page_id' => $this->getDbId(),
            'component_key' => $this->getComponentKey()
        );
        if (!$this->showInvisible()) {
            $where['visible = 1'] = '';
        }
        $class = $this->_getClassFromSetting('details', 'Vpc_News_Details_Component');
        foreach ($this->getTable()->fetchAll($where) as $row) {
            $fn = $row->getUniqueString($row->title, 'title', $where);
            if ($filename != '' && $filename != $fn && $filename != $row->id) continue;
            $page = $this->createPage($class, $row->id);
            $page->setRow($row);
            $this->getPagecollection()->addTreePage($page, $fn, $row->title, $this);
            $this->getPagecollection()->hideInMenu($page);
            $pages[] = $page;
        }
        return $pages;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['news'] = array();
        foreach ($this->generateHierarchy() as $n) {
            $data = $n->row->toArray();
            $data['href'] = $n->getUrl();
            $ret['news'][] = $data;
        }
        return $ret;
    }

}
