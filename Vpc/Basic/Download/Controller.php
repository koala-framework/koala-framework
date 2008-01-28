<?php
class Vpc_Basic_Download_Controller extends Vps_Controller_Action_Auto_Vpc_Form
{
    public function _initFields()
    {
        $classes = Vpc_Abstract::getSetting($this->class, 'childComponentClasses');

        $id = $this->componentId . '-tag';
        $form = new Vpc_Basic_DownloadTag_Form($classes['downloadTag'], $id);
        $this->_form->add($form);
        $this->_form->add(new Vps_Auto_Field_TextArea('infotext', 'Infotext'))
            ->setWidth(300)
            ->setGrow(true);
    }
}
