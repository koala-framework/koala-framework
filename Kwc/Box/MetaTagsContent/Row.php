<?php
class Kwc_Box_MetaTagsContent_Row extends Kwf_Model_Proxy_Row
{
    public function __construct(array $config)
    {
        parent::__construct($config);
        if (!$this->sitemap_priority) {
            $this->sitemap_priority = 0.5;
        }
        if (!$this->sitemap_changefreq) {
            $this->sitemap_changefreq = 'weekly';
        }
    }
}
