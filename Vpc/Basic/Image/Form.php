<?php
class Vpc_Basic_Image_Form extends Vps_Auto_Vpc_Form
{
    public function __construct(Vpc_Basic_Image_Component $component)
    {
        parent::__construct($component);

        // Dateiname
        if ($component->getSetting('editFilename')) {
            $this->add(new Vps_Auto_Field_TextField('filename', 'Filename'))
                ->setAllowBlank(false)
                ->setVtype('alphanum');
        }

        // Höhe, Breite
        $dimensions = $component->getSetting('dimension');
        if (empty($dimensions)) {
            $this->add(new Vps_Auto_Field_TextField('width', 'Width'));
            $this->add(new Vps_Auto_Field_TextField('height','Height'));
        } else if (is_array($dimensions[0])) {
            $this->add(new Vps_Auto_Field_ComboBoxSize('dimension', 'Size'))
                ->setSizes($dimensions);
        }

        // Skalierungstyp
        $allow = $component->getSetting('allow');
        if (is_array($allow) && sizeof($allow) > 1) {
            $this->add(new Vps_Auto_Field_Select('scale', 'Scaling'))
                ->setValues($allow);
        }

        // Fileupload
        $this->add(new Vps_Auto_Field_File('vps_upload_id', 'File'))
            ->setExtensions($component->getSetting('extensions'))
            ->setAllowBlank($component->getSetting('allowBlank'));

        // Bildvorschau
        $this->add(new Vps_Auto_Field_ImageViewer('vps_upload_id_image', 'Preview'))
            ->setImageUrl($component->getImageUrl())
            ->setPreviewUrl($component->getImageUrl(Vpc_Basic_Image_Component::DIMENSION_THUMB));

        // Enlarged Image
        if ($component->getSetting('hasEnlarge')) {
            $imagebig = new Vpc_Basic_Image_Form($component->imagebig);
            $imagebig->fields->getByName('vps_upload_id')->setFileFieldLabel('File (optional)');
            $this->add(new Vps_Auto_Container_FieldSet('Enlarged Image'))
                ->setCheckboxToggle(true)
                ->setCheckboxName('enlarge')
                ->setCollapsed(!$component->getSetting('enlarge'))
                ->add($imagebig);
        }
    }
}