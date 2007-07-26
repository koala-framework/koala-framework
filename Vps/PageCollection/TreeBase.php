<?php
class Vps_PageCollection_TreeBase extends Vps_PageCollection_Tree
{
    public function getComponentById($pageId)
    {
        $this->getRootPage(); // Muss hier gemacht werden
        if (!isset($this->_pages[$pageId])) {
            try {
                $parts = Vpc_Abstract::parsePageId($pageId);
                $page = $this->addPage($parts['topComponentId']);
                if ($page != null) {
                    $id = $page->getPageId();
                    foreach ($parts['pageKeys'] as $pageKey) {
                        $this->_pages[$id]->generateHierarchy($pageKey);
                        $id .= $id == $page->getPageId() ? '_' : '.';
                        $id .= $pageKey;
                    }
                }
            } catch (Vpc_Exception $e) {
                return null;
            }
        }

        if (isset($this->_pages[$pageId])) {
            return $this->_pages[$pageId];
        } else {
            return null;
        }
    }

    public function getUrl($page)
    {
        return '/component/show/' . $page->getId() . '/';
    }

    protected function _addDecorators(Vpc_Interface $page)
    {
        return $page;
    }
}