<?php
namespace KwfBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionHandler
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ($event->isPropagationStopped()) return;
        if ($event->getException() instanceof \Exception) {
            $event->stopPropagation();
            \Kwf_Debug::handleException($event->getException());
            exit;
        }
    }
}
