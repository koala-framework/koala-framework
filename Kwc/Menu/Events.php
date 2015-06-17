<?php
class Kwc_Menu_Events extends Kwc_Menu_Abstract_Events
{
    protected $_emptyIfSingleEntry;

    //overwritten in Kwc_Menu_Trl_Events
    protected function _initSettings()
    {
        parent::_initSettings();
        $this->_emptyIfSingleEntry = $menuLevel = Kwc_Abstract::getSetting($this->_class, 'emptyIfSingleEntry');
    }

    //fire HasContentChanged here because hasContent is implemented in this component
    protected function _onMenuChanged(Kwf_Events_Event_Abstract $event, Kwf_Component_Data $menu)
    {
        parent::_onMenuChanged($event, $menu);
        $newCount = count($menu->getComponent()->getMenuData());

        if ($event instanceof Kwf_Component_Event_Page_Removed && $event->flag == Kwf_Component_Event_Page_Removed::FLAG_ROW_ADDED_REMOVED) {
            //if a row was ->delete()d it the the component is still in the component tree and returned by getMenuData()
            //so the newCount must be one less.
            //when changing the visibility it is not returned and $newCount is already correct.
            $newCount--;
        }

        if ($this->_emptyIfSingleEntry) $newCount--; //check with one less

        $previousCount = $newCount;
        if ($event instanceof Kwf_Component_Event_Page_Added) {
            $previousCount--;
        } else if ($event instanceof Kwf_Component_Event_Page_Removed) {
            $previousCount++;
        }
        if (!$previousCount && $newCount || $previousCount && !$newCount) {
            $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
                $this->_class, $menu
            ));
        }
    }
}
