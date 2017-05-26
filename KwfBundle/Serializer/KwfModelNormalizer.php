<?php
namespace KwfBundle\Serializer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use KwfBundle\Serializer\KwfModel\ColumnNormalizer\CacheableInterface;
use Kwf_Cache_Simple;


class KwfModelNormalizer extends AbstractNormalizer
{
    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Kwf_Model_Row_Abstract) {
            return true;
        }
        return false;
    }

    public function normalize($object, $format = null, array $context = array())
    {
        $attributes = $this->getAllowedAttributes($object, $context, false);
        $groups = isset($context['groups']) ? $context['groups'] : null;
        $context['serializer'] = $this->serializer;
        if ($groups) {
            $columns = $object->getModel()->getSerializationColumns($groups);
            $ret = array();
            $pk = $object->getModel()->getPrimaryKey();
            $ret[$pk] = $object->$pk;
            foreach ($columns as $column=>$settings) {
                $ret[$column] = $object->normalizeColumn($column, $format, $context);
            }
            return $ret;
        } else {
            return $object->toArray();
        }
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return false;
    }


    public function denormalize($data, $class, $format = null, array $context = array())
    {
    }
}
