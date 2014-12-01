<?php
class Kwc_Blog_List_Feed_Component extends Kwc_Directories_List_Feed_Component
{
    protected function _getRssEntryByItem(Kwf_Component_Data $item)
    {
        $ret = parent::_getRssEntryByItem($item);

        $renderer = new Kwf_Component_Renderer_HtmlExport();
        $ret['description'] = $renderer->renderComponent($item->getChildComponent('-content'));

        $ret['lastUpdate'] = strtotime($item->publish_date);

        $ret['title'] = $item->row->title;
        return $ret;
    }
}
