<?php
class Kwc_Basic_DownloadTag_Trl_Events extends Kwc_Basic_LinkTag_Abstract_Trl_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => Kwc_Abstract::getSetting($this->_class, 'masterComponentClass'),
            'event' => 'Kwf_Events_Event_Media_Changed',
            'callback' => 'onMasterMediaCacheChanged'
        );
        return $ret;
    }

    public function onMasterMediaCacheChanged(Kwf_Events_Event_Media_Changed $e)
    {
        foreach (Kwc_Chained_Trl_Component::getAllChainedByMaster($e->component, 'Trl') as $chained) {
            if (!$chained->getComponent()->getRow()->own_download) {
                $this->fireEvent(new Kwf_Events_Event_Media_Changed(
                    $this->_class, $chained
                ));
            }
        }
    }
}
