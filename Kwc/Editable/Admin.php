<?php
class Kwc_Editable_AdminResource extends Kwf_Acl_Resource_MenuUrl
    implements Kwf_Acl_Resource_ComponentClass_Interface
{
    protected $_componentClass;

    public function __construct($componentClass, $menuConfig = null, $menuUrl = null)
    {
        $this->_componentClass = $componentClass;
        parent::__construct('kwc_Kwc_Editable', $menuConfig, $menuUrl);
    }

    public function getComponentClass()
    {
        return $this->_componentClass;
    }
}

class Kwc_Editable_Admin extends Kwc_Admin
{
    public function addResources(Kwf_Acl $acl)
    {
        parent::addResources($acl);
        if (!$acl->has('kwc_Kwc_Editable')) {
            $acl->add(new Kwc_Editable_AdminResource($this->_class,
                    array('text'=>trlKwf('Texts'), 'icon'=>'page_white_text.png'),
                    $this->getControllerUrl('Components')), 'kwf_component_root');
        }
    }
}
