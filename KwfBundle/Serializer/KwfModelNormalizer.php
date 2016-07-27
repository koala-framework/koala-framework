<?php
namespace KwfBundle\Serializer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class KwfModelNormalizer extends AbstractNormalizer
{
    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Kwf_Model_Row_Abstract) {
            return true;
        } else if ($data instanceof \Kwf_Model_RowSet_Abstract) {
            return true;
        }
        return false;
    }

    public function normalize($object, $format = null, array $context = array())
    {
        $attributes = $this->getAllowedAttributes($object, $context, false);
        $groups = isset($context['groups']) ? $context['groups'] : null;
        if ($groups) {
            $columns = $object->getModel()->getSerializationColumns($groups);
            $ret = array();
            foreach ($columns as $a) {
                $ret[$a] = $object->$a;
            }
            return $ret;
        } else {
            return $object->toArray();
        }
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        if (is_subclass_of($type, 'Kwf_Model_Row_Abstract')) {
            return true;
        }
        return false;
    }

    public function denormalize($data, $class, $format = null, array $context = array())
    {
        if (isset($context['object_to_populate'])) {
            $row = $context['object_to_populate'];
        } else {
            throw new \Exception();
        }
        $groups = isset($context['groups']) ? $context['groups'] : null;
        if ($groups) {
            $columns = $row->getModel()->getSerializationColumns($groups);
        } else {
            $columns = $row->getModel()->getOwnColumns();
        }
        foreach ($columns as $col) {
            if (isset($data[$col])) {
                $v = $data[$col];
                if (!is_null($v) && $row->getModel()->getColumnType($col) == \Kwf_Model_Interface::TYPE_DATE) {
                    $v = new \Kwf_Date($v);
                    $v = $v->format();
                } else if (!is_null($v) && $row->getModel()->getColumnType($col) == \Kwf_Model_Interface::TYPE_DATETIME) {
                    $v = new \Kwf_DateTime($v);
                    $v = $v->format();
                }
                $row->$col = $v;
            }
        }
    }

}
