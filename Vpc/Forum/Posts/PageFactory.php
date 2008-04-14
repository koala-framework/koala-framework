<?php
class Vpc_Forum_Posts_PageFactory extends Vpc_Posts_PageFactory
{

    protected function _init()
    {
        parent::_init();
        $childComponentClasses = Vpc_Abstract::getSetting(get_class($this->_component), 'childComponentClasses');

        $this->_pages['observe'] = array(
            'showInMenu' => false,
            'name'       => 'Observe',
            'id'         => 'observe',
            'componentClass' => $childComponentClasses['observe']
        );
    }
}