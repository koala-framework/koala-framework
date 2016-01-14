<?php
class Kwc_Basic_Download_Trl_Form extends Kwc_Abstract_Form
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);
        $form = Kwc_Abstract_Form::createChildComponentForm($class, '-downloadTag');
        $this->add($form);

        $this->add(new Kwf_Form_Field_TextField('infotext', trlKwf('Descriptiontext')))
            ->setWidth(300)
            ->setHelpText(trlKwf('Text, shown after the file icon (automatically generated) and used as link for downloading the file.'));

        $this->add(new Kwf_Form_Field_ShowField('original_infotext', trlKwf('Original')))
            ->setData(new Kwf_Data_Trl_OriginalComponent('infotext'));
    }
}
