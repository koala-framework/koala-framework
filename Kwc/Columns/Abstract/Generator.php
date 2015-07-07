<?php
class Kwc_Columns_Abstract_Generator extends Kwf_Component_Generator_Table
{
    protected function _getModel()
    {
        return Kwc_Columns_Abstract_ModelFactory::getModelInstance(array(
            'componentClass' => $this->_class
        ));
    }
}
