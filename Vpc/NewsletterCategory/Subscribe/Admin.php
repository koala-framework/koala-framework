<?php
class Vpc_NewsletterCategory_Subscribe_Admin extends Vpc_Newsletter_Subscribe_Admin
{
    public function getExtConfig()
    {
        $icon = new Vps_Asset('application_form');
        $ret = array(
            'categories' => array(
                'xtype' => 'vps.autogrid',
                'controllerUrl' => $this->getControllerUrl('Categories'),
                'title' => trlVps('Edit {0}', trlVps('Categories')),
                'icon' => $icon->__toString()
            )
        );
        return $ret;
    }
}
