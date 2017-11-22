<?php

namespace KwfBundle\EventListener;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class CsrfProtection
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if ($request->headers->get('X-Requested-With') !== 'XMLHttpRequest') {
            throw new AccessDeniedHttpException('Missing X-Requested-With header');
        }
    }
}
