<?php
class Vps_View_Helper_ComponentLink
{
    public function componentLink($m, $text = null)
    {
        if ($m instanceof Vps_Dao_Row_TreeCache) {
            $si = $m->getTable()->showInvisible();
            $m = array(
                'href' => $si ? $m->url_preview : $m->url,
                'rel' => $si ? $m->rel_preview : $m->rel,
                'text' => $m->name
            );
        }
        if (!$text) $text = $m['text'];
        return '<a href="'.$m['href'].'" rel="'.$m['rel'].'">'.$text.'</a>';
    }
}
