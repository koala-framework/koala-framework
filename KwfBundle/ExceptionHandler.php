<?php
namespace KwfBundle;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionHandler implements EventSubscriberInterface
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ($event->isPropagationStopped()) return;
        if ($event->getException() instanceof \Kwf_Exception_Abstract) {
            $event->stopPropagation();
            \Kwf_Debug::handleException($event->getException());
            exit;
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION => array('onKernelException')
        );
    }
}
