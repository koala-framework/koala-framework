<?php

namespace KwfBundle\EventListener;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Request;

class CsrfProtection
{
    /**
     * @var array
     */
    private $ignorePaths;

    public function __construct(array $ignorePaths = array())
    {
        $this->ignorePaths = $ignorePaths;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (!$this->shouldIgnoreUrl($request) && $request->headers->get('X-Requested-With') !== 'XMLHttpRequest' &&
            $request->getMethod() !== Request::METHOD_OPTIONS) {

            throw new AccessDeniedHttpException('Missing X-Requested-With header');
        }
    }

    protected function shouldIgnoreUrl(Request $request)
    {
        $ret = false;

        foreach ($this->ignorePaths as $ignorePath) {
            if (!preg_match('{' . $ignorePath . '}i', $request->getRequestUri())) continue;

            $ret = true;
            break;
        }

        return $ret;
    }
}
