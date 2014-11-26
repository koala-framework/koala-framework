<?php
class Kwf_Model_Events_Basic_EventSubscriber extends Kwf_Model_EventSubscriber
{
    public static $onTestEventCalled;
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Model_Events_Basic_TestEvent',
            'callback' => 'onTestEvent'
        );
        return $ret;
    }

    public function onTestEvent(Kwf_Model_Events_Basic_TestEvent $ev)
    {
        self::$onTestEventCalled++;
    }
}
