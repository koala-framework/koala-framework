<?php
class Kwc_Tags_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Tags');
        $ret['flags']['hasFulltext'] = true;
        $ret['generators']['child']['component']['suggestions'] = 'Kwc_Tags_Suggestions_Component';
        $ret['menuConfig'] = 'Kwc_Tags_MenuConfig';
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_Grid';
        $ret['assetsAdmin']['dep'][] = 'KwfFormSuperBoxSelect';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $model = Kwf_Model_Abstract::getInstance('Kwc_Tags_ComponentToTag');
        $select = new Kwf_Model_Select();
        $select->whereEquals('component_id', $this->getData()->dbId);
        $select->expr('tag_name');
        $ret['tags'] = array();
        foreach ($model->getRows($select) as $tag) {
            $ret['tags'][] = $tag->tag_name;
        }
        $ret['headline'] = $this->getData()->trlStaticExecute($this->_getSetting('componentName'));
        return $ret;
    }

    public function getFulltextContent()
    {
        $ret = array();
        $model = Kwf_Model_Abstract::getInstance('Kwc_Tags_ComponentToTag');
        $select = new Kwf_Model_Select();
        $select->whereEquals('component_id', $this->getData()->dbId);
        $select->expr('tag_name');
        $tags = array();
        foreach ($model->getRows($select) as $tag) {
            $tags[] = $tag->tag_name;
        }
        //TODO: better separator that also understands solr?
        $ret['keywords'] = implode(' ', $tags);
        return $ret;
    }
}
