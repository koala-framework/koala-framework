<?php
class Vps_Component_News_Aktuelle extends Vps_Component_Abstract
{
    private function getNews()
    {
        return $this->getDao()->getTable('Vps_Dao_News')->fetchAll();
    }

    protected function generateTreeHierarchy(Vps_PageCollection_Tree $pageCollection, $filename)
    {
        foreach($this->getNews() as $row) {
            if ($filename != '' && $filename != $row->filename) continue;

            $component = $this->createPageInTree($pageCollection, 'Vps_Component_News_Details', $row->filename, $this->getComponentId(), $row->id);
            if($component) $component->setNewsId($row->id);
        }
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