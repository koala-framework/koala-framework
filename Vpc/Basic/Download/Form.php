<?php
class Vpc_Basic_Download_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);
        $form = Vpc_Abstract_Form::createComponentForm($class, '{0}-downloadTag');
        $this->add($form);

        $this->add(new Vps_Form_Field_TextField('infotext', trlVps('Beschreibungstext')))
            ->setWidth(300)
            ->setHelpText(hlpVps('vpc_download_linktext'));
    }
}
