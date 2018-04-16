<?php
namespace KwfBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ExceptionHandler
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ($event->isPropagationStopped()) return;

        $exception = $event->getException();
        if ($exception instanceof HttpExceptionInterface) {
            if (in_array('application/json', $event->getRequest()->getAcceptableContentTypes())) {
                $response = new JsonResponse();
                if ($message = $exception->getMessage()) {
                    $response->setData(array(
                        'message' => $message
                    ));
                }
            } else {
                $response = new Response();
                $response->setContent($exception->getMessage());
            }

            $response->setStatusCode($exception->getStatusCode());
            $event->setResponse($response);
        } else if ($exception instanceof \Exception) {
            $event->stopPropagation();
            \Kwf_Debug::handleException($event->getException());
            exit;
        }
    }
}
