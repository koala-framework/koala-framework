<?php
class E3_Component_Decorator_Menu extends E3_Component_Decorator_Abstract
{
    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();

        $menus['/'] = 'Home';
        $menus['/test1/'] = 'decorator + paragraphs + 2x text';
        $menus['/test1/test2/'] = 'text';
        $menus['/news/'] = 'News';
        $menus['/pic/'] = 'Pic';
        $menus['/textpic/'] = 'Text + Pic';
        $menus['/events/'] = 'Events';
        foreach ($menus as $url=>$text) {
            $return['menu'][] = array('url'=>$url, 'text'=>$text);
        }
        return $return;
    }
}