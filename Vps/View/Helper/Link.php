<?php
class Vps_View_Helper_Link
{
    public function link($url)
    {
        $withHttp = $url;
        if (substr($withHttp, 0, 7) != 'http://') {
            $withHttp = 'http://'.$withHttp;
        }
        $withoutHttp = substr($withHttp, 7);
        return '<a href="'.$withHttp.'" target="_blank">'.$withoutHttp.'</a>';
    }
}
