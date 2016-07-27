<?php
namespace KwfBundle\Rest;
use FOS\RestBundle\View\ViewHandler as ViewHandlerBase;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ViewHandler extends ViewHandlerBase
{
    /**
     * Handles creation of a Response using either redirection or the templating/serializer service.
     *
     * @param View    $view
     * @param Request $request
     * @param string  $format
     *
     * @return Response
     */
    public function createResponse(View $view, Request $request, $format)
    {
        $route = $view->getRoute();
        $location = $route
            ? $this->getRouter()->generate($route, (array) $view->getRouteParameters(), UrlGeneratorInterface::ABSOLUTE_URL)
            : $view->getLocation();

        if ($location) {
            return $this->createRedirectResponse($view, $location, $format);
        }

        $response = $this->initResponse($view, $format);

        if (!$response->headers->has('Content-Type')) {
            $response->headers->set('Content-Type', $request->getMimeType($format));
        }

        return $response;
    }

    /**
     * Initializes a response object that represents the view and holds the view's status code.
     *
     * @param View   $view
     * @param string $format
     *
     * @return Response
     */
    private function initResponse(View $view, $format)
    {
        $content = null;
        if ($this->isFormatTemplating($format)) {
            $content = $this->renderTemplate($view, $format);
        } elseif ($this->serializeNull || null !== $view->getData()) {
            $data = $this->getDataFromView($view);
            $serializer = $this->getSerializer($view);
            $context = $this->getContext($view);
            $content = $serializer->serialize($data, $format, $context);
        }

        $response = $view->getResponse();
        $response->setStatusCode($this->getStatusCode($view, $content));

        if (null !== $content) {
            $response->setContent($content);
        }

        return $response;
    }

    protected function getContext(View $view)
    {
        $context = array();
        if ($view instanceof \KwfBundle\Rest\View) {
            if ($view->getContext()) {
                $context = $view->getContext();
            }
        }
/*
        if ($context->attributes->get('groups')->isEmpty() && $this->exclusionStrategyGroups) {
            $context->setGroups($this->exclusionStrategyGroups);
        }

        if ($context->attributes->get('version')->isEmpty() && $this->exclusionStrategyVersion) {
            $context->setVersion($this->exclusionStrategyVersion);
        }

        if (null === $context->shouldSerializeNull() && null !== $this->serializeNullStrategy) {
            $context->setSerializeNull($this->serializeNullStrategy);
        }
*/
        return $context;
    }

    /**
     * Returns the data from a view. If the data is form with errors, it will return it wrapped in an ExceptionWrapper.
     *
     * @param View $view
     *
     * @return mixed|null
     */
    private function getDataFromView(View $view)
    {
        $form = $this->getFormFromView($view);

        if (false === $form) {
            return $view->getData();
        }

        if ($form->isValid() || !$form->isSubmitted()) {
            return $form;
        }

        /** @var ExceptionWrapperHandlerInterface $exceptionWrapperHandler */
        $exceptionWrapperHandler = $this->container->get('fos_rest.exception_handler');

        return $exceptionWrapperHandler->wrap(
            array(
                 'status_code' => $this->failedValidationCode,
                 'message' => 'Validation Failed',
                 'errors' => $form,
            )
        );
    }
}
