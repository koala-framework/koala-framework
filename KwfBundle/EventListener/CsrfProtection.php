<?php

namespace KwfBundle\EventListener;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Request;

class CsrfProtection
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if ($request->headers->get('X-Requested-With') !== 'XMLHttpRequest' &&
            $request->getMethod() !== Request::METHOD_OPTIONS) {

            throw new AccessDeniedHttpException('Missing X-Requested-With header');
        }
    }
}
