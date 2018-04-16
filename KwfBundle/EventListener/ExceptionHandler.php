<?php
namespace KwfBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Bundle\TwigBundle\Controller\ExceptionController;

class ExceptionHandler
{
    private $controller;

    public function __construct(ExceptionController $controller)
    {
        $this->controller = $controller;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ($event->isPropagationStopped()) return;

        $exception = $event->getException();
        if ($exception instanceof HttpExceptionInterface) {
            $request = $event->getRequest();
            if (in_array('application/json', $request->getAcceptableContentTypes())) {
                $request->setRequestFormat('json');
            }

            $flattenException = FlattenException::create($exception);
            $event->setResponse($this->controller->showAction($request, $flattenException));
        } else if ($exception instanceof \Exception) {
            $event->stopPropagation();
            \Kwf_Debug::handleException($event->getException());
            exit;
        }
    }
}
