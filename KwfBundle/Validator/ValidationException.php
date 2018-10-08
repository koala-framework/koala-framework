<?php
namespace KwfBundle\Validator;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use \Exception;

class ValidationException extends BadRequestHttpException
{
    protected $errors;

    /**
     * ValidationException constructor.
     * @param null $message
     * @param Exception|null $previous
     * @param int $code
     */
    public function __construct($message = null, Exception $previous = null, $code = 0)
    {
        if (!$message) $message = trlKwf('An error has occurred');

        parent::__construct($message, $previous, $code);
    }

    /**
     * @param array $errors
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
