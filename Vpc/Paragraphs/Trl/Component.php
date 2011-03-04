<?php
class Vpc_Paragraphs_Trl_Component extends Vpc_Chained_Trl_Component
{

    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['componentIcon'] = new Vps_Asset('page');
        $ret['generators']['paragraphs']['class'] = 'Vpc_Paragraphs_Trl_Generator';
        $ret['childModel'] = 'Vpc_Paragraphs_Trl_Model';
        $ret['previewWidth'] = Vpc_Abstract::getSetting($masterComponentClass, 'previewWidth');
        $ret['extConfig'] = 'Vpc_Paragraphs_Trl_ExtConfig';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['paragraphs'] = $this->getData()
            ->getChildComponents(array('generator'=>'paragraphs'));
        return $ret;
    }

    public function hasContent()
    {
        $childComponents = $this->getData()->getChildComponents(array('generator' => 'paragraphs'));
        foreach ($childComponents as $c) {
            if ($c->hasContent()) return true;
        }
        return false;
    }

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta($componentClass);
        $ret[] = new Vpc_Paragraphs_Trl_CacheMeta();
        return $ret;
    }
}
