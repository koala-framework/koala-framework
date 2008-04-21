<?php
class Vpc_News_PageFactoryMonths extends Vpc_Abstract_StaticPageFactory implements Vpc_News_Interface_PageFactoryCategory
{
    protected $_pages = array(array(
        'id'=>'months',
        'name'=>'Monate',
        'showInMenu' => false
    ));
    protected $_additionalFactories = array();

    public function setComponentClass($class)
    {
        $this->_pages[0]['componentClass'] = $class;
    }

}
