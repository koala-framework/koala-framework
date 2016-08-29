<?php
class Kwc_Basic_LinkTag_CommunityVideo_Admin extends Kwc_Admin
{
    public function componentToString(Kwf_Component_Data $data)
    {
        return Kwf_Trl::getInstance()
            ->trlStaticExecute(Kwc_Abstract::getSetting($data->componentClass, 'componentName'));
    }
}
