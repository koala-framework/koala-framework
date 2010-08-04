<?php
class Vpc_Abstract_List_ExtConfigList extends Vps_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $class = Vpc_Abstract::getChildComponentClass($this->_class, 'child');
        $childConfig = array_values(Vpc_Admin::getInstance($class)->getExtConfig());
        if (count($childConfig) > 1) {
            //wenn das mal benötigt wird möglicherwesie mit tabs
            throw new Vps_Exception("Vpc_Abstract_List can only have childs with one Controller '$class'");
        } else if (!count($childConfig)) {
            throw new Vps_Exception("Vpc_Abstract_List must have child with at least one ExtConfig");
        }

        $multiFileUpload = false;
        $form = Vpc_Abstract_Form::createChildComponentForm($this->_class, 'child');
        if ($field = $this->_getFileUploadField($form)) {
            $multiFileUpload = array(
                'allowOnlyImages' => $field->getAllowOnlyImages(),
                'maxResolution' => $field->getMaxResolution(),
                'maxResolution' => $field->getMaxResolution(),
                'fileSizeLimit' => $field->getFileSizeLimit(),
            );
        }

        $config = $this->_getStandardConfig('vpc.list');
        $config['childConfig'] = $childConfig[0];
        $config['multiFileUpload'] = $multiFileUpload;
        return array(
            'list' => $config
        );
    }

    private function _getFileUploadField($form)
    {
        foreach ($form as $i) {
            if ($i instanceof Vps_Form_Field_File) {
                return $i;
            }
            if (!($i instanceof Vps_Form_Container_Cards)) { //in cards nicht reinschaun, bei Links wollen wir keinen multi upload
                $ret = $this->_getFileUploadField($i);
                if ($ret) return $ret;
            }
        }
        return null;
    }
}