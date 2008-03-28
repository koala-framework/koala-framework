<?php
function p($src, $maxDepth = 5) {
    ini_set('xdebug.var_display_max_depth', $maxDepth);
    if (is_object($src) && method_exists($src, '__toString')) {
        $src = $src->__toString();
    } else if (is_object($src) && method_exists($src, 'toDebug')) {
        echo $src->toDebug();
        return;
    }
    if (function_exists('xdebug_var_dump')) {
        xdebug_var_dump($src);
    } else {
        echo "<pre>";
        var_dump($src);
        echo "</pre>";
    }
}

function d($src, $maxDepth = 5)
{
    p($src, $maxDepth);
    exit;
}

function trl($string, $text = array()){

    //if abfrage wird bei smarty templates erfüllt
    if (is_array($string)){
        $tempstring = "";
        $text = array();
        foreach ($string as $key => $value){
            if ($key === "text"){
                $tempstring = $value;
            } else {
                $text[$key] = $value;
            }
        }
        $string = $tempstring;
    }
    $string = str_replace("[", "{", str_replace("]", "}", $string));
    return Zend_Registry::get('trl')->trl($string, $text, 'project');
}

function trlc($context, $string, $text = array()){

    //Sonderfall Smarty
    if (is_array($context)){
        $tempcontext = "";
        $text = array();
        foreach ($context as $key => $value){
            if ($key === "text"){
                $string = $value;
            } else if ($key === "context") {
                $tempcontext = $value;
            } else {
                $text[$key] = $value;
            }
        }
        $context = $tempcontext;
    }
    $string = str_replace("[", "{", str_replace("]", "}", $string));
    return Zend_Registry::get('trl')->trlc($context, $string, $text, 'project');
}

function trlp($single, $plural, $text =  array(), $prog_lang = 'php'){
    //Sonderfall Smarty
    if (is_array($single)){
        $tempsingle = "";
        $text = array();
        foreach ($single as $key => $value){
            if ($key === "single"){
                $tempsingle = $value;
            } else if ($key === "plural") {
                $plural = $value;
            } else {
                $text[$key] = $value;
            }
        }
        $prog_lang = "smarty";
        $single = $tempsingle;
    }
    $single = str_replace("[", "{", str_replace("]", "}", $single));
    $plural = str_replace("[", "{", str_replace("]", "}", $plural));
    return Zend_Registry::get('trl')->trlp($single, $plural, $text, 'project', $prog_lang);
}

function trlcp($context, $single, $plural = null, $text = array(), $prog_lang = 'php'){

    if (is_array($context)){
        $tempcontext = "";
        $text = array();
        foreach ($context as $key => $value){
            if ($key === "context"){
                $tempcontext = $value;
            } else if ($key === "single") {
                $single = $value;
            } else if ($key === "plural") {
                $plural = $value;
            }else {
                $text[$key] = $value;
            }
        }
        $context = $tempcontext;
        $prog_lang = "smarty";
    }
    $single = str_replace("[", "{", str_replace("]", "}", $single));
    $plural = str_replace("[", "{", str_replace("]", "}", $plural));

    return Zend_Registry::get('trl')->trlcp($context, $single, $plural, $text, 'project', $prog_lang);
}

function trlVps($string, $text = array()){
    //if abfrage wird bei smarty templates erfüllt
    if (is_array($string)){
        $tempstring = "";
        $text = array();
        foreach ($string as $key => $value){
            if ($key === "text"){
                $tempstring = $value;
            } else {
                $text[$key] = $value;
            }
        }
        $string = $tempstring;
    }
    $string = str_replace("[", "{", str_replace("]", "}", $string));
    return Zend_Registry::get('trl')->trl($string, $text, 'vps');
}

function trlcVps($context, $string, $text = array()){
        //Sonderfall Smarty
    if (is_array($context)){
        $tempcontext = "";
        $text = array();
        foreach ($context as $key => $value){
            if ($key === "text"){
                $string = $value;
            } else if ($key === "context") {
                $tempcontext = $value;
            } else {
                $text[$key] = $value;
            }
        }
        $context = $tempcontext;
    }
    $string = str_replace("[", "{", str_replace("]", "}", $string));
    return Zend_Registry::get('trl')->trlc($context, $string, $text, 'vps');
}

function trlpVps($single, $plural, $text =  array(), $prog_lang = 'php'){
        //Sonderfall Smarty
    if (is_array($single)){
        $tempsingle = "";
        $text = array();
        foreach ($single as $key => $value){
            if ($key === "single"){
                $tempsingle = $value;
            } else if ($key === "plural") {
                $plural = $value;
            } else {
                $text[$key] = $value;
            }
        }
        $prog_lang = "smarty";
        $single = $tempsingle;
    }
    $single = str_replace("[", "{", str_replace("]", "}", $single));
    $plural = str_replace("[", "{", str_replace("]", "}", $plural));
    return Zend_Registry::get('trl')->trlp($single, $plural, $text, 'vps', $prog_lang);
}

function trlcpVps($context, $single, $plural, $text = array(), $prog_lang= 'php'){
        if (is_array($context)){
        $tempcontext = "";
        $text = array();
        foreach ($context as $key => $value){
            if ($key === "context"){
                $tempcontext = $value;
            } else if ($key === "single") {
                $single = $value;
            } else if ($key === "plural") {
                $plural = $value;
            }else {
                $text[$key] = $value;
            }
        }
        $context = $tempcontext;
        $prog_lang = "smarty";
    }
    $single = str_replace("[", "{", str_replace("]", "}", $single));
    $plural = str_replace("[", "{", str_replace("]", "}", $plural));

    return Zend_Registry::get('trl')->trlcp($context, $single, $plural, $text, 'vps', $prog_lang);
}

//notwendig für die Erstellung der javascript methoden
function getTrlpValues ($context, $single, $plural, $mode){
    return Zend_Registry::get('trl')->getTrlpValues($context, $single, $plural, $mode);

}

class Vps_Setup
{
    public static function setUp()
    {
        require_once 'Vps/Loader.php';
        Vps_Loader::registerAutoload();

        Zend_Registry::setClassName('Vps_Registry');

        error_reporting(E_ALL);
        date_default_timezone_set('Europe/Berlin');
        //set_error_handler(array('Vps_Debug', 'handleError'), E_ALL);

        $ip = get_include_path();
        foreach (Zend_Registry::get('config')->includepath as $p) {
            $ip .= PATH_SEPARATOR . $p;
        }
        set_include_path($ip);

        Zend_Db_Table_Abstract::setDefaultAdapter(Zend_Registry::get('db'));
    }

    public static function createDb()
    {
        $dao = Zend_Registry::get('dao');
        return $dao->getDb();
    }

    public static function createDao()
    {
        return new Vps_Dao(new Zend_Config_Ini('application/config.db.ini', 'database'));
    }

    public static function createConfig()
    {
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';

        //www abschneiden damit www.test und www.preview usw auch funktionieren
        if (substr($host, 0, 4)== 'www.') $host = substr($host, 4);

        if (preg_match('#/www/(usr|public)/([0-9a-z-]+)/#', $_SERVER['SCRIPT_FILENAME'], $m)) {
            $vpsSection = $webSection = 'vivid';

            $webConfigFull = new Zend_Config_Ini('application/config.ini', null);
            if (isset($webConfigFull->{$m[2]})) {
                $webSection = $m[2];
            }

            $vpsConfigFull = new Zend_Config_Ini(VPS_PATH.'/config.ini', null);
            if (isset($vpsConfigFull->{$m[2]})) {
                $vpsSection = $m[2];
            }
        } else if (substr($host, 0, 4)=='dev.') {
            $vpsSection = $webSection = 'dev';
        } else if (substr($host, 0, 5)=='test.') {
            $vpsSection = $webSection = 'test';
        } else if (substr($host, 0, 8)=='preview.') {
            $vpsSection = $webSection = 'preview';
        } else {
            $vpsSection = $webSection = 'production';
        }

        $webConfig = new Zend_Config_Ini('application/config.ini', $webSection);

        $vpsConfig = new Zend_Config_Ini(VPS_PATH.'/config.ini', $vpsSection,
                        array('allowModifications'=>true));
        $vpsConfig->merge($webConfig);

        $v = $vpsConfig->application->vps->version;
        if (preg_match('#tags/vps/([^/]+)/config\\.ini#', $v, $m)) {
            $v = $m[1];
        } else if (preg_match('#branches/vps/([^/]+)/config\\.ini#', $v, $m)) {
            $v = $m[1];
        } else if (preg_match('#trunk/vps/config\\.ini#', $v, $m)) {
            $v = 'trunk';
        }
        $vpsConfig->application->vps->version = $v;
        if (preg_match('/Revision: ([0-9]+)/', $vpsConfig->application->vps->revision, $m)) {
            $vpsConfig->application->vps->revision = (int)$m[1];
        }
        return $vpsConfig;
    }
}
