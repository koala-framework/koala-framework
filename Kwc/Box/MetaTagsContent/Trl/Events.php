<?php
class Kwc_Box_MetaTagsContent_Trl_Events extends Kwc_Box_MetaTags_Trl_Events
{
    protected function _onOwnRowUpdate(Kwf_Component_Data $c, Kwf_Events_Event_Row_Abstract $event)
    {
        parent::_onOwnRowUpdate($c, $event);
        if (Kwc_Abstract::getFlag($c->parent->componentClass, 'subroot') || $c->parent instanceof Kwf_Component_Data_Root) {
            $this->fireEvent(new Kwf_Component_Event_Component_RecursiveContentChanged(
                $this->_class, $c
            ));
        }
    }
}
