<?php
class Kwc_News_Detail_Abstract_MetaTags_Component extends Kwc_Box_MetaTagsContent_Component
{
    protected function _getMetaTags()
    {
        $ret = parent::_getMetaTags();
        $row = $this->getData()->parent->row;
        if ($teaser = $row->teaser) {
            $ret['description'] = $teaser;
        }
        return $ret;

    }

}

