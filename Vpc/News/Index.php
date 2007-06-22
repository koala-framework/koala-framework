<?php
class Vpc_News_Index extends Vpc_Abstract
{
    private $_titles;
    
    protected function getChildPages($filename = '')
    {
        $pages = array();
        foreach($this->getDao()->getTable('Vps_Dao_News')->fetchAll() as $row) {
            if ($filename != '' && $filename != $row->filename) continue;

            $page = $this->createPage('Vpc_News_Details', 0, $row->id);
            $page->setNewsId($row->id);
            $pages[$row->filename] = $page;
            $this->_titles[$row->filename] = $row->title;
        }
        return $pages;
    }
    
    public function getTemplateVars($mode)
    {
        $ret = parent::getTemplateVars($mode);
        
        $news = $this->generateHierarchy();
        foreach($news as $filename => $n) {
            $data['title'] = $this->_titles[$filename];
            $data['filename'] = $n->getPath();
            $ret['news'][] = $data;
        }
        $ret['id'] = $this->getComponentId();
        $ret['template'] = 'News/Aktuelle.html';
        return $ret;
    }

}