<?php
class Vpc_Formular_Component extends Vpc_Abstract
{
    protected $_form;
    protected $_formName;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childComponentClasses']['success'] = 'Vpc_Formular_Success_Component';
        $ret['componentName'] = 'Formular';
        $ret['placeholder']['submitButton'] = trlVps('Submit');
        $ret['decorator'] = 'Vpc_Formular_Decorator_Label';
        return $ret;
    }

    protected function _initForm()
    {
        $this->_form = new Vps_Rotary_Test_Form();
        $this->_form->setId(1);
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $this->_initForm();

        if (!isset($this->_form) && isset($this->_formName)) {
            $this->_form = new $this->_formName();
        }

        $ret['isSuccess'] = false;
        $ret['errors'] = array();
        if (isset($_POST[$this->getTreeCacheRow()->component_id])) {
            $ret['errors'] = $this->_form->validate($_REQUEST);
            if (!$ret['errors']) {
                $this->_form->prepareSave(null, $_REQUEST);
                $this->_form->save(null, $_REQUEST);
                $ret['isSuccess'] = true;
            }
        }

        $values = array_merge($this->_form->load(null), $_REQUEST);
        $ret['form'] = $this->_form->getTemplateVars($values);

        $dec = $this->_getSetting('decorator');
        if ($dec && is_string($dec)) {
            $dec = new $dec();
        }
        if ($dec) {
            $ret['form'] = $dec->processItem($ret['form']);
        }

        $ret['formName'] = $this->getTreeCacheRow()->component_id;

        $ret['action'] = $this->getTreeCacheRow()->tree_url;

        $componentId = $this->getTreeCacheRow()->component_id.'-success';
        $row = $this->getTreeCacheRow()->getTable()->find($componentId)->current();
        $return['success'] = $row->getComponent()->getTemplateVars();

        return $ret;
    }
}
