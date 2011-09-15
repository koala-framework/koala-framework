<?php
class Vps_Exception_TestView extends Vps_View
{
    public $template;

    public function render($name){
        $this->template = $name;
    }
}
/**
 * @group Exception
 */
class Vps_Exception_ExceptionTest extends Vps_Test_TestCase
{
    public function testExceptions()
    {
        // Ohne Mail
        $exception = new Vps_Exception();
        $view = $this->_processException($exception);
        $this->assertEquals($view->message, $exception->getMessage());
        $this->assertTrue($view->debug);
        $this->assertEquals($view->template, 'error.tpl');

        // Mit Mail
        Zend_Registry::get('config')->debug->error->log = true;
        Vps_Config::deleteValueCache('debug.error.log');
        $exception = new Vps_Exception();
        $view = $this->_processException($exception);
        $this->assertEquals($view->message, $exception->getMessage());
        $this->assertFalse($view->debug);
        $this->assertEquals($view->template, 'error.tpl');

        // Nicht-Vps_Exception mit Mail
        Zend_Registry::get('config')->debug->error->log = true;
        Vps_Config::deleteValueCache('debug.error.log');
        $e = new Zend_Exception();
        $exception = new Vps_Exception_Other($e);
        $view = $this->_processException($exception);
        $this->assertEquals($view->message, $e->getMessage());
        $this->assertFalse($view->debug);
        $this->assertEquals($view->template, 'error.tpl');
        Zend_Registry::get('config')->debug->error->log = false;
        Vps_Config::deleteValueCache('debug.error.log');

        // Vps_Exception_NoLog mit Debug
        Zend_Registry::get('config')->debug->error->log = true;
        Vps_Config::deleteValueCache('debug.error.log');
        $exception = new Vps_Exception_NoLog();
        $view = $this->_processException($exception);
        $this->assertEquals($view->message, $exception->getMessage());
        $this->assertFalse($view->debug);
        Zend_Registry::get('config')->debug->error->log = false;
        Vps_Config::deleteValueCache('debug.error.log');
        $this->assertEquals($view->template, 'error.tpl');

        // Vps_Exception_NoLog ohne Debug
        $exception = new Vps_Exception_NoLog();
        $view = $this->_processException($exception);
        $this->assertEquals($view->message, $exception->getMessage());
        $this->assertTrue($view->debug);
        $this->assertEquals($view->template, 'error.tpl');

        // Vps_Exception_NotFound
        $exception = new Vps_Exception_NotFound();
        $view = $this->_processException($exception);
        $this->assertEquals($view->message, $exception->getMessage());
        $this->assertEquals($view->template, 'error404.tpl');

        // Nicht-Vps_Exception
        $exception = new Exception();
        $view = $this->_processException($exception);
        $this->assertEquals($view->message, $exception->getMessage());
        $this->assertEquals($view->template, 'error.tpl');

        // ClientException
        $exception = new Vps_ClientException();
        $view = $this->_processException($exception);
        $this->assertEquals($view->message, $exception->getMessage());
        $this->assertEquals($view->template, 'error-client.tpl');
    }

    private function _processException($exception)
    {
        $view = new Vps_Exception_TestView();
        Vps_Debug::setView($view);
        Vps_Debug::handleException($exception, true);
        return $view;
    }
    /**
     * @group slow
     */
    public function testController()
    {
        $d = Zend_Registry::get('testDomain');
        $testCookie = md5(uniqid('testId', true));

        $client = new Zend_Http_Client("http://$d/vps/test/vps_exception_test/access-denied");
        $client->setCookie('unitTest', $testCookie);
        $response = $client->request();
        $this->assertEquals(401, $response->getStatus());

        $client = new Zend_Http_Client("http://$d/vps/test/vps_exception_test/not-found");
        $client->setCookie('unitTest', $testCookie);
        $response = $client->request();
        $this->assertEquals(404, $response->getStatus());

        $client = new Zend_Http_Client("http://$d/vps/test/vps_exception_test/client");
        $client->setCookie('unitTest', $testCookie);
        $response = $client->request();
        $this->assertEquals(200, $response->getStatus());
        $this->assertContains('client exception', $response->getBody());

        $client = new Zend_Http_Client("http://$d/vps/test/vps_exception_test/exception");
        $client->setCookie('unitTest', $testCookie);
        $response = $client->request();
        $this->assertEquals(500, $response->getStatus());

        $client = new Zend_Http_Client("http://$d/vps/test/vps_exception_test/exception-other");
        $client->setCookie('unitTest', $testCookie);
        $response = $client->request();
        $this->assertEquals(500, $response->getStatus());
    }
}
