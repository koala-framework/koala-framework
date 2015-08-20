<?php
class Kwf_Component_Sitemap
{
    public function outputSitemap(Kwf_Component_Data $page)
    {
        header('Content-Type: text/xml; charset=utf-8');
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
        foreach ($this->_getSitemap($page) as $url) {
            echo "<url>\n";
            echo " <loc>".htmlspecialchars($url)."</loc>\n";
            echo "</url>\n";
        }
        echo "</urlset>\n";
        exit;
    }

    private function _getSitemap(Kwf_Component_Data $page)
    {
        $sites = array();
        if (substr($page->url, 0, 1) == '/') {
            $sites[] = $page->getAbsoluteUrl();
        }
        foreach ($page->getChildPseudoPages(array('pageGenerator' => true)) as $childPage) {
            $sites = array_merge($sites, $this->_getSitemap($childPage));
        }
        return $sites;
    }
}
