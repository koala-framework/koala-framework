<?php
class Vpc_Basic_LinkTag_FirstChildPage_Data extends Vps_Component_Data
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
}
