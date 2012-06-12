<?php
class Kwc_Box_InheritContent_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = array();
        $ret[] = array(
            'class' => Kwc_Abstract::getChildComponentClass($this->_class, 'child'),
            'event' => 'Kwf_Component_Event_Component_HasContentChanged',
            'callback' => 'onChildHasContentChange'
        );
        return $ret;
    }

    public function onChildHasContentChange(Kwf_Component_Event_Component_HasContentChanged $event)
    {
        $component = $event->component;;
        $ic = $component->parent;
        if ($ic->componentClass == $this->_class) {

            // always throw contentChanged
            $this->fireEvent(
                new Kwf_Component_Event_Component_RecursiveContentChanged(
                    $ic->componentClass, $ic
                )
            );

            // throw hasContentChanged only when parent-inherit-content has no Content
            $parentIc = $this->_getParentIc($ic);
            if (!$parentIc || !$parentIc->getComponent()->hasContent()) {
                $this->fireEvent(
                    new Kwf_Component_Event_Component_RecursiveHasContentChanged(
                        $ic->componentClass, $ic
                    )
                );
            }
        }
    }

    private function _getParentIc($component)
    {
        $ret = null;
        $idParts = array();
        $page = $component;
        while ($page && !$page->isPage && $page->parent) {
            $idParts[] = $page->id;
            $page = $page->parent;
        }
        $parentPage = $page->parent;
        if ($parentPage) {
            $ret = $parentPage;
            foreach ($idParts as $part) {
                $ret = $ret->getChildComponent('-' . $part);
            }
        }
        return $ret;
    }
}
