<?php
namespace KwfBundle\Validator;

use FOS\RestBundle\Exception\InvalidParameterException;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ErrorCollectValidator
{
    /**
     * @param ParamFetcher $paramFetcher
     * @return array
     */
    public function validate(ParamFetcher $paramFetcher)
    {
        $errors = array();

        foreach ($paramFetcher->getParams() as $param) {
            try {
                $paramFetcher->get($param->getName());
            } catch (InvalidParameterException $exception) {
                $errors[$param->getName()] = $this->parseViolations($exception->getViolations());
            } catch (BadRequestHttpException $exception) {
                $errors[$param->getName()] = array($exception->getMessage());
            }
        }

        return $errors;
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @throws ValidationException
     */
    public function validateAndThrow(ParamFetcher $paramFetcher)
    {
        $errors = $this->validate($paramFetcher);
        if (count($errors) > 0) {
            $exception = new ValidationException();
            $exception->setErrors($errors);
            throw $exception;
        }
    }

    /**
     * @param ConstraintViolationListInterface $violationList
     * @return array
     */
    private function parseViolations(ConstraintViolationListInterface $violationList)
    {
        $messages = array();

        foreach ($violationList as $violation) {
            $messages[] = $violation->getMessage();
        }

        return $messages;
    }
}
