<?php
class Vps_View_Helper_ComponentLink
{
    public function componentLink($m)
    {
        return '<a href="'.$m['href'].'" rel="'.$m['rel'].'">'.$m['text'].'</a>';
    }
}
