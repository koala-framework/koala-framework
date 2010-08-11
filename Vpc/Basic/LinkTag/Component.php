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
            'ownModel'     => 'Vpc_Basic_LinkTag_Model',
            'componentName' => trlVps('Link'),
            'componentIcon' => new Vps_Asset('page_link'),
            'default'       => array(
                'component'    => 'intern'
            )
        ));
        $ret['generators']['link'] = array(
            'class' => 'Vpc_Basic_LinkTag_Generator',
            'component' => array(
                'intern'   => 'Vpc_Basic_LinkTag_Intern_Component',
                'extern'   => 'Vpc_Basic_LinkTag_Extern_Component',
                'mail'     => 'Vpc_Basic_LinkTag_Mail_Component',
                'download' => 'Vpc_Basic_DownloadTag_Component'
            ),
        );
        $cc = Vps_Registry::get('config')->vpc->childComponents;
        if (isset($cc->Vpc_Basic_LinkTag_Component)) {
            $ret['generators']['link']['component'] = array_merge(
                $ret['generators']['link']['component'],
                $cc->Vpc_Basic_LinkTag_Component->toArray()
            );
        }
        $ret['assetsAdmin']['dep'][] = 'VpsFormCards';

        $ret['extConfig'] = 'Vps_Component_Abstract_ExtConfig_Form';
        return $ret;
    }
    public function getTemplateVars()
    {
        $ret = array();
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

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta();
        // der typ vom link-tag kann sich ändern und hat die gleiche cache-id,
        // deshalb unterkomponente gleich mitlöschen
        $model = Vpc_Abstract::getSetting($componentClass, 'ownModel');
        $ret[] = new Vps_Component_Cache_Meta_Static_Model($model, '{component_id}-link');
        return $ret;
    }
}
