<?php
namespace KwfBundle\Serializer;
use KwfBundle\Rest\SubmitHandler;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class SubmitHandlerErrorNormalizer implements NormalizerInterface
{
    private $translator;
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $errors = array();
        foreach ($object->getErrors() as $error) {
            $errors[] = array(
                'field' => $error['field'],
                'message' => $this->getErrorMessage($error['violation'])
            );
        }
        return array(
            'code' => isset($context['status_code']) ? $context['status_code'] : null,
            'message' => 'Validation Failed',
            'errors' => $errors,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof SubmitHandler && $data->isSubmitted() && !$data->isValid();
    }

    private function getErrorMessage(ConstraintViolation $error)
    {
        if (null !== $error->getPlural()) {
            return $this->translator->transChoice($error->getMessageTemplate(), $error->getPlural(), $error->getParameters(), 'validators');
        }
        return $this->translator->trans($error->getMessageTemplate(), $error->getParameters(), 'validators');
    }
}
