<?php
class Kwc_Basic_Image_ApiContent implements Kwf_Component_ApiContent_Interface
{
    public function getContent(Kwf_Component_Data $data)
    {
        $component = $data->getComponent();
        $componentRow = $component->getRow();


        $renderer = new Kwf_Component_Renderer();
        $templateVars = $component->getTemplateVars($renderer);

        $domain = $data->getDomain();
        $protocol = Kwf_Util_Https::domainSupportsHttps($domain) ? 'https' : 'http';
        $url = "$protocol://$domain";

        return array(
            'caption' => $componentRow->image_caption,
            'alt' => $componentRow->alt_text,
            'title' => $componentRow->title_text,
            'enlarge' => $componentRow->enlarge,
            'dimension' => $componentRow->dimension,
            'baseUrl' => "$protocol://$domain${templateVars['baseUrl']}",
            'widthSteps' => $templateVars['widthSteps'],
            'url' => $component->getAbsoluteImageUrl()
        );
    }
}
