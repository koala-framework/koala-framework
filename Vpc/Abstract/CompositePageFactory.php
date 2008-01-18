<?php
abstract class Vpc_Abstract_CompositePageFactory extends Vpc_Abstract_PageFactory
{
    protected $_pageFactories = array();

//     protected _init()
//     {
//         $this->_pageFactories[] = new Vpc_Abstract_StaticPageFactory($this->_component);
//         $this->_pageFactories[] = new Vpc_Abstract_TablePageFactory($this->_component);
//     }

    public function getChildPages()
    {
        $ret = parent::getChildPages();
        foreach ($this->_pageFactories as $f) {
            $ret = array_merge($ret, $f->getChildPages());
        }
        return $ret;
    }

    public function getMenuChildPages()
    {
        $ret = parent::getMenuChildPages();
        foreach ($this->_pageFactories as $f) {
            $ret = array_merge($ret, $f->getMenuChildPages());
        }
        return $ret;
    }

    public function getChildPageById($id)
    {
        foreach ($this->_pageFactories as $f) {
            if ($page = $f->getChildPageById($id)) {
                return $page;
            }
        }
        return parent::getChildPageById($id);
    }
    public function getChildPageByFilename($filename)
    {
        foreach ($this->_pageFactories as $f) {
            if ($page = $f->getChildPageByFilename($filename)) {
                return $page;
            }
        }
        return parent::getChildPageByFilename($filename);
    }
}
