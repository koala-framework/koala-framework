<?php
class Kwc_Basic_ImageEnlarge_EnlargeTag_Trl_Component extends Kwc_Abstract_Image_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['image']['component'] =
            'Kwc_Basic_ImageEnlarge_EnlargeTag_Trl_Image_Component.'.$masterComponentClass;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $childImageComponent = $this->getData()->getChildComponent('-image')->getComponent();
        $ret['imageUrl'] = $childImageComponent->getImageUrl();

        $ret['imagePage'] = $this->getData()->getChildComponent('_imagePage');

        return $ret;
    }

    private function _getImageEnlargeComponentData()
    {
        $d = $this->getData();
        while (!is_instance_of($d->componentClass, 'Kwc_Basic_ImageEnlarge_Trl_Component')) {
            $d = $d->parent;
        }
        return $d;
    }

    public function getCacheMeta()
    {
        $ret = parent::getCacheMeta();

        //own_image checkbox kann sich aendern
        $row = $this->_getImageEnlargeComponentData()->getComponent()->getRow();
        $model = $row->getModel();
        $primaryKey = $model->getPrimaryKey();
        $ret[] = new Kwf_Component_Cache_Meta_Static_Model($model);
        return $ret;
    }
}
