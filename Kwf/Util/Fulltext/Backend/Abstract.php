<?php
abstract class Kwf_Util_Fulltext_Backend_Abstract
{
    public static function getInstance()
    {
        static $i;
        if (!isset($i)) {
            $be = Kwf_Config::getValue('fulltext.backend');
            $i = new $be();
        }
        return $i;
    }

    abstract public function search(Kwf_Component_Data $subroot, $query);
    abstract public function userSearch(Kwf_Component_Data $subroot, $queryString, $offset, $limit, $params = array());
    abstract public function getSubroots();
    abstract public function optimize($debugOutput = false);
    abstract public function indexPage(Kwf_Component_Data $page, $debugOutput = false);
    abstract public function getAllDocuments(Kwf_Component_Data $subroot);
    abstract public function documentExists(Kwf_Component_Data $page);
    abstract public function deleteDocument(Kwf_Component_Data $subroot, $componentId);

    public function getAllDocumentIds(Kwf_Component_Data $subroot)
    {
        return array_keys($this->getAllDocuments());
    }

    public function deleteAll(Kwf_Component_Data $subroot)
    {
        foreach ($this->getAllDocumentIds() as $id) {
            $this->deleteDocument($subroot, $id);
        }
    }

    public function getFulltextComponents(Kwf_Component_Data $component)
    {
        $fulltextComponents = $component->getRecursiveChildComponents(array('flag'=>'hasFulltext', 'inherit' => false, 'page'=>false));
        if (Kwc_Abstract::getFlag($component->componentClass, 'hasFulltext')) {
            $fulltextComponents[] = $component;
        }

        foreach ($fulltextComponents as $c) {
            if (!method_exists($c->getComponent(), 'getFulltextComponents')) continue;
            //components can return other components that should be included in fulltext content
            foreach ($c->getComponent()->getFulltextComponents() as $c) {
                $fulltextComponents = array_merge(
                    $fulltextComponents,
                    $this->getFulltextComponents($component)
                );
            }
        }
        return $fulltextComponents;
    }

    public function getFulltextContentForPage(Kwf_Component_Data $page, array $fulltextComponents = array())
    {
        if (Kwc_Abstract::getFlag($page->componentClass, 'skipFulltext')) return null;

        $c = $page;
        while($c = $c->parent) {
            if (Kwc_Abstract::getFlag($c->componentClass, 'skipFulltextRecursive')) return null;
        }

        if (!$fulltextComponents) {
            $fulltextComponents = $this->getFulltextComponents($page);
        }

        $ret = array();

        //whole content, for preview in search result
        $ret['content'] = '';


        //normal content with boost=1 goes here
        $ret['normalContent'] = '';

        $row = Kwc_FulltextSearch_MetaModel::getInstance()->getRow($page->componentId);
        if ($row && $row->changed_date) {
            $ret['lastModified'] = new Kwf_DateTime($row->changed_date);
        }

        $t = $page->getTitle();
        if (substr($t, -3) == ' - ') $t = substr($t, 0, -3);
        $ret['title'] = $t;

        foreach ($fulltextComponents as $c) {
            if (!method_exists($c->getComponent(), 'getFulltextContent')) continue;
            $content = $c->getComponent()->getFulltextContent();
            unset($c);
            foreach ($content as $field=>$text) {
                if (!$text) continue;
                if (isset($ret[$field])) {
                    if (is_string($ret[$field])) {
                        $ret[$field] = $ret[$field].' '.$text;
                    }
                } else {
                    $ret[$field] = $text;
                }
            }
        }

        if (!$ret['content']) {
            //echo " [no content]";
            $ret = null;
        }

        return $ret;
    }


    protected function _afterIndex(Kwf_Component_Data $page)
    {
        $m = Kwc_FulltextSearch_MetaModel::getInstance();
        $row = $m->getRow($page->componentId);
        if (!$row) {
            $row = $m->createRow();
            $row->page_id = $page->componentId;
        }
        $row->indexed_date = date('Y-m-d H:i:s');
        $row->save();
    }
}
