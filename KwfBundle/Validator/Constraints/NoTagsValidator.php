<?php
namespace KwfBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ExecutionContextInterface;

class NoTagsValidator extends ConstraintValidator
{
    protected $kwfNoTagsValidator = false;

    public function initialize(ExecutionContextInterface $context)
    {
        parent::initialize($context);
        $this->kwfNoTagsValidator = new \Kwf_Validate_NoTags();
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$this->kwfNoTagsValidator->isValid($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
