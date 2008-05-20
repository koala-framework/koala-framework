<?php
class Vpc_Forum_User_View_Images_PageFactory extends Vpc_Abstract_StaticPageFactory
{
    protected function _init()
    {
        parent::_init();
        $this->_pages[] = array(
            'componentClass' => $this->_getChildComponentClass('edit'),
            'showInMenu' => false,
            'name' => 'Edit Images',
            'id' => 'edit'
        );
    }

    protected function _decoratePage($page)
    {
        $decoratorName = $this->_component->getSetting(get_class($this->_component), 'loginDecorator');
        $dao = $this->_component->getDao();
        return new $decoratorName($dao, $page);
    }
}