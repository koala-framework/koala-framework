<?php
namespace KwfBundle\Serializer\KwfModel\ColumnNormalizer;
use Kwf_Model_Row_Interface;

interface ColumnDenormalizerInterface
{
    public function getValidationConstraints($column, array $settings, array $context = array());
    public function denormalize($data, Kwf_Model_Row_Interface $row, $column, array $settings, $format = null, array $context = array());
}
