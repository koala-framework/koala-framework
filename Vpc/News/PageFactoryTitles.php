<?php
class Vpc_News_PageFactoryTitles extends Vpc_Abstract_StaticPageFactory
{
    protected $_pages = array(array(
        'id'=>'titles',
        'name'=>'Titel',
        'showInMenu' => false,
        'componentClass' => 'Vpc_News_Titles_Component'
    ));
    protected $_additionalFactories = array();

    protected function _init()
    {
        parent::_init();
        $childComponentClasses = Vpc_Abstract::getSetting(get_class($this->_component), 'childComponentClasses');
        $this->_pages[0]['componentClass'] = $childComponentClasses['titles'];
    }
}
