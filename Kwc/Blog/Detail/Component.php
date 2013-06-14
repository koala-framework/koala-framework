<?php
class Kwc_Blog_Detail_Component extends Kwc_Directories_Item_Detail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['content'] = 'Kwc_Paragraphs_Component';
        $ret['cssClass'] = 'webStandard';
        $ret['placeholder']['backLink'] = trlKwfStatic('Back to overview');
        $ret['placeholder']['commentHeadline'] = trlKwfStatic('Leave a Reply');
        $ret['editComponents'] = array('content');
        $ret['flags']['hasFulltext'] = true;

        $ret['generators']['child']['component']['comments'] = 'Kwc_Blog_Comments_Directory_Component';

        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['title'] = $this->getData()->row->title;
        $ret['publish_date'] = $this->getData()->row->publish_date;
        $ret['author'] = $this->getData()->row->author_firstname.' '.$this->getData()->row->author_lastname;
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
        $new->author = $new->row->author_firstname.' '.$new->row->author_lastname;
    }

    public function getFulltextContent()
    {
        $ret = array();
        $ret['type'] = 'blog';
        if (isset($this->getData()->row->publish_date)) {
            $ret['created'] = new Kwf_DateTime($this->getData()->row->publish_date);
        }
        return $ret;
    }
}
