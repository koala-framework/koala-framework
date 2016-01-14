<?php
class Kwc_Advanced_IntegratorTemplate_Form extends Kwc_Abstract_Form
{
    protected function _init()
    {
        parent::_init();
        $this->setLabelWidth(150);
        $this->add(new Kwf_Form_Field_TextField('url', trl('Application URL')))
            ->setWidth(500)
            ->setAllowBlank(false);
        $this->add(new Kwf_Form_Field_ShowField('template_url', trl('HTML Template URL')))
            ->setWidth(500)
            ->setData(new Kwc_Advanced_IntegratorTemplate_FormData());
    }
}

class Kwc_Advanced_IntegratorTemplate_FormData extends Kwf_Data_Abstract
{
    public function load($row)
    {
        return Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($row->component_id, array('ignoreVisible' => true))
            ->getChildComponent('_embed')->getAbsoluteUrl();
    }
}
