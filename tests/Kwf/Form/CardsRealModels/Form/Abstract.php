<?php
class Kwf_Form_CardsRealModels_Form_Abstract extends Kwf_Form
{
    protected $_modelName = 'Kwf_Form_CardsRealModels_Model_WrapperModel';
    protected $_rowType = null;

    public function getRow($parentRow = null)
    {
        if (!$this->_rowType) {
            throw new Kwf_Exception('_rowType must be set when using '.get_class($this));
        }
        $row = parent::getRow($parentRow);
        $row->type = $this->_rowType;
        return $row;
    }

    protected function _init()
    {
        $this->setIdTemplate('{0}');
        parent::_init();
    }
}
