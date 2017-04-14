<?php
namespace KwfBundle\Serializer\KwfModel\ColumnNormalizer;

use Kwf_Model_Row_Interface;
use Kwf_Model_Interface;

interface CacheableInterface
{
    public function getEventSubscribers(Kwf_Model_Interface $model, $column, array $settings);
    public function getCacheId(Kwf_Model_Row_Interface $row, $column, array $settings, $format = null, array $context = array());
}
