<?php
class Vps_Auto_Form_Image extends Vps_Auto_Form
{
    public function __construct($name = null, $id = null, Vpc_Basic_Image_Index $component)
    {
        parent::__construct($name, $id);
        $this->setTable(new Vpc_Basic_Image_IndexModel());
        $this->setFileUpload(true);
        $fields = $this->fields;
        $fields->add(new Vps_Auto_Field_TextField('name'))
            ->setFieldLabel('Filename');

        $fields->add(new Vps_Auto_Field_File('BasicImage/', $component->getSetting('extensions')))
            ->setFieldLabel('File');

        //Einstellungen für die Veränderbarkeit der Höhe und Breite
        $sizes = $component->getSetting('size');
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
        
        if ($component->getSetting('allow') != '' && $component->getSetting('allow') != array()) {
            $styles = $component->getSetting('allow');
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
}