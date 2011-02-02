<?php
class Vpc_Basic_Download_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);
        $form = Vpc_Abstract_Form::createChildComponentForm($class, '-downloadTag');
        $this->add($form);

        $this->add(new Vps_Form_Field_TextField('infotext', trlVps('Descriptiontext')))
            ->setWidth(300)
            ->setAutoFillWithFilename('filenameWithExt') //um es beim MultiFileUpload zu finde
            ->setHelpText(hlpVps('vpc_download_linktext'))
            ->setAllowBlank(false);
    }
}
