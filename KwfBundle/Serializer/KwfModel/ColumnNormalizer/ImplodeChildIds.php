<?php
namespace KwfBundle\Serializer\KwfModel\ColumnNormalizer;
use Kwf_Model_Row_Interface;
use Symfony\Component\Serializer\SerializerInterface;

class ImplodeChildIds implements ColumnNormalizerInterface, ColumnDenormalizerInterface
{
    public function normalize(Kwf_Model_Row_Interface $row, $column, array $settings, $format = null, array $context = array())
    {
        if (!isset($settings['rule'])) {
            throw new \Exception("rule setting is required");
        }
        if (isset($settings['column'])) {
            $col = $settings['column'];
        } else {
            $col = null;
        }

        $rows = $row->getChildRows($settings['rule']);
        $ids = array();
        foreach ($rows as $row) {
            if (!$col) $col = $row->getModel()->getPrimaryKey();
            $ids[] = $row->$col;
        }
        return implode(',', $ids);
    }

    public function getValidationConstraints($column, array $settings, array $context = array())
    {
        if (isset($settings['constraints'])) {
            return $settings['constraints'];
        }
        return array();
    }

    public function denormalize($data, Kwf_Model_Row_Interface $row, $column, array $settings, $format = null, array $context = array())
    {
        if (!isset($settings['rule'])) {
            throw new \Exception("rule setting is required");
        }
        if (!isset($settings['column'])) {
            throw new \Exception("column setting is required");
        }

        $values = explode(',', $data);
        $existingRows = $row->getChildRows($settings['rule']);
        $existingIds = array();
        foreach ($existingRows as $r) {
            $existingIds[] = $r->{$settings['column']};
        }
        foreach ($values as $v) {
            if (!in_array($v, $existingIds)) {
                //create row
                $row->createChildRow($settings['rule'], array(
                    $settings['column'] => $v
                ));
            }
        }
        foreach ($existingRows as $r) {
            if (!in_array($r->{$settings['column']}, $values)) {
                $r->delete();
            }
        }
    }
}
