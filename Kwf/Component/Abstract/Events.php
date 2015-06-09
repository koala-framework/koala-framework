<?php
class Kwf_Component_Abstract_Events extends Kwf_Events_Subscriber
{
    protected $_class;

    protected function _init()
    {
        $this->_class = $this->_config['componentClass'];
    }

    /**
     * Returns all component classes that can create $createdClass. Optionally filtering instances of $createClass.
     */
    protected function _getCreatingClasses($createdClass, $createClass = null)
    {
        $ret = array();
        foreach (Kwc_Abstract::getComponentClasses() as $c) {
            if (!$createClass || in_array($createClass, Kwc_Abstract::getParentClasses($c))) {
                if (Kwc_Abstract::getChildComponentClasses($c, array('componentClass'=>$createdClass))) {
                    $ret[] = $c;
                }
            }
        }
        return $ret;
    }
}
