<?php
abstract class Kwc_News_Detail_Abstract_Component extends Kwc_Directories_Item_Detail_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['content'] = 'Kwc_Paragraphs_Component';
        $ret['generators']['metaTags'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'Kwc_News_Detail_Abstract_MetaTags_Component',
        );
        $ret['rootElementClass'] = 'kwfUp-webStandard';
        $ret['placeholder']['backLink'] = '&laquo; '.trlKwfStatic('Back to overview');
        $ret['editComponents'] = array('content');
        $ret['flags']['hasFulltext'] = true;
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['title'] = $this->getData()->row->title;
        $ret['publish_date'] = $this->getData()->row->publish_date;
        return $ret;
    }

    public function hasContent()
    {
        return $this->getData()->getChildComponent('-content')->hasContent();
    }

    public static function modifyItemData(Kwf_Component_Data $new)
    {
        parent::modifyItemData($new);
        $new->publish_date = $new->row->publish_date;
        $new->teaser = $new->row->teaser;
    }

    public function getFulltextContent()
    {
        $ret = array();
        $ret['type'] = 'news';
        if (isset($this->getData()->row->publish_date)) {
            $ret['created'] = new Kwf_DateTime($this->getData()->row->publish_date);
        }
        return $ret;
    }
}
