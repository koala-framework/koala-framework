<?php
namespace KwfBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Bundle\TwigBundle\Controller\ExceptionController;
use KwfBundle\Validator\ValidationException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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

        $acceptsJson = in_array('application/json', $event->getRequest()->getAcceptableContentTypes());
        $exception = $event->getException();
        if ($exception instanceof ValidationException) {
            if ($acceptsJson) {
                $event->setResponse(new JsonResponse(array(
                    'error' => array(
                        'code' => $exception->getStatusCode(),
                        'message' => $exception->getMessage(),
                        'errors' => $exception->getErrors()
                    )
                ), $exception->getStatusCode()));
            } else {
                $event->setResponse(new Response($exception->getMessage(), $exception->getStatusCode()));
            }
        } else if ($exception instanceof HttpExceptionInterface) {
            $request = $event->getRequest();
            if ($acceptsJson) {
                $request->setRequestFormat('json');
            }

            $flattenException = FlattenException::create($exception);
            $event->setResponse($this->controller->showAction($request, $flattenException));
        } else if ($exception instanceof \Exception) {
            $event->stopPropagation();
            \Kwf_Debug::handleException($exception instanceof AccessDeniedException ? new \Kwf_Exception_AccessDenied() : $event->getException());
            exit;
        }
    }
}
