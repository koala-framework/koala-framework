<?php
class Kwc_Directories_List_ViewAjax_DetailContentSender extends Kwf_Component_Abstract_ContentSender_Default
{
    public function getLinkRel()
    {
        return 'ajaxDetail';
    }
/*
    private function _getParent()
    {
        $previous = null;
        $parent = $this->_data->parent;
        while ($parent && !$parent->isPage) {
            $previous = $parent;
            $parent = $parent->parent;
        }
        return $parent;
    }

    protected function _render($includeMaster)
    {
        $detailContent = $this->_data->render(null, false);
        if ($includeMaster) {
            $parent = $this->_getParent();
            $parentContentSender = Kwc_Abstract::getSetting($parent->componentClass, 'contentSender');
            $parentContentSender = new $parentContentSender($parent);
            $parentContent = $parentContentSender->_render($includeMaster);

            $parentContent = preg_replace(
                "#(<div class=\"[^\"]*".preg_quote(Kwc_Abstract::getCssClass($parent->getComponent()), '#')."[^\"]*)(\">)#",
                $detailContent.'\1 kwfViewAjaxHidden\2',
                $parentContent);
            return $parentContent;
        } else {
            return $detailContent;
        }
    }
*/
}
