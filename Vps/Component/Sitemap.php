<?php
class Vps_Component_Sitemap
{
    public function outputSitemap(Vps_Component_Data $page)
    {
        $xml = new XmlWriter();
        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('urlset');
        $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        foreach (array_unique($this->_getSitemap($page)) as $url) {
            $xml->startElement('url');
            $xml->writeElement('loc', $url);
            $xml->endElement();
        }
        $xml->endElement();
        $xml->endDocument();
        header('Content-Type: text/xml; charset=utf-8');
        echo $xml->outputMemory(true);
        exit;
    }

    private function _getSitemap(Vps_Component_Data $page)
    {
        $sites = array();
        if (is_instance_of($page->componentClass, 'Vpc_Mail_Redirect_Component') || // TODO: das gehört natürlich noch gscheit gemacht
            is_instance_of($page->componentClass, 'Vpc_Advanced_Amazon_Nodes_ProductsDirectory_Component')
        ) {
            return $sites;
        }

        if ($page->url) {
            $sites[] = $page->url;
        }
        foreach ($page->getChildPseudoPages(array(), array('pseudoPage'=>false)) as $childPage) {
            $sites = array_merge($sites, $this->_getSitemap($childPage));
        }
        return $sites;
    }
}
