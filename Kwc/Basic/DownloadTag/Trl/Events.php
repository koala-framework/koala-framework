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
        if ($e->component->parent->componentClass == $this->_class) return;
        foreach (Kwc_Chained_Trl_Component::getAllChainedByMaster($e->component, 'Trl') as $chained) {
            $row = $chained->getComponent()->getRow();
            if (!isset($row->own_download) || !$row->own_download) {
                $this->fireEvent(new Kwf_Events_Event_Media_Changed(
                    $this->_class, $chained
                ));
            }
        }
    }

    public function onMasterOwnRowUpdate(Kwf_Events_Event_Row_Abstract $event)
    {
        parent::onMasterOwnRowUpdate($event);
        $cmps = Kwf_Component_Data_Root::getInstance()->getComponentsByDbId(
            $event->row->component_id, array('ignoreVisible'=>true)
        );
        foreach ($cmps as $c) {
            $chainedType = 'Trl';
            $select = array('ignoreVisible' => true);
            $chained = Kwc_Chained_Abstract_Component::getAllChainedByMaster($c, $chainedType, $select);
            foreach ($chained as $i) {
                if ($i->componentClass != $this->_class) { continue; } //like in parent::onMasterOwnRowUpdate
                if ($i->getComponent()->getRow()->own_download) continue;

                $this->fireEvent(new Kwf_Component_Event_Page_UrlChanged(
                    $this->_class, $i
                ));
            }
        }
    }
}
