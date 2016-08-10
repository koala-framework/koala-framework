<?php
namespace KwfBundle\Serializer\KwfModel\ColumnNormalizer;
use Kwf_Model_Row_Interface;

interface ColumnNormalizerInterface
{
    public function normalize(Kwf_Model_Row_Interface $row, $column, array $settings, $format = null, array $context = array());
}
