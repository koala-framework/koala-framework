<?php
abstract class Vpc_Abstract_StaticPageFactory extends Vpc_Abstract_PageFactory
{
    protected $_pages;
    protected $_additionalPageFactories = array('Vpc_Abstract_PagesFactory');

    public function getChildPages()
    {
        $ret = parent::getChildPages();
        foreach ($this->_pages as $p) {
            $ret[] = $this->_createStaticPage($p);
        }
        return $ret;
    }

    public function getMenuChildPages()
    {
        $ret = parent::getMenuChildPages();

        foreach ($this->_pages as $p) {
            if (!isset($p['showInMenu']) || $p['showInMenu']) {
                $ret[] = $this->_createStaticPage($p);
            }
        }
        return $ret;
    }

    public function getChildPageByFilename($filename)
    {
        foreach ($this->_pages as $p) {
            if ($filename == $this->_getFilenameByP($p)) {
                return $this->_createStaticPage($p);
            }
        }
        return parent::getChildPageByFilename($filename);
    }

    public function getChildPageById($id)
    {
        foreach ($this->_pages as $p) {
            if ($id == $this->_getIdByP($p)) {
                return $this->_createStaticPage($p);
            }
        }
        return parent::getChildPageById($id);
    }

    private function _getFilenameByP($p)
    {
        if (isset($p['filename'])) return $p['filename'];
        if (!isset($p['name'])) {
            throw new Vps_Exception(trlVps("'name' is required in _pages array"));
        }
        $filter = new Vps_Filter_Url();
        return $filter->filter($p['name']);
    }

    private function _getIdByP($p)
    {
        if (isset($p['id'])) return $p['id'];
        return $this->_getFilenameByP($p);
    }

    protected function _createStaticPage($p)
    {
        $pc = $this->getPageCollection();

        // Gibt's die Page schon?
        $id = $this->_component->getId() . '_' . $this->_getIdByP($p);
        if ($page = $pc->getExistingPageById($id)) {
            return $page;
        }

        // Page erstellen
        if (!isset($p['componentClass'])) {
            throw new Vps_Exception(trlVps("'componentClass' is required in _pages array"));
        }
        $page = $this->_createPage($p['componentClass'], $this->_getIdByP($p));

        // Page hinzufügen
        $pc->addTreePage($page, $this->_getFilenameByP($p), $p['name'], $this->_component);

        // Page zurückgeben
        return $page;
    }
}
