<?php
class Kwc_Articles_Detail_Component extends Kwc_Directories_Item_Detail_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['content'] = 'Kwc_Articles_Detail_Paragraphs_Component';
        $ret['generators']['child']['component']['previewImage'] = 'Kwc_Articles_Detail_PreviewImage_Component';

        $ret['rootElementClass'] = 'kwfUp-webStandard';

        $ret['flags']['hasFulltext'] = true;
        $ret['flags']['processInput'] = true;

        $ret['editComponents'] = array('content');

        $ret['assetsAdmin']['dep'][] = 'ExtFormDateField';
        return $ret;
    }

    public function processInput($input)
    {
        $this->getData()->row->markRead();
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['config'] = array(
            'isTopArticle' => ($this->getData()->getRow()->is_top) ? 1 : 0
        );
        $ret['title'] = $this->getData()->row->title;
        $ret['author'] = $this->getData()->row->getParentRow('Author');
        return $ret;
    }

    public function getFulltextContent()
    {
        $ret = array();
        $ret['type'] = 'article';
        $ret['created'] = new Kwf_DateTime($this->getData()->row->date);
        $ret['only_intern'] = (bool)$this->getData()->row->only_intern;
        return $ret;
    }

    public static function modifyItemData(Kwf_Component_Data $item)
    {
        $item->title = $item->row->title;
        $item->teaser = $item->row->teaser;
        $item->date = $item->row->date;
    }
}
