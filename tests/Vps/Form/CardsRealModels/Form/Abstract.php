<?php
class Vps_Form_CardsRealModels_Form_Abstract extends Vps_Form
{
    protected $_modelName = 'Vps_Form_CardsRealModels_Model_WrapperModel';
    protected $_rowType = null;

    public function getRow($parentRow = null)
    {
        if (!$this->_rowType) {
            throw new Vps_Exception('_rowType must be set when using '.get_class($this));
        }

        $row = parent::getRow($parentRow);
        $row->type = $this->_rowType;
        return $row;
    }

    protected function _init()
    {
        parent::_init();

        $form = new Vps_Form_CardsRealModels_Form_Details();
        $form->setIdTemplate('{0}');
        $this->add($form);


    }

}
