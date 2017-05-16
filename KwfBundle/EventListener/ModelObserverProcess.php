<?php
namespace KwfBundle\EventListener;
class ModelObserverProcess
{
    public function onKernelTerminate()
    {
        \Kwf_Events_ModelObserver::getInstance()->process();
    }
}
