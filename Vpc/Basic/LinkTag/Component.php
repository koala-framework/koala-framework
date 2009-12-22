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
        $ret['configChildComponentsGenerator'] = 'link';
        $ret['generators']['link'] = array(
            'class' => 'Vpc_Basic_LinkTag_Generator',
            'component' => array(
                'intern'   => 'Vpc_Basic_LinkTag_Intern_Component',
                'extern'   => 'Vpc_Basic_LinkTag_Extern_Component',
                'mail'     => 'Vpc_Basic_LinkTag_Mail_Component',
                'download' => 'Vpc_Basic_DownloadTag_Component'
            ),
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
        $ret = parent::getCacheVars();
        $row = $this->_getCacheRow();
        $ret[] = array(
            'model' => $row->getModel(),
            'id' => $row->component_id,
            'callback' => true
        );
        // der typ vom link-tag kann sich ändern und hat die gleiche cache-id,
        // deshalb unterkomponente gleich mitlöschen
        $link = $this->getData()->getChildComponent('-link');
        if ($link) {
            $ret[] = array(
                'model' => $row->getModel(),
                'id' => $row->component_id,
                'componentId' => $link->componentId
            );
        }
        return $ret;
    }

    public function onCacheCallback($row)
    {
        if ($this->getData()->isPage) {
            foreach (Vpc_Abstract::getComponentClasses() as $componentClass) {
                if (is_instance_of($componentClass, 'Vpc_Menu_Abstract')) {
                    Vps_Component_Cache::getInstance()->cleanComponentClass($componentClass);
                }
            }
        }
    }

}
