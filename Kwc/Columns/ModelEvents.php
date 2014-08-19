<?php
class Kwc_Columns_ModelEvents extends Kwf_Model_Proxy_Events
{
    protected function _getModel()
    {
        if (is_instance_of($this->_modelClass, 'Kwc_Columns_Model')) {
            return Kwc_Columns_Component::getColumnsModel($this->_config['componentClass']);
        } else {
            return parent::_getModel();
        }
    }
}
