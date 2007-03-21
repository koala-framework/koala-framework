<?php
class E3_Component_News_Aktuelle extends E3_Component_Abstract
{
    private function getNews()
    {
        return $this->getDao()->getTable('E3_Dao_News')->fetchAll();
    }

    protected function generateTreeHierarchy(E3_PageCollection_Tree $pageCollection, $filename)
    {
        foreach($this->getNews() as $row) {
            if ($filename != '' && $filename != $row->filename) continue;

            $tag = "";
            if($this->getPageKey()!="") $tag = $this->getPageKey().".";
            $tag = $tag.$row->id;
            if (!$pageCollection->pageExists($this->getComponentId(), $tag)) {
	            $component = new E3_Component_News_Details($this->getDao(), $this->getComponentId(), $tag);
	            $component->setNewsId($row->id);

	            $pageCollection->addPage($component, $row->filename);
	            $pageCollection->setParentPage($component, $this);
            }
        }
    }
    
    public function getTemplateVars()
    {
        foreach($this->getNews() as $row) {
            $new = array('title'=>$row->title, 'filename'=>$row->filename);
            $ret['news'][] = $new;;
        }
        $ret['id'] = $this->getComponentId();
       	$ret['template'] = 'News/Aktuelle.html';
        return $ret;
    }
}