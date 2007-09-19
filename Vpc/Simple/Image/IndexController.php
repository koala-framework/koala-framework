<?php
class Vpc_Simple_Image_IndexController extends Vps_Controller_Action_Auto_Form_Vpc
{
    protected $_buttons = array (
        'save' => true
    );

    public function indexAction() {
        $this->view->ext('Vpc.Simple.Image.Index');
    }

    public function _initFields()
    {
        $this->_form->setTable(new Vpc_Simple_Image_IndexModel());
        $this->_form->setFileUpload(true);
        $fields = $this->_form->fields;
        $fields->add(new Vps_Auto_Field_TextField('name'))
            ->setFieldLabel('Filename');
        $fields->add(new Vps_Auto_Field_File('SimpleImage/', $this->component->getSetting('extensions')))
            ->setFieldLabel('File');

        //Einstellungen für die Veränderbarkeit der Höhe und Breite
        $sizes = $this->component->getSetting('size');
        if (empty($sizes)) {
            $fields->add(new Vps_Auto_Field_TextField('width'))
                ->setFieldLabel('Width');
            $fields->add(new Vps_Auto_Field_TextField('height'))
                ->setFieldLabel('Height');
        } else {
            $fields->add(new Vps_Auto_Field_ComboBoxSize())
                ->setFieldLabel('Possible Sizes')
                ->setSizes($sizes);
        }
        
        if ($this->component->getSetting('allow') != '' && $this->component->getSetting('allow') != array()) {
            $styles = $this->component->getSetting('allow');
            $newStyles = array ();
            foreach ($styles as $data) {
                $newStyles[] = array($data, $data);
            }
            $this->_fields[] = array (
                'type'          => 'ComboBox',
                'fieldLabel'    => 'Settings',
                'name'          => 'style',
                'width'         => 150,
                'store'         => array('data' => $newStyles),
                'hiddenName'    => 'style',
                'editable'      => false,
                'triggerAction' => 'all'
            );
        }
    }
    
    public function jsonLoadAction()
    {
        parent::jsonLoadAction();
        $this->view->urlbig = $this->component->getImageUrl();
        $this->view->url = $this->component->getImageUrl(Vpc_Simple_Image_Index::SIZE_THUMB);
    }
}