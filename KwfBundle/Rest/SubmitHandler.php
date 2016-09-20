<?php
namespace KwfBundle\Rest;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

class SubmitHandler
{
    protected $data;
    protected $fields;
    protected $errors;
    protected $submitted = false;
    protected $validator;

    public function __construct(array $fields)
    {
        foreach ($fields as $k=>$i) {
            if (is_string($i)) $fields[$k] = array('name'=>$i);
        }
        $this->fields = $fields;
    }

    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function submit($data, array $submitData)
    {
        $this->data = $data;
        $this->submitted = true;
        $this->errors = array();

        foreach ($this->fields as $field) {
            $value = $this->data->{$field['name']};
            if (isset($submitData[$field['name']])) {
                $value = $submitData[$field['name']];
            }
            if (isset($field['constraints'])) {
                $violations = $this->validator->validate($value, $field['constraints']);
                if (count($violations)) {
                    foreach ($violations as $violation) {
                        $this->errors[] = array(
                            'field' => $field['name'],
                            'violation' => $violation
                        );
                    }
                }
            }
        }
        if (!count($this->errors)) {
            foreach ($this->fields as $field) {
                if (isset($submitData[$field['name']])) {
                    $field['columnNormalizer']->denormalize($submitData[$field['name']], $this->data, $field['name'], $field['settings']);
                }
            }
        }
    }

    public function isSubmitted()
    {
        return $this->submitted;
    }

    public function isValid()
    {
        if (!$this->submitted) throw new \Exception("Form not yet submitted");
        return !count($this->errors);
    }

    public function getErrors()
    {
        if (!$this->submitted) throw new \Exception("Form not yet submitted");
        return $this->errors;
    }

    public function addError(ConstraintViolationInterface $violation)
    {
        $this->errors[] = $violation;
    }
}
