<?php
class Kwf_Component_PagesMetaRow extends Kwf_Model_Proxy_Row
{
    private static function _canHaveFulltext($class)
    {
        static $cache = array();
        if (isset($cache[$class])) return $cache[$class];
        $cache[$class] = false;
        if (Kwc_Abstract::getFlag($class, 'skipFulltext')) {
            return $cache[$class]; //false
        }
        if (Kwc_Abstract::getFlag($class, 'hasFulltext')) {
            $cache[$class] = true;
            return $cache[$class];
        }
        foreach (Kwc_Abstract::getChildComponentClasses($class, array('pseudoPage'=>false)) as $c) {
            if (self::_canHaveFulltext($c)) {
                $cache[$class] = true;
                return $cache[$class];
            }
        }
        return $cache[$class]; //false
    }

    private function _getFulltextSkip(Kwf_Component_Data $page)
    {
        if (Kwc_Abstract::getFlag($page->componentClass, 'skipFulltext')) {
            return true;
        }
        if (Kwc_Abstract::getFlag($page->componentClass, 'skipFulltextRecursive')) {
            return true;
        }
        if (!self::_canHaveFulltext($page->componentClass)) {
            return true;
        }
        $c = $page->parent;
        while ($c) {
            if (Kwc_Abstract::getFlag($c->componentClass, 'skipFulltextRecursive')) {
                return true;
            }
            $c = $c->parent;
        }
        return false;
    }

    private function _getMetaNoIndex(Kwf_Component_Data $page)
    {
        if (Kwc_Abstract::getFlag($page->componentClass, 'noIndex')) {
            return true;
        }

        $c = $page;
        $onlyInherit = false;
        while ($c) {
            $p = Kwc_Abstract::getSetting($c->componentClass, 'pluginsInherit');
            if (!$onlyInherit) {
                $p = array_merge($p, Kwc_Abstract::getSetting($c->componentClass, 'plugins'));
            }
            foreach ($p as $plugin) {
                if (is_instance_of($plugin, 'Kwf_Component_Plugin_Interface_Login')) {
                    return true;
                }
            }
            if ($c->isPage) {
                $onlyInherit = true;
            }
            $c = $c->parent;
        }

        return false;
    }

    public function updateFromPage(Kwf_Component_Data $page)
    {
        $this->deleted = !$page->isVisible();
        $this->page_id = $page->componentId;
        $this->expanded_component_id = $page->getExpandedComponentId();
        $domainCmp = $page->getDomainComponent();
        $this->domain_component_id = $domainCmp ? $domainCmp->componentId : null;
        $this->subroot_component_id = $page->getSubroot()->componentId;
        $this->url = $page->getAbsoluteUrl();
        if (!$this->url) $this->url = '';

        $this->sitemap_priority = '0.5';
        $this->sitemap_changefreq = 'weekly';
        $noindex = false;
        foreach ($page->getRecursiveChildComponents(array('flag'=>'hasPageMeta')) as $c) {
            $pageMeta = $c->getComponent()->getPageMeta();
            $this->sitemap_priority = $pageMeta['sitemap_priority'];
            $this->sitemap_changefreq = $pageMeta['sitemap_changefreq'];
            $noindex = $pageMeta['noindex'];
        }

        $this->meta_noindex = $noindex || $this->_getMetaNoIndex($page);
        $this->fulltext_skip = $this->_getFulltextSkip($page);
    }

    public function deleteRecursive()
    {
        if (is_numeric($this->page_id)) {
            $page = Kwf_Component_Data_Root::getInstance()->getComponentById($this->page_id, array('ignoreVisible'=>true));
            if ($page) {
                $pages = $page->getChildPages(array(
                    'pageGenerator' => true
                ));
                foreach ($pages as $p) {
                    $row = $this->getModel()->getRow($p->componentId);
                    if ($row) $row->deleteRecursive();
                }
            }
        }

        $deleteSelect = new Kwf_Model_Select();
        $deleteSelect->where(new Kwf_Model_Select_Expr_Like('expanded_component_id', $this->page_id.'%'));
        $this->getModel()->updateRows(array('deleted'=>true), $deleteSelect);
    }
}
