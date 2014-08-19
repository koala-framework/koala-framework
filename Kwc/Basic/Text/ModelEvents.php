<?php
class Kwc_Basic_Text_ModelEvents extends Kwf_Model_Proxy_Events
{
    protected function _getModel()
    {
        if (is_instance_of($this->_modelClass, 'Kwc_Basic_Text_ChildComponentsModel')) {
            return Kwc_Basic_Text_Component::getTextModel($this->_config['componentClass'])
                ->getDependentModel('ChildComponents');
        } else if (is_instance_of($this->_modelClass, 'Kwc_Basic_Text_Model')) {
            return Kwc_Basic_Text_Component::getTextModel($this->_config['componentClass']);
        } else {
            return parent::_getModel();
        }
    }
}
