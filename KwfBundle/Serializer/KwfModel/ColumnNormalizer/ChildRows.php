<?php
namespace KwfBundle\Serializer\KwfModel\ColumnNormalizer;
use Kwf_Model_Row_Interface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;

class ChildRows implements ColumnNormalizerInterface, SerializerAwareInterface
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * {@inheritdoc}
     */
    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function normalize(Kwf_Model_Row_Interface $row, $column, array $settings, $format = null, array $context = array())
    {
        if (!isset($settings['rule'])) {
            throw new \Exception("rule setting is required");
        }
        $select = new \Kwf_Model_Select(isset($settings['where']) ? $settings['where'] : array());
        $rows = $row->getChildRows($settings['rule'], $select);
        if (isset($settings['child_groups'])) {
            $context['groups'] = $settings['child_groups'];
        } else if (isset($settings['groups'])) {
            $context['groups'] = $settings['groups'];
        }
        return $this->serializer->normalize($rows, $format, $context);
    }
}
