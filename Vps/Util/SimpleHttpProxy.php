<?php
class Vps_Util_SimpleHttpProxy
{
    /**
     * Proxy, der zB für cross-domain ajax requests verwendet werden kann
     *
     * @param string|array $hosts Erlaubte Hostnamen (RegExp erlaubt, ^ vorne und $ hinten werden autom. angefügt)
     */
    public static function dispatch($hostnames)
    {
        if (empty($_SERVER['REDIRECT_URL'])) return;

        if (!preg_match('#^/vps/proxy/?$#i', $_SERVER['REDIRECT_URL'])) return;

        if (is_string($hostnames)) {
            $hostnames = array($hostnames);
        }

        $proxyUrl = $_REQUEST['proxyUrl'];
        $proxyPostVars = $_POST;
        $proxyGetVars = $_GET;
        if (array_key_exists('proxyUrl', $proxyPostVars)) unset($proxyPostVars['proxyUrl']);
        if (array_key_exists('proxyUrl', $proxyGetVars)) unset($proxyGetVars['proxyUrl']);

        // host checking
        $proxyHost = parse_url($proxyUrl, PHP_URL_HOST);
        $matched = false;
        foreach ($hostnames as $hostname) {
            if (preg_match('/^'.$hostname.'$/i', $proxyHost)) {
                $matched = true;
                break;
            }
        }
        if (!$matched) return;

        // proxying
        $http = new Zend_Http_Client($proxyUrl);
        if (count($_POST)) {
            $http->setMethod(Zend_Http_Client::POST);
        } else {
            $http->setMethod(Zend_Http_Client::GET);
        }
        if (count($_GET)) $http->setParameterGet($proxyGetVars);
        if (count($_POST)) $http->setParameterPost($proxyPostVars);
        $response = $http->request();
        $headers = $response->getHeaders();
        if ($headers && !empty($headers['Content-type'])) {
            header("Content-Type: ".$headers['Content-type']);
        }
        echo $response->getBody();
        exit;
    }
}
