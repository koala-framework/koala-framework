<?php
/**
 * List mit child daneben; list ist immer sichtbar
 * + multi-file-upload
 */
class Vpc_Abstract_List_ExtConfigListUpload extends Vpc_Abstract_List_ExtConfigList
{
    protected function _getConfig()
    {
        $ret = parent::_getConfig();
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
        $ret['list']['multiFileUpload'] = $multiFileUpload;
        return $ret;
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
