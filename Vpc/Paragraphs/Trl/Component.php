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

    public function getCacheVars()
    {
        $ret = parent::getCacheVars();
        foreach ($this->getData()->getChildComponents(array('generator'=>'paragraphs', 'ignoreVisible'=>true)) as $p) {
            $ret[] = array(
                'model' => $this->getChildModel(),
                'id' => $p->dbId,
                'field' => 'component_id'
            );
        }
        return $ret;
    }
}
