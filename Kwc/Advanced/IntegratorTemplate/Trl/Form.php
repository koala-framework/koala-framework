<?php
class Kwc_Advanced_IntegratorTemplate_Trl_Form extends Kwc_Abstract_Form
{
    protected function _init()
    {
        parent::_init();
        $this->setLabelWidth(150);
        $this->add(new Kwf_Form_Field_TextField('url', trl('Application URL')))
            ->setWidth(500);
        $this->add(new Kwf_Form_Field_ShowField('original_url', trlKwf('Original')))
            ->setData(new Kwf_Data_Trl_OriginalComponent())
            ->getData()->setFieldname('url');

        $this->add(new Kwf_Form_Field_Static(''));

        $this->add(new Kwf_Form_Field_ShowField('template_url', trl('HTML Template URL')))
            ->setWidth(500)
            ->setData(new Kwc_Advanced_IntegratorTemplate_Trl_FormData());
    }
}

class Kwc_Advanced_IntegratorTemplate_Trl_FormData extends Kwf_Data_Abstract
{
    public function load($row, array $info = array())
    {
        return Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($row->component_id, array('ignoreVisible' => true))
            ->getChildComponent('_embed')->getAbsoluteUrl();
    }
}
