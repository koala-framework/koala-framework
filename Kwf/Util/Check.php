<?php
class Kwf_Util_Check
{
    public static function dispatch()
    {
        $ok = true;
        $msg = '';
        if (file_exists('instance_startup')) {
            //can be used while starting up autoscaling instances
            $ok = false;
            $msg .= 'instance startup in progress';
        }
        if (!$ok) {
            header("HTTP/1.0 500 Error");
            echo "<h1>Check failed</h1>";
            echo $msg;
        } else {
            echo "ok";
        }
        exit;
    }
}
