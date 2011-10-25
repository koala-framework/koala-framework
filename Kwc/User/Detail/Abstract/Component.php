<?php
abstract class Kwc_User_Detail_Abstract_Component extends Kwc_Abstract_Composite_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['row'] = $this->getData()->parent->row;
        return $ret;
    }

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta($componentClass);
        $model = Kwf_Registry::get('config')->user->model;
        $ret[] = new Kwf_Component_Cache_Meta_Static_Model($model, 'users_{id}-general');
        return $ret;
    }
}
