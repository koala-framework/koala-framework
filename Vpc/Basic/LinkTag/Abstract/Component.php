<?php
abstract class Vpc_Basic_LinkTag_Abstract_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentIcon' => new Vps_Asset('page_link')
        ));
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['href'] = $this->getData()->url;
        $ret['rel'] = $this->getData()->rel;
        return $ret;
    }
}
