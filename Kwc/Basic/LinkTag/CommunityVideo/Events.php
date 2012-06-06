<?php
class Kwc_Basic_LinkTag_CommunityVideo_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $g = Kwc_Abstract::getSetting($this->_class, 'generators');
        $ret[] = array(
            'class' => $g['video']['component'],
            'event' => 'Kwf_Component_Event_Component_HasContentChanged',
            'callback' => 'onVideoHasContentChanged'
        );
        return $ret;
    }

    public function onVideoHasContentChanged(Kwf_Component_Event_Component_HasContentChanged $ev)
    {
        foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($ev->dbId) as $c) {
            if ($c->parent->componentClass != $this->_class) continue;
            $this->fireEvent(
                new Kwf_Component_Event_Component_HasContentChanged($this->_class, $c->parent->dbId)
            );
            $this->fireEvent(new Kwf_Component_Event_Page_UrlChanged($this->_class, $c->parent->dbId));
        }
    }
}
