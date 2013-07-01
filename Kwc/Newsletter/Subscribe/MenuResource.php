<?php
class Kwc_Newsletter_Subscribe_MenuResource extends Kwf_Acl_Resource_ComponentClass_MenuUrl
    implements Kwf_Acl_Resource_Component_Interface, Serializable
{
    private $_component;
    public function __construct($resourceId, $menuConfig, $menuUrl, $componentClass, $component)
    {
        parent::__construct($resourceId, $menuConfig, $menuUrl, $componentClass);
        $this->_component = $component;
    }
    public function getComponent() {
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
