<?php
class Kwc_Box_TitleEditable_Events extends Kwc_Box_Title_Events
{
    protected function _onOwnRowUpdate(Kwf_Component_Data $c, Kwf_Events_Event_Row_Abstract $event)
    {
        parent::_onOwnRowUpdate($c, $event);
        $page = $c;
        while ($page && !$page->inherits) $page = $page->parent;
        if (Kwc_Abstract::getFlag($page->componentClass, 'subroot') || $page instanceof Kwf_Component_Data_Root) {
            $this->fireEvent(new Kwf_Component_Event_Component_RecursiveContentChanged(
                $this->_class, $c
            ));
        }
    }
}
