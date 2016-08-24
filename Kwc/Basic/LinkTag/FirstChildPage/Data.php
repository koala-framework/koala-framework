<?php
class Kwc_Basic_LinkTag_FirstChildPage_Data extends Kwf_Component_Data
{
    private $_pageCache = false;
    public function __get($var)
    {
        if ($var == 'url') {
            $page = $this->_getFirstChildPage();
            return $page ? $page->url : '';
        } else if ($var == 'rel') {
            $page = $this->_getFirstChildPage();
            return $page ? $page->rel : '';
        } else {
            return parent::__get($var);
        }
    }

    public function getAbsoluteUrl()
    {
        $page = $this->_getFirstChildPage();
        return $page ? $page->getAbsoluteUrl() : '';
    }

    public function _getFirstChildPage()
    {
        if ($this->_pageCache === false) {
            // zuerst prüfen ob es eine händisch angelegte child page gibt
            $page = $this->getChildPage(array('pageGenerator' => true));
            if (!$page) {
                $page = $this->getChildPage(array('inherit'=>false),
                                        array(
                                            'inherit'=>false,
                                            'page'=>false
                                        ));
            }
            $this->_pageCache = $page;
        }
        return $this->_pageCache;
    }

    public function getLinkDataAttributes()
    {
        $page = $this->_getFirstChildPage();
        return $page ? $page->getLinkDataAttributes() : array();
    }

    public function getLinkClass()
    {
        $page = $this->_getFirstChildPage();
        return $page ? $page->getLinkClass() : array();
    }
}
