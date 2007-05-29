<?php
class Vpc_News_Aktuelle extends Vpc_Abstract
{
    private $_titles;
    
    protected function createComponents($filename)
    {
        $components = array();
        foreach($this->getDao()->getTable('Vps_Dao_News')->fetchAll() as $row) {
            if ($filename != '' && $filename != $row->filename) continue;

            $component = $this->createComponent('Vpc_News_Details', 0, $row->id);
            $component->setNewsId($row->id);
            $components[$row->filename] = $component;
            $this->_titles[$row->filename] = $row->title;
        }
        return $components;
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