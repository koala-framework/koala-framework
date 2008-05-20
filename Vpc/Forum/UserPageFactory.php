<?php
class Vpc_Forum_UserPageFactory extends Vpc_Abstract_StaticPageFactory
{
    protected $_pages = array(
        'user' => array(
            'showInMenu' => true,
            'id' => 'users'
        )
    );

    protected function _init()
    {
        parent::_init();
        $childComponentClasses = Vpc_Abstract::getSetting(get_class($this->_component), 'childComponentClasses');

        $this->_pages['user']['componentClass'] = $childComponentClasses['user'];
        $this->_pages['user']['name'] = trlVps('Users');
    }
}