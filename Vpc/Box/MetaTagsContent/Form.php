<?php
class Vpc_Box_MetaTagsContent_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Vps_Form_Field_TextArea('description', trlVps('Description')))
            ->setWidth(400)
            ->setHeight(100);
        $this->add(new Vps_Form_Field_TextArea('keywords', trlVps('Keywords')))
            ->setWidth(400)
            ->setHeight(100);
    }
}
