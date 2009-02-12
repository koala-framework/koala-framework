<?php
/**
 * @package Vpc
 * @subpackage Basic
 */
class Vpc_Basic_LinkTag_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'dataClass'     => 'Vpc_Basic_LinkTag_Data',
            'modelname'     => 'Vpc_Basic_LinkTag_Model',
            'componentName' => trlVps('Link'),
            'componentIcon' => new Vps_Asset('page_link'),
            'default'       => array(
                'component'    => 'intern'
            )
        ));
        $ret['generators']['link'] = array(
            'class' => 'Vpc_Basic_LinkTag_Generator',
            'component' => array(
                'intern' => 'Vpc_Basic_LinkTag_Intern_Component',
                'extern' => 'Vpc_Basic_LinkTag_Extern_Component',
                'mail'     => 'Vpc_Basic_LinkTag_Mail_Component'
            )
        );
        $ret['assetsAdmin']['dep'][] = 'VpsFormCards';
        return $ret;
    }
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['linkTag'] = $this->getData()->getChildComponent(array(
            'generator' => 'link'
        ));
        return $ret;
    }

    public function hasContent()
    {
        return $this->getData()->getChildComponent(array(
            'generator' => 'link'
        ))->hasContent();
    }

    public function getCacheVars()
    {
        return array();
    }
}
