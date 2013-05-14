<?php
class Kwc_Basic_LinkTag_FirstChildPage_Cc_Events extends Kwc_Basic_LinkTag_FirstChildPage_Events
{
    public function onRecursiveUrlChanged(Kwf_Component_Event_Page_RecursiveUrlChanged $event)
    {
        $component = $event->component->parent;

        while($component && is_instance_of($component->componentClass, 'Kwc_Basic_LinkTag_FirstChildPage_Cc_Component')) {
            if ($component->componentClass == $this->_class) {
                $this->fireEvent(new Kwf_Component_Event_Page_UrlChanged(
                    $this->_class, $component
                ));
            }
            $component = $component->parent;
        }
    }

}
