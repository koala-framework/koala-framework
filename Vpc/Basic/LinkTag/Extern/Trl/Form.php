<?php
class Vpc_Basic_LinkTag_Extern_Trl_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Vps_Form_Field_TextField('target', trlVps('Url')))
            ->setWidth(450)
            ->setHelpText(hlpVps('vpc_basic_linktag_extern_target'))
            ->setAllowBlank(false)
            ->setVtype('url');
    }
}
