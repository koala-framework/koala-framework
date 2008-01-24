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
                    throw new Vps_Exception('News category pageFactory not set (key: '.$key.')');
                }
                $this->_additionalFactories[$key] = new $category['pageFactory']($this->_component);
            }
        }
        $this->_additionalFactories['details'] = new Vpc_News_PageFactoryDetails($this->_component);
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