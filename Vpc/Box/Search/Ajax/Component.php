<?php
class Vpc_Box_Search_Ajax_Component extends Vpc_Abstract_Ajax_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars()
    {
        $parent = $this->getData()->parent->getComponent();
        $ret = parent::getTemplateVars();
        $searchViews = $parent->getSearchViews();
        $formData = $parent->getSearchFormData();
        $ret['queryValue'] = $formData['queryValue'];
        $ret['foundNothing'] = true;
        foreach ($searchViews as $key => $view) {
            $addList = array();
            $addList['component'] = $view;
            $addList['title'] = is_numeric($key) ? null : $key;
            if ($addList['component']->getComponent()->getPagingCount() > 0) {
                $ret['foundNothing'] = false;
            } else {
                continue;
            }

            if ($addList['component']->getComponent()->moreItemsAvailable()) {
                $ret['queryValue'] = $formData['queryValue'];
                $addList['showAllHref'] =
                    $formData['searchPageUrl'] . '?' .
                    $formData['queryParam'] . '=' . $ret['queryValue'] . '&' .
                    $formData['submitParam'] . '=submit';
            } else {
                $addList['showAllHref'] = null;
            }
            $ret['lists'][] = $addList;
        }
        return $ret;
    }
}
