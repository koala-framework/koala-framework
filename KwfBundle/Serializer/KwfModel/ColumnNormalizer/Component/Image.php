<?php
namespace KwfBundle\Serializer\KwfModel\ColumnNormalizer\Component;

use KwfBundle\Serializer\KwfModel\ColumnNormalizer\Component\Url;

class Image extends Url
{
    protected $eventSubscriberClass = 'KwfBundle\Serializer\KwfModel\ColumnNormalizer\Component\ImageEvents';

    public function normalize(\Kwf_Model_Row_Interface $row, $column, array $settings, $format = null, array $context = array())
    {
        $data = $this->getComponent($row, $settings);
        if (!$data || !$data->hasContent()) return null;

        $ret = $data->getComponent()->getApiData();
        $ret['id'] = $this->getComponentId($row, $settings);
        return $ret;
    }
}
