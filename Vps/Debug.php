<?php
class Vps_Debug
{
    static $_enabled = true;
    static $_view;

    public static function handleError($errno, $errstr, $errfile, $errline)
    {
        if (error_reporting() == 0) return; // error unterdrückt mit @foo()
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    public static function handleException($exception)
    {
        if (php_sapi_name() == 'cli') d($exception);

        if (!$exception instanceof Vps_Exception_Abstract) {
            $exception = new Vps_Exception_Other($exception);
        }

        $view = self::getView();
        $view->exception = $exception->getException();
        $view->message = $exception->getException()->getMessage();
        $view->requestUri = isset($_SERVER['REQUEST_URI']) ?
            $_SERVER['REQUEST_URI'] : '' ;
        $view->debug = Vps_Exception::isDebug();

        $header = $exception->getHeader();
        $template = $exception->getTemplate();
        $template = strtolower(Zend_Filter::get($template, 'Word_CamelCaseToDash').'.tpl');
        if ($exception instanceof Vps_Exception_Abstract) $exception->log();

        if (!headers_sent()) header($header);
        try {
            echo $view->render($template);
        } catch (Exception $e) {
            echo '<pre>';
            print_r($e->__toString());
            echo '</pre>';
        }
        Vps_Benchmark::shutDown();
        Vps_Benchmark::output();
    }

    public static function setView(Vps_View $view)
    {
        self::$_view = $view;
    }

    public static function getView()
    {
        if (!self::$_view) self::$_view = new Vps_View();
        return self::$_view;
    }

    public static function enable()
    {
        self::$_enabled = true;
    }

    public static function disable()
    {
        p('debug output disabled');
        self::$_enabled = false;
    }

    public static function isEnabled()
    {
        return self::$_enabled;
    }
}
