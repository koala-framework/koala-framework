<?php
class Kwc_Box_MetaTagsContent_Model extends Kwf_Component_FieldModel
{
    protected $_rowClass = 'Kwc_Box_MetaTagsContent_Row';
    protected $_default = array(
        'sitemap_priority' => 0.5,
        'sitemap_changefreq' => 'weekly',
    );
}
