<?php
class Vpc_Mail_Editable_AdminResource extends Vps_Acl_Resource_MenuUrl
    implements Vps_Acl_Resource_ComponentClass_Interface
{
    protected $_componentClass;

    public function __construct($componentClass, $menuConfig = null, $menuUrl = null)
    {
        $this->_componentClass = $componentClass;
        parent::__construct('vpc_Vpc_Mail_Editable', $menuConfig, $menuUrl);
    }

    public function getComponentClass()
    {
        return $this->_componentClass;
    }
}

class Vpc_Mail_Editable_Admin extends Vpc_Mail_Admin
{
    public function addResources(Vps_Acl $acl)
    {
        parent::addResources($acl);
        if (!$acl->has('vpc_Vpc_Mail_Editable')) {
            $acl->add(new Vpc_Mail_Editable_AdminResource($this->_class,
                    array('text'=>trlVps('Mail Texts'), 'icon'=>'email_open.png'),
                    $this->getControllerUrl('Mails')), 'vps_component_root');
        }
    }
}
