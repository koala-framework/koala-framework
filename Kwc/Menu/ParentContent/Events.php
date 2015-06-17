<?php
class Kwc_Menu_ParentContent_Events extends Kwc_Abstract_Events //not Kwc_Basic_ParentContent_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_ParentChanged',
            'callback' => 'onPageParentChanged'
        );
        $ret[] = array(
            'class' => Kwc_Abstract::getSetting($this->_class, 'menuComponentClass'),
            'event' => 'Kwf_Component_Event_Component_HasContentChanged',
            'callback' => 'onMenuHasContentChanged'
        );
        return $ret;
    }

    public function onPageParentChanged(Kwf_Component_Event_Page_ParentChanged $event)
    {
        $menuLevel = Kwc_Abstract::getSetting(Kwc_Abstract::getSetting($this->_class, 'menuComponentClass'), 'level');

        $data = $event->component;
        $parentData = $event->oldParent;

        $level = 0;
        if ($data->isPage) $level++;
        $newCategoryData = $event->newParent;
        while ($newCategoryData && !Kwc_Abstract::getFlag($newCategoryData->componentClass, 'menuCategory')) {
            if ($newCategoryData->isPage) $level++;
            $newCategoryData = $newCategoryData->parent;
        }

        if (is_int($menuLevel)) {
            //numeric menu level
            if ($level+1 >= $menuLevel) {
                $l = $level + 1;
                if ($l != $menuLevel) {
                    if ($data->isPage) $l--;
                    $data = $parentData;
                    while ($data) {
                        if ($l == $menuLevel) {
                            break;
                        }
                        if ($data->isPage) $l--;
                        $data = $data->parent;
                    }
                } else {
                    $data = $parentData;
                }
                if ($data) {
                    $this->fireEvent(new Kwf_Component_Event_Component_RecursiveContentChanged(
                        $this->_class, $data
                    ));
                }
            }
        } else {
            //category menu level

            $oldCategoryData = $event->oldParent;
            while ($oldCategoryData && !Kwc_Abstract::getFlag($oldCategoryData->componentClass, 'menuCategory')) {
                $oldCategoryData = $oldCategoryData->parent;
            }

            if (!$newCategoryData || !$oldCategoryData) return;

            $newCat = Kwc_Abstract::getFlag($newCategoryData->componentClass, 'menuCategory');
            if ($newCat) {
                if ($newCat === true) $newCat = $newCategoryData->id;
            }

            $oldCat = Kwc_Abstract::getFlag($oldCategoryData->componentClass, 'menuCategory');
            if ($oldCat) {
                if ($oldCat === true) $oldCat = $oldCategoryData->id;
            }
            if ($newCat != $oldCat) {
                $this->fireEvent(new Kwf_Component_Event_Component_RecursiveContentChanged(
                    $this->_class, $data
                ));
            }
        }
    }

    public function onMenuHasContentChanged(Kwf_Component_Event_Component_HasContentChanged $event)
    {
        $c = $event->component;
        $this->fireEvent(new Kwf_Component_Event_Component_RecursiveHasContentChanged(
            $this->_class, $c
        ));
    }
}
