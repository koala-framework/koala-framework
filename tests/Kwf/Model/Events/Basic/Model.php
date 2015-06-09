<?php
class Kwf_Model_Events_Basic_Model extends Kwf_Model_FnF
{
    public function getEventSubscribers()
    {
        $ret = parent::getEventSubscribers();
        $ret[] = Kwf_Model_EventSubscriber::getInstance('Kwf_Model_Events_Basic_EventSubscriber', array(
            'modelFactoryConfig' => $this->getFactoryConfig()
        ));
        return $ret;
    }
}
