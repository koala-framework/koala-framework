<?php
class Vps_Debug
{
    static $_enabled = true;
    static $_view;

    public static function handleError($errno, $errstr, $errfile, $errline)
    {
        if (error_reporting() == 0) return; // error unterdrückt mit @foo()
        if ($errno == E_DEPRECATED && strpos($errfile, 'tcpdf/') !== false) {
            return;
        }
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    public static function handleException($exception, $ignoreCli = false)
    {
        if (!$ignoreCli && php_sapi_name() == 'cli') {
            file_put_contents('php://stderr', $exception->__toString()."\n");
            exit(1);
        }

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
        $template = strtolower(Zend_Filter::filterStatic($template, 'Word_CamelCaseToDash').'.tpl');
        if ($exception instanceof Vps_Exception_Abstract) $exception->log();

        if (!headers_sent()) header($header);
        try {
            echo $view->render($template);
        } catch (Exception $e) {
            echo '<pre>';
            echo $exception->getException()->__toString();
            echo "\n\n\nError happened while handling exception:";
            echo $e->__toString();
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
