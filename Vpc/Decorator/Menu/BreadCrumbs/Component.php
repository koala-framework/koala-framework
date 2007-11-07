<?php
/**
 * @package Vpc
 * @subpackage Decorator
 */
class Vpc_Decorator_Menu_BreadCrumbs_Component extends Vpc_Decorator_Abstract
{
    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $pc = $this->_pageCollection;

        $page = $pc->getCurrentPage();
        $return['menu']['breadCrumbs'] = array();
        while ($page) {
            $url = $pc->getUrl($page);
            $text = $pc->getFilename($page);
            $return['menu']['breadCrumbs'][] = array('href' => $url,
                                                     'text' => $text,
                                                     'rel'  => '');
            $page = $pc->getParentPage($page);
        }
        $return['menu']['breadCrumbs'] = array_reverse($return['menu']['breadCrumbs']);

        return $return;
    }
}
