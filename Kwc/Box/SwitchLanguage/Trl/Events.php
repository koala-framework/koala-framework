<?php
class Kwc_Box_SwitchLanguage_Trl_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = array();
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Component_Event_Page_Added',
            'callback' => 'onPageAddedRemoved'
        );
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Component_Event_Page_Removed',
            'callback' => 'onPageAddedRemoved'
        );
        return $ret;
    }

    public function onPageAddedRemoved(Kwf_Component_Event_Component_Abstract $ev)
    {
        $master = $ev->component;
        if (is_instance_of($master->componentClass, 'Kwc_Chained_Trl_Component')) {
            $master = $master->chained;
        }
        $masterCc = Kwc_Abstract::getSetting($this->_class, 'masterComponentClass');
        foreach (Kwc_Chained_Abstract_Component::getAllChainedByMaster($master, 'Trl') as $page) {
            foreach ($page->getRecursiveChildComponents(array('componentClass' => $masterCc)) as $c) {
                //for child component, because this trl is MasterAsChild
                $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                    $masterCc, $c
                ));
            }
        }
    }
}
