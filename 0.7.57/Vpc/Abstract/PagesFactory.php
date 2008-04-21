<?php
class Vpc_Abstract_PagesFactory extends Vpc_Abstract_PageFactory
{
    protected $_additionalFactories = array();
    /**
     * Falls eine Komponente Unterseiten im Seitenbaum erstellt, wird das hier gemacht.
     *
     * Standardmäßig werden die Seiten aus dem als Unterseite im Seitenbaum hinzugefügt. Falls
     * eine Komponente dynamisch Unterseiten erstellen will, sollte das in dieser Methode erfolgen.
     * parent::getChildPages sollte dennoch aufgerufen werden.
     *
     * @return Array mit erstellten Unterseiten
     */
    public function getChildPages()
    {
        $ret = array();
        foreach ($this->_childPagesData() as $pageRow) {
            if (!$this->getPageCollection()->getExistingPageById($pageRow['id'])) {
                $ret[] = $this->_createChildPageByPageRow($pageRow);
            }
        }
        foreach ($this->_component->getChildComponents() as $p) {
            $ret = array_merge($ret, $p->getPageFactory()->getChildPages());
        }
        return $ret;
    }

    public function getMenuChildPages()
    {
        $ret = array();
        foreach ($this->_childPagesData() as $pageRow) {
            if (!$pageRow['hide']) {
                $ret[] = $this->_createChildPageByPageRow($pageRow);
            }
        }

        foreach ($this->_component->getChildComponents() as $p) {
            $ret = array_merge($ret, $p->getPageFactory()->getMenuChildPages());
        }
        return $ret;
    }

    private function _childPagesData()
    {
        if (!isset($this->_childPagesDataCache)) {
            $t = $this->_component->getDao()->getTable('Vps_Dao_Pages');
            $this->_childPagesDataCache = $t->retrieveChildPagesData($this->_component->getId());
        }
        return $this->_childPagesDataCache;
    }

    private function _createChildPageByPageRow($pageRow)
    {
        if ($p = $this->getPageCollection()->getExistingPageById($pageRow['id'])) {
            return $p;
        }
        $class = $pageRow['component_class'];
        $page = Vpc_Abstract::createInstance(
            $this->_component->getDao(),
            $pageRow['component_class'],
            $pageRow['id'],
            $this->getPageCollection()
        );
        $this->getPageCollection()->addTreePage($page, $pageRow['filename'], $pageRow['name'], $this->_component);
        return $page;
    }

    public function getChildPageById($id)
    {
        if ($p = $this->getPageCollection()->getExistingPageById($this->_component->getId().'_'.$id)) {
            return $p;
        }
        foreach ($this->_childPagesData() as $pageRow) {
            if ($id != $pageRow['id']) continue;
            return $this->_createChildPageByPageRow($pageRow);
        }
        foreach ($this->_component->getChildComponents() as $p) {
            if ($cp = $p->getPageFactory()->getChildPageById($id)) {
                return $cp;
            }
        }
        return null;
    }

    public function getChildPageByFilename($filename)
    {
        if ($p = $this->getPageCollection()->getExistingChildPageByFilename($this->_component, $filename)) {
            return $p;
        }
        foreach ($this->_childPagesData() as $pageRow) {
            if ($filename != '' && $filename != $pageRow['filename']) continue;
            return $this->_createChildPageByPageRow($pageRow);
        }
        foreach ($this->_component->getChildComponents() as $p) {
            if ($cp = $p->getPageFactory()->getChildPageByFilename($filename)) {
                return $cp;
            }
        }
        return null;
    }
}
