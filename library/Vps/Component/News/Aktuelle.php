<?php
class Vps_Component_News_Aktuelle extends Vps_Component_Abstract
{
    private function getNews()
    {
        return $this->getDao()->getTable('Vps_Dao_News')->fetchAll();
    }

    protected function createComponents($filename)
    {
        $components = array();
        foreach($this->getNews() as $row) {
            if ($filename != '' && $filename != $row->filename) continue;

            $component = $this->createComponent('Vps_Component_News_Details', 0, $row->id);
            $component->setNewsId($row->id);
            $components[$row->filename] = $component;
        }
        return $components;
    }
    
    public function getTemplateVars($mode)
    {
        $ret = parent::getTemplateVars($mode);
        foreach($this->getNews() as $row) {
            $new = array('title'=>$row->title, 'filename'=>$row->filename);
            $ret['news'][] = $new;;
        }
        $ret['id'] = $this->getComponentId();
         $ret['template'] = 'News/Aktuelle.html';
        return $ret;
    }

}