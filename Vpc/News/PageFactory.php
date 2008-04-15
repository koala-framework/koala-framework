<?php
class Vpc_News_PageFactory extends Vpc_Abstract_PageFactory
{
    protected function _init()
    {
        parent::_init();
        $categories = Vpc_Abstract::getSetting(get_class($this->_component), 'categories');
        if ($categories) {
            foreach ($categories as $key => $category) {
                if (!isset($category['pageFactory'])) {
                    throw new Vps_Exception(trlVps('News category pageFactory not set (key: {0})', $key));
                }
                $this->_additionalFactories[$key] = new $category['pageFactory']($this->_component);
                if ($this->_additionalFactories[$key] instanceof Vpc_News_Interface_PageFactoryCategory) {
                    $childComponentClasses = Vpc_Abstract::getSetting(get_class($this->_component), 'childComponentClasses');
                    $this->_additionalFactories[$key]->setComponentClass($childComponentClasses[$key]);
                }
            }
        }
        $this->_additionalFactories['details'] = new Vpc_News_PageFactoryDetails($this->_component);
        $childComponentClasses = Vpc_Abstract::getSetting(get_class($this->_component), 'childComponentClasses');
        if (!empty($childComponentClasses['titles'])) {
            $this->_additionalFactories['titles'] = new Vpc_News_PageFactoryTitles($this->_component);
        }
        $this->_additionalFactories['feed'] = new Vpc_News_PageFactoryFeed($this->_component);
    }

    public function getChildPageByNewsRow($row)
    {
        return $this->_additionalFactories['details']->getChildPageByRow($row);
    }

    public function getCategoryPages()
    {
        $ret = array();
        $categories = Vpc_Abstract::getSetting(get_class($this->_component), 'categories');
        if ($categories) {
            foreach ($categories as $key => $category) {
                $ret = array_merge($ret, $this->_additionalFactories[$key]->getChildPages());
            }
        }
        return $ret;
    }
}