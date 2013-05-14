<?php
class Kwc_Paragraphs_Trl_Component extends Kwc_Chained_Trl_Component
{

    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['componentIcon'] = new Kwf_Asset('page');
        $ret['generators']['paragraphs']['class'] = 'Kwc_Paragraphs_Trl_Generator';
        $ret['childModel'] = 'Kwc_Paragraphs_Trl_Model';
        $ret['extConfig'] = 'Kwc_Paragraphs_Trl_ExtConfig';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $paragraphs = $this->getData()->getChildComponents(array('generator'=>'paragraphs'));

        $paragraphsById = array();
        foreach ($paragraphs as $c) {
            $paragraphsById[$c->id] = $c;
        }

        foreach(array_keys($ret['paragraphs']) as $key) {
            $id = $ret['paragraphs'][$key]['data']->id;
            if (isset($paragraphsById[$id])) {
                $ret['paragraphs'][$key]['data'] = $paragraphsById[$id];
            } else {
                unset($ret['paragraphs'][$key]);
            }
        }
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
        $ret[] = new Kwc_Chained_Abstract_ParentIdCacheMeta(Kwc_Abstract::getSetting($componentClass, 'childModel'));
        return $ret;
    }
}
