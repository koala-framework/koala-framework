<?php
/**
 * @package Vpc
 * @subpackage Basic
 */
class Vpc_Basic_Space_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Space'),
            'ownModel'     => 'Vpc_Basic_Space_Model',
            'default'       => array(
                'height' => 20
            )
        ));
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['height'] = $this->_getRow()->height;
        return $ret;
    }
}
