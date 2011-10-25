<?php
class Kwc_Mail_Editable_AdminResource extends Kwf_Acl_Resource_MenuUrl
    implements Kwf_Acl_Resource_ComponentClass_Interface
{
    protected $_componentClass;

    public function __construct($componentClass, $menuConfig = null, $menuUrl = null)
    {
        $this->_componentClass = $componentClass;
        parent::__construct('kwc_Kwc_Mail_Editable', $menuConfig, $menuUrl);
    }

    public function getComponentClass()
    {
        return $this->_componentClass;
    }
}

class Kwc_Mail_Editable_Admin extends Kwc_Mail_Admin
{
    public function addResources(Kwf_Acl $acl)
    {
        parent::addResources($acl);
        if (!$acl->has('kwc_Kwc_Mail_Editable')) {
            $acl->add(new Kwc_Mail_Editable_AdminResource($this->_class,
                    array('text'=>trlKwf('Mail Texts'), 'icon'=>'email_open.png'),
                    $this->getControllerUrl('Mails')), 'kwf_component_root');
        }
    }
}
