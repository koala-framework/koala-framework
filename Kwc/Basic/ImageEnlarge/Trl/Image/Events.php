<?php
class Kwc_Basic_ImageEnlarge_Trl_Image_Events extends Kwc_Abstract_Image_Trl_Image_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'event' => 'Kwc_Basic_ImageEnlarge_EnlargeTag_Trl_AlternativePreviewChangedEvent',
            'callback' => 'onAlternativePreviewChanged'
        );

        $masterImageCC = Kwc_Abstract::getSetting($this->_class, 'masterImageComponentClass');
        $ret[] = array(
            'class' => $masterImageCC,
            'event' => 'Kwf_Component_Event_Media_Changed',
            'callback' => 'onMasterMediaChanged'
        );
        return $ret;
    }

    //in ImageEnlarge trl we use always this image child component, and that eventually gets image data from master
    public function onMasterMediaChanged(Kwf_Component_Event_Media_Changed $event)
    {
        $chained = Kwc_Chained_Abstract_Component::getAllChainedByMaster($event->component, 'Trl', array('ignoreVisible'=>true));
        foreach ($chained as $c) {
            $c = $c->getChildComponent('-image');
            if ($c && $c->componentClass == $this->_class) {
                $this->fireEvent(new Kwf_Component_Event_Media_Changed(
                    $this->_class, $c
                ));
                //content changed in parent
            }
        }
    }

    public function onAlternativePreviewChanged(Kwc_Basic_ImageEnlarge_EnlargeTag_Trl_AlternativePreviewChangedEvent $event)
    {
        $component = $event->component->parent;
        if (is_instance_of($component->componentClass, 'Kwc_Basic_LinkTag_Trl_Component')) {
            $component = $component->parent;
        }
        $component = $component->getChildComponent('-image');
        if ($component && $component->componentClass == $this->_class) {
            $this->fireEvent(new Kwf_Component_Event_Media_Changed(
                $this->_class, $component
            ));
            //content changed in parent
        }
    }
}
