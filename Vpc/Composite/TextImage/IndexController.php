<?php
class Vpc_Composite_TextImage_IndexController extends Vps_Controller_Action_Auto_Form_Vpc
{
    public function _initFields()
    {
        $pageId = $this->_getParam('page_id');
        $componentKey = $this->_getParam('component_key');
        $textId = array('page_id' => $pageId, 'component_key' => $componentKey . '-1');
        $imageId = array('page_id' => $pageId, 'component_key' => $componentKey . '-2');

        //parent::_initFields();
        $this->_form->fields->add(new Vps_Auto_Form_Text('text', $textId));
        $this->_form->fields->add(new Vps_Auto_Form_Image('text', $imageId, $this->component->pic));
    }
}