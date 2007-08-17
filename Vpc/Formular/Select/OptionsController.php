<?php
class Vpc_Formular_Select_OptionsController extends Vpc_Formular_Field_FormGrid
{
    protected $_columns = array(array('dataIndex' => 'value',
                              'header'    => 'Wert',
                              'width'     => 100,
                              'editor'    => array('type' => 'TextField',
                                          'allowBlank' => false)),
                        array('dataIndex' => 'text',
                              'header'    => 'Ausgabe',
                              'width'     => 100,
                              'editor'    => array('type' => 'TextField',
                                          'allowBlank' => false)),
                        array('dataIndex' => 'selected',
                              'header'    => 'Angehakt',
                              'width'     => 50,
                              'editor'    => 'Checkbox'));

    protected $_defaultOrder = 'value';
    protected $_tableName = 'Vpc_Formular_Select_OptionsModel';

    protected function _getWhere()
    {
        $where = parent::_getWhere();
        $where['page_id = ?'] = $this->component->getDbId();
        return $where;
    }
}