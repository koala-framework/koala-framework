<?php
class Kwc_FulltextSearch_Search_Detail_Component extends Kwc_Directories_Item_Detail_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);

        $ret['queryParts'] = $this->getData()->parent->getChildComponent('-searchForm')->getComponent()->getFormRow()->query;

        $ret['breadcrumbs'] = array();

        $highlightTermsHelper = new Kwf_View_Helper_HighlightTerms();
        // fake component from external solr in Kwf_Util_Fulltext_Backend_Solr
        if (isset($this->getData()->row->data->externalUrl) && $this->getData()->row->data->externalUrl) {
            $this->getData()->row->data->url = $this->getData()->row->data->externalUrl;
            // $ret['breadcrumbs'] = 'Shop'; // todo
            $ret['linkText'] = $this->getData()->row->title;
            // $ret['imageUrl'] = $this->getData()->row->data->imageUrl; // todo
            // $ret['price'] = $this->getData()->row->data->price; // todo
            $ret['previewText'] = $highlightTermsHelper->highlightTerms($ret['queryParts'], $this->getData()->row->content);
            $ret['external'] = true;
            $ret['template'] = $renderer->getTemplate($this->getData(), 'Component.external');
        } else {

            $page = $this->getData()->row->data->getPage();
            if ($page->getChildComponent('-metaTags')
                && $page->getChildComponent('-metaTags')->getComponent()->getRow()
            ) {
                $ret['title'] = $page->getChildComponent('-metaTags')->getComponent()->getRow()->og_title;
            } else {
                $ret['title'] = $page->getName();
            }
            do {
                $ret['breadcrumbs'][] = $page->getName();
            } while ($page = $page->getParentPage());
            $ret['breadcrumbs'] = implode(' / ', array_reverse($ret['breadcrumbs']));

            $ret['linkText'] = $this->getData()->row->data->name;
            $ret['highLightedContent'] = $highlightTermsHelper->highlightTerms($ret['queryParts'], $this->getData()->row->content);
            $ret['highLightedName'] = $highlightTermsHelper->highlightTerms($ret['queryParts'], $this->getData()->row->data->name);
            $ret['template'] = $renderer->getTemplate($this->getData(), 'Component');
        }

        return $ret;
    }
}
