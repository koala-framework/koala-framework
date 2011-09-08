<?php
class Vpc_Basic_Link_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'ownModel' => 'Vpc_Basic_Link_Model',
            'componentName' => trlVps('Link'),
            'componentIcon' => new Vps_Asset('page_white_link'),
            'default' => array(),
        ));
        $ret['generators']['child']['component'] = array(
            'linkTag' => 'Vpc_Basic_LinkTag_Component',
        );
        $ret['flags']['searchContent'] = true;
        $ret['flags']['hasFulltext'] = true;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['text'] = $this->_getRow()->text;
        return $ret;
    }

    public function getSearchContent()
    {
        return $this->_getRow()->text;
    }

    public function modifyFulltextDocument(Zend_Search_Lucene_Document $doc)
    {
        $doc->getField('content')->value .= ' '.$this->_getRow()->text;
        $doc->getField('normalContent')->value .= ' '.$this->_getRow()->text;
        return $doc;
    }

    public function hasContent()
    {
        if (!$this->_getRow()->text) return false;
        return parent::hasContent();
    }
}
