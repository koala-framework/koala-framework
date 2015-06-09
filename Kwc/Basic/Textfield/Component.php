<?php
/**
 * @package Kwc
 * @subpackage Basic
 */
class Kwc_Basic_Textfield_Component extends Kwc_Basic_Html_Component
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => trlKwfStatic('Headline'),
            'ownModel' => 'Kwc_Basic_Textfield_Model'
        ));
    }
}
