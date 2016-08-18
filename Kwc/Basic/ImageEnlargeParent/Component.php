<?php
class Kwc_Basic_ImageEnlargeParent_Component extends Kwc_Basic_ImageParent_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => array(
                'linkTag' => 'Kwc_Basic_ImageEnlarge_EnlargeTag_Component'
            ),
            'addUrlPart' => false
        );
        $ret['rootElementClass'] = 'kwcBasicImageEnlarge ' . Kwf_Config::getValue('kwc.imageEnlarge.cssClass');
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['keys'] = array();
        foreach ($this->getData()->getChildComponents(array('generator' => 'child')) as $c) {
            $ret[$c->id] = $c;
            $ret['keys'][] = $c->id;
        }
        unset($ret['template']);
        return $ret;
    }


}

