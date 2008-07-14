<?php
class Vps_View_Helper_Truncate
{
    //borrowed from smarty
    public function truncate($string, $length = 80, $etc = '...',
                                    $break_words = false, $middle = false)
    {
        if ($length === false) return $string;

        if ($length == 0)
            return '';

        if (strlen($string) > $length) {
            $length -= strlen($etc);
            if (!$break_words && !$middle) {
                $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length+1));
            }
            if(!$middle) {
                return substr($string, 0, $length).$etc;
            } else {
                return substr($string, 0, $length/2) . $etc . substr($string, -$length/2);
            }
        } else {
            return $string;
        }
    }
}

