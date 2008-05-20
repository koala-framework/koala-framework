<?php
class Vpc_Forum_SearchPageFactory extends Vpc_Abstract_StaticPageFactory
{
    protected $_pages = array(
        'search' => array(
            'showInMenu' => true,
            'name' => 'Search',
            'id' => 'search'
        )
    );

    protected function _init()
    {
        parent::_init();
        $childComponentClasses = Vpc_Abstract::getSetting(get_class($this->_component), 'childComponentClasses');

        $this->_pages['search']['componentClass'] = $childComponentClasses['search'];
        $this->_pages['search']['name'] = trlVps('Search');
    }
}