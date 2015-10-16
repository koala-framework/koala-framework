<?php
class Kwc_Columns_Abstract_Row extends Kwc_Abstract_List_Row
{
    private $_rows;
    public function __get($name)
    {
        if ($name == 'name' || $name == 'col_span' || $name == 'total_columns' || $name == 'columns') {
            if (!isset($this->_rows)) {
                $select = new Kwf_Model_Select();
                $select->whereEquals('component_id', $this->component_id);
                $select->order('pos');
                $this->_rows = $this->getModel()->getRows($select);
            }

            $columnTypes = Kwc_Abstract::getSetting($this->getModel()->getComponentClass(), 'columns');
            $typeName = array_shift(array_keys($columnTypes));
            if ($parentRow = $this->getParentRow('Component')) $typeName = $parentRow->type;
            $type = $columnTypes[$typeName];
            if ($name == 'columns') return $type['columns'];
            if ($name == 'total_columns') return count($type['colSpans']);
            unset($columnTypes);

            $i = 0;
            $countInvisible = 0;
            foreach ($this->_rows as $row) {
                if (!$row->visible) {
                    $countInvisible++;
                    $i++;
                    if ($this == $row) {
                        if ($name == 'name') {
                            return trlKwf('Invisible');
                        } else if ($name == 'col_span') {
                            return max($type['colSpans']);
                        }
                    } else {
                        continue;
                    }
                }
                $number = $i - $countInvisible;
                while ($number >= count($type['colSpans'])) $number -= count($type['colSpans']);
                if ($this == $row) {
                    if ($name == 'name') {
                        return trlKwf('Column {0}, width {1}%', array($number+1, floor(($type['colSpans'][$number] / $type['columns']) * 100)));
                    } else if ($name == 'col_span') {
                        return $type['colSpans'][$number];
                    }
                }
                $i++;
            }
        }

        return parent::__get($name);
    }
}
