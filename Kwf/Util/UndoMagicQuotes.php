<?php
/**
 * If we can't configure the server to not use magic quotes deal with that and remove this cap
 *
 * Don't use it.
 */
class Kwf_Util_UndoMagicQuotes
{
    public static function undoMagicQuotes()
    {
        if (!get_magic_quotes_gpc()) {
            throw new Kwf_Exception("No need to call this function if magic_quotes are not enabled");
        }
        $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
        while (list($key, $val) = each($process)) {
            foreach ($val as $k => $v) {
                unset($process[$key][$k]);
                if (is_array($v)) {
                    $process[$key][stripslashes($k)] = $v;
                    $process[] = &$process[$key][stripslashes($k)];
                } else {
                    $process[$key][stripslashes($k)] = stripslashes($v);
                }
            }
        }
        unset($process);
    }
}
