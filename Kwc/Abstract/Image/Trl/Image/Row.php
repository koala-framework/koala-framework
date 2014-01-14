<?php
class Kwc_Abstract_Image_Trl_Image_Row extends Kwc_Abstract_Image_Row
{
    protected $_chainedRow;

    private function _getChainedRow()
    {
        if (!$this->_chainedRow) {
            $component = Kwf_Component_Data_Root::getInstance()
                ->getComponentByDbId($this->component_id, array('ignoreVisible'=>true, 'limit'=>1));
            $this->_chainedRow = $component->parent->chained->getComponent()->getRow();
        }
        return $this->_chainedRow;
    }

    public function __get($name)
    {
        // This is just for backend. Else it would be neccessary to overwrite Form,
        // ImageUploadField, DimensionField and do a lot more.
        if ($name == 'dimension') {
            return $this->_getChainedRow()->dimension;
        } else if ($name == 'width') {
            return $this->_getChainedRow()->width;
        } else if ($name == 'height') {
            return $this->_getChainedRow()->height;
        }
        return parent::__get($name);
    }
}
