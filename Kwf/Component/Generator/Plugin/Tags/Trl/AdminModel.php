<?php
class Kwf_Component_Generator_Plugin_Tags_Trl_AdminModel extends Kwf_Model_Data_Abstract
{
    protected $_translateFields = array('text');
    protected $_primaryKey = 'id';

    public function _init()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwf_Component_Generator_Plugin_Tags_TagsModel');
        foreach ($model->getRows() as $row) {
            $trlRow = Kwf_Model_Abstract::getInstance('Kwf_Component_Generator_Plugin_Tags_Trl_Model')
                ->getRow($row->id);
            $this->_data[$row->id] = array(
                'id' => $row->id,
                'original_text' => $row->text,
                'text' => $trlRow ? $trlRow->text : null
            );
        }
        parent::_init();
    }

    public function update(Kwf_Model_Row_Interface $row, $rowData)
    {
        parent::update($row, $rowData);
        if ($row->text && $row->text != $row->original_text) {
            $model = Kwf_Model_Abstract::getInstance('Kwf_Component_Generator_Plugin_Tags_Trl_Model');
            $trlRow = $model->getRow($row->id);
            if (!$trlRow) $trlRow = $model->createRow(array('id' => $row->id));
            $trlRow->text = $row->text;
            $trlRow->save();
        }
    }
}
