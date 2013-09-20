<?php
class Kwc_ColumnsResponsive_Model extends Kwf_Model_FnF
{
    public function setData($class, $dbId)
    {
        $select = new Kwf_Model_Select();
        $select->whereEquals('component_id', $dbId);
        $row = Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting($class, 'ownModel'))->getRow($select);
        $columnTypes = Kwc_Abstract::getSetting($class, 'columns');
        if (!$row) {
            $columns = $columnTypes[array_shift(array_keys($columnTypes))];
        } else {
            $columns = $columnTypes[$row->type];
        }
        $data = array();
        $i = 1;
        foreach($columns['colSpans'] as $colSpan) {
            $data[] = array(
                'id' => $i,
                'component_id' => $dbId,
                'name' => trlKwf('Column {0}, width {1}%', array($i, floor(($colSpan / $columns['columns']) * 100)))
            );
            $i++;
        }
        parent::setData($data);
    }
}
