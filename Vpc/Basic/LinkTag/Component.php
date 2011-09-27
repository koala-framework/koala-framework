<?php
/**
 * @package Vpc
 * @subpackage Basic
 */
class Vpc_Basic_LinkTag_Component extends Vpc_Abstract_Cards_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dataClass'] = 'Vpc_Basic_LinkTag_Data';
        $ret['componentName'] = trlVps('Link');
        $ret['componentIcon'] = new Vps_Asset('page_link');
        $ret['default']['component'] = 'intern';
        $ret['extConfig'] = 'Vps_Component_Abstract_ExtConfig_None';
        $ret['generators']['child']['component'] = array(
            'intern'   => 'Vpc_Basic_LinkTag_Intern_Component',
            'extern'   => 'Vpc_Basic_LinkTag_Extern_Component',
            'mail'     => 'Vpc_Basic_LinkTag_Mail_Component',
            'download' => 'Vpc_Basic_DownloadTag_Component'
        );
        $cc = Vps_Registry::get('config')->vpc->childComponents;
        if (isset($cc->Vpc_Basic_LinkTag_Component)) {
            $ret['generators']['child']['component'] = array_merge(
                $ret['generators']['child']['component'],
                $cc->Vpc_Basic_LinkTag_Component->toArray()
            );
        }
        return $ret;
    }

    public static function validateSettings($settings, $componentClass)
    {
        parent::validateSettings($settings, $componentClass);
        if (isset($settings['generators']['link'])) {
            throw new Vps_Exception("\$ret['generators']['link'] is deprecated, use \$ret['generators']['child']");
        }
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['linkTag'] = $ret['child'];
        return $ret;
    }
}
