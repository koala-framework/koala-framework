<?php
class Vpc_Forum_User_ViewPageFactory extends Vpc_Abstract_TablePageFactory
{
    protected $_tableName = 'Vpc_Forum_User_Model';

    protected function _init()
    {
        parent::_init();
        $childComponentClasses = Vpc_Abstract::getSetting(get_class($this->_component), 'childComponentClasses');

        $this->_componentClass = $childComponentClasses['view'];
    }

}
