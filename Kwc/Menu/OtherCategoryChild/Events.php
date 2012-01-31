<?php
class Kwc_Menu_OtherCategoryChild_Events extends Kwc_Abstract_Events //not Kwc_Basic_ParentContent_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => Kwc_Abstract::getSetting($this->_class, 'menuComponentClass'),
            'event' => 'Kwf_Component_Event_Component_HasContentChanged',
            'callback' => 'onMenuHasContentChanged'
        );
        return $ret;
    }

    public function onMenuHasContentChanged(Kwf_Component_Event_Component_HasContentChanged $event)
    {
        foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($event->dbId) as $c) {
            $menu = $c->getParentPageOrRoot()->getChildComponent('-'.$c->id);
            if ($menu) {
                $this->fireEvent(new Kwf_Component_Event_Component_RecursiveHasContentChanged(
                    $this->_class, $menu->componentId
                ));
            }
        }
    }
}
