<?php
namespace KwfBundle\Serializer\KwfModel\ColumnNormalizer;
use Kwf_Model_Interface;
use Kwf_Model_Row_Interface;
use Kwf_Date;
use Kwf_DateTime;
use Symfony\Component\Validator\ConstraintViolationList;

class Column implements ColumnNormalizerInterface, ColumnDenormalizerInterface
{
    public function normalize(Kwf_Model_Row_Interface $row, $column, array $settings, $format = null, array $context = array())
    {
        if (isset($settings['column'])) {
            return $row->{$settings['column']};
        }

        return $row->$column;
    }

    public function getValidationConstraints($column, array $settings, array $context = array())
    {
        if (isset($settings['constraints'])) {
            return $settings['constraints'];
        }
        return array();
    }

    public function denormalize($v, Kwf_Model_Row_Interface $row, $column, array $settings, $format = null, array $context = array())
    {
        if (!is_null($v) && $row->getModel()->getColumnType($column) == Kwf_Model_Interface::TYPE_DATE) {
            $v = new Kwf_Date($v);
            $v = $v->format();
        } else if (!is_null($v) && $row->getModel()->getColumnType($column) == Kwf_Model_Interface::TYPE_DATETIME) {
            $v = new Kwf_DateTime($v);
            $v = $v->format();
        }
        $row->$column = $v;
    }
}
