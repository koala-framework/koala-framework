<?php
class Vpc_Posts_PageFactory extends Vpc_Abstract_StaticPageFactory
{
    protected $_pages = array(
        'write'=> array(
            'showInMenu' => false,
            'name' => 'New Post',
            'id' => 'write'
        )
    );
    protected function _init()
    {
        parent::_init();
        $childComponentClasses = Vpc_Abstract::getSetting(get_class($this->_component), 'childComponentClasses');
        $this->_pages['write']['componentClass'] = $childComponentClasses['write'];
    }

    protected function _decoratePage($page)
    {
        if ($this->_component instanceof Vpc_Forum_Posts_Component) {
            $forumComponent = $this->_component->getForumComponent();
            $decoratorName = $forumComponent->getSetting(get_class($forumComponent), 'loginDecorator');
        } else {
            $decoratorName = $this->_component->getSetting(get_class($this->_component), 'loginDecorator');
        }
        $dao = $this->_component->getDao();
        return new $decoratorName($dao, $page);
    }
}