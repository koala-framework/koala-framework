<?php
class Vpc_Basic_Download_Trl_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);
        $form = Vpc_Abstract_Form::createChildComponentForm($class, '-downloadTag');
        $this->add($form);

        $this->add(new Vps_Form_Field_TextField('infotext', trlVps('Descriptiontext')))
            ->setWidth(300)
            ->setHelpText(hlpVps('vpc_download_linktext'));

        $this->add(new Vps_Form_Field_ShowField('original_infotext', trlVps('Original')))
            ->setData(new Vps_Data_Trl_OriginalComponent('infotext'));
    }
}
