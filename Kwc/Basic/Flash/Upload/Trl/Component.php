<?php
class Kwc_Basic_Flash_Upload_Trl_Component extends Kwc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['flash'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => $masterComponentClass
        );
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        if ($this->getRow()->own_flash) {
            $tvars = $this->getData()->getChildComponent('-flash')->getComponent()->getTemplateVars($renderer);
            $ret['flash'] = $tvars['flash'];
        }
        return $ret;
    }

    public function hasContent()
    {
        if ($this->getRow()->own_flash) {
            return $this->getData()->getChildComponent('-flash')->hasContent();
        }
        return $this->getData()->chained->hasContent();
    }
}