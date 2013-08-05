<?php
class Kwf_Acl_Resource_Component_MenuUrl extends Kwf_Acl_Resource_MenuUrl
    implements Kwf_Acl_Resource_Component_Interface, Serializable
{
    protected $_component;

    public function __construct($resourceId, $menuConfig = null, $menuUrl = null, Kwf_Component_Data $component = null)
    {
        if ($resourceId instanceof Kwf_Component_Data) {
            $component = $resourceId;
            $resourceId = 'kwc_'.$component->dbId;
        } else {
            if (!$component) throw new Kwf_Exception("component parameter is required");
        }
        $this->_component = $component;
        if (!$menuConfig) {
            if (Kwc_Abstract::hasSetting($this->_class, 'componentNameShort')) {
                $name = Kwc_Abstract::getSetting($this->_class, 'componentNameShort');
            } else {
                $name = Kwc_Abstract::getSetting($this->_class, 'componentName');
            }
            $icon = Kwc_Abstract::getSetting($component->componentClass, 'componentIcon');
            $menuConfig = array('text'=>trlKwfStatic('Edit {0}', $name), 'icon'=>$icon);
        }
        if (!$menuUrl) {
            $menuUrl = Kwc_Admin::getInstance($component->componentClass)
                ->getControllerUrl() . '?componentId=' . $component->dbId;
        }
        parent::__construct($resourceId, $menuConfig, $menuUrl);
    }

    public function getComponent()
    {
        return $this->_component;
    }

    public function serialize()
    {
        $ret = array();
        foreach (get_object_vars($this) as $k=>$i) {
            if ($k == '_component') {
                $i = $i->kwfSerialize();
            }
            $ret[$k] = $i;
        }
        return serialize($ret);
    }

    public function unserialize($serialized)
    {
        foreach (unserialize($serialized) as $k=>$i) {
            if ($k == '_component') {
                $i = Kwf_Component_Data::kwfUnserialize($i);
            }
            $this->$k = $i;
        }
    }
}
