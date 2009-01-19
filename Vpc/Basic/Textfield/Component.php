<?php
/**
 * @package Vpc
 * @subpackage Basic
 */
class Vpc_Basic_Textfield_Component extends Vpc_Basic_Html_Component
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Headline'),
            'modelname' => 'Vpc_Basic_Textfield_Model'
        ));
    }
}
