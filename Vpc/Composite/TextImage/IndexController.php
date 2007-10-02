<?php
class Vpc_Composite_TextImage_IndexController extends Vps_Controller_Action_Auto_Form
{
    protected $_buttons = array('save' => true);

    public function _initFields()
    {
        $pageId = $this->component->getDbId();
        $componentKey = $this->component->getComponentKey();
        $textId = array('page_id' => $pageId, 'component_key' => $componentKey . '-1');
        $imageId = array('page_id' => $pageId, 'component_key' => $componentKey . '-2');

        $this->_form = new Vps_Auto_Container();
        $this->_form
            ->setBodyStyle('padding: 10px')
            ->setLoadAfterSave(true);
        $this->_form->add(new Vpc_Basic_Text_Form('text', $textId, $this->component->text));
        $this->_form->add(new Vpc_Basic_Image_Form('image', $imageId, $this->component->image));
    }
}