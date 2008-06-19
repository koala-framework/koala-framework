<?php
class Vps_View_Helper_ComponentLink
{
    public function componentLink($m, $text = null)
    {
        if ($m instanceof Vps_Component_Data) {
            $m = $m->getPage();
            $m = array(
                'href' => $m->url,
                'rel' => $m->rel,
                'text' => $m->name
            );
        }
        if (!$text) $text = $m['text'];
        return '<a href="'.$m['href'].'" rel="'.$m['rel'].'">'.$text.'</a>';
    }
}
