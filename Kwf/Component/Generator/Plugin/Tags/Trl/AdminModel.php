<?php
class Vps_Component_Generator_Plugin_Tags_Trl_AdminModel extends Vps_Model_Data_Abstract
{
    protected $_translateFields = array('text');
    protected $_primaryKey = 'id';

    public function _init()
    {
        $model = Vps_Model_Abstract::getInstance('Vps_Component_Generator_Plugin_Tags_TagsModel');
        foreach ($model->getRows() as $row) {
            $trlRow = Vps_Model_Abstract::getInstance('Vps_Component_Generator_Plugin_Tags_Trl_Model')
                ->getRow($row->id);
            $this->_data[$row->id] = array(
                'id' => $row->id,
                'original_text' => $row->text,
                'text' => $trlRow ? $trlRow->text : null
            );
        }
        parent::_init();
    }

    public function update(Vps_Model_Row_Interface $row, $rowData)
    {
        parent::update($row, $rowData);
        if ($row->text && $row->text != $row->original_text) {
            $model = Vps_Model_Abstract::getInstance('Vps_Component_Generator_Plugin_Tags_Trl_Model');
            $trlRow = $model->getRow($row->id);
            if (!$trlRow) $trlRow = $model->createRow(array('id' => $row->id));
            $trlRow->text = $row->text;
            $trlRow->save();
        }
    }
}
