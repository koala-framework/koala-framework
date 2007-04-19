<?php
class Vps_Component_Decorator_Menu extends Vps_Component_Decorator_Abstract
{
    public function getTemplateVars($mode)
    {
        $return = parent::getTemplateVars($mode);

        $menus['/'] = 'Home';
        $menus['/test1/'] = 'decorator + paragraphs + 2x text';
        $menus['/test1/test2/'] = 'text';
        $menus['/news/'] = 'News';
        $menus['/pic/'] = 'Pic';
        $menus['/textpic/'] = 'Text + Pic';
        $menus['/events/'] = 'Events';
        $menus['/tagcloud/'] = 'Tag-Cloud';
        $menus['/products/'] = 'Produkte';
        foreach ($menus as $url=>$text) {
            $return['menu'][] = array('url'=>$url, 'text'=>$text);
        }
        return $return;
    }
}