<?php
class Kwf_Exception_TestView extends Kwf_View
{
    public $template;

    public function render($name){
        $this->template = $name;
    }
}
/**
 * @group Exception
 */
class Kwf_Exception_ExceptionTest extends Kwf_Test_TestCase
{
    public function tearDown()
    {
        parent::tearDown();
        Kwf_Exception_Abstract::$logErrors = null;
    }

    public function testExceptions()
    {
        // Ohne Mail
        $exception = new Kwf_Exception();
        $view = $this->_processException($exception);
        $this->assertEquals($view->message, $exception->getMessage());
        $this->assertTrue($view->debug);
        $this->assertEquals($view->template, 'error.tpl');

        // Mit Mail
        Kwf_Exception_Abstract::$logErrors = true;
        $exception = new Kwf_Exception();
        $view = $this->_processException($exception);
        $this->assertEquals($view->message, $exception->getMessage());
        $this->assertFalse($view->debug);
        $this->assertEquals($view->template, 'error.tpl');

        // Nicht-Kwf_Exception mit Mail
        Kwf_Exception_Abstract::$logErrors = true;
        $e = new Zend_Exception();
        $exception = new Kwf_Exception_Other($e);
        $view = $this->_processException($exception);
        $this->assertEquals($view->message, $e->getMessage());
        $this->assertFalse($view->debug);
        $this->assertEquals($view->template, 'error.tpl');
        Kwf_Exception_Abstract::$logErrors = null;

        // Kwf_Exception_NoLog mit Debug
        Kwf_Exception_Abstract::$logErrors = true;
        $exception = new Kwf_Exception_NoLog();
        $view = $this->_processException($exception);
        $this->assertEquals($view->message, $exception->getMessage());
        $this->assertFalse($view->debug);
        Kwf_Exception_Abstract::$logErrors = null;
        $this->assertEquals($view->template, 'error.tpl');

        // Kwf_Exception_NoLog ohne Debug
        $exception = new Kwf_Exception_NoLog();
        $view = $this->_processException($exception);
        $this->assertEquals($view->message, $exception->getMessage());
        $this->assertTrue($view->debug);
        $this->assertEquals($view->template, 'error.tpl');

        // Kwf_Exception_NotFound
        $exception = new Kwf_Exception_NotFound();
        $view = $this->_processException($exception);
        $this->assertEquals($view->message, $exception->getMessage());
        $this->assertEquals($view->template, 'error404.tpl');

        // Nicht-Kwf_Exception
        $exception = new Exception();
        $view = $this->_processException($exception);
        $this->assertEquals($view->message, $exception->getMessage());
        $this->assertEquals($view->template, 'error.tpl');

        // ClientException
        $exception = new Kwf_ClientException();
        $view = $this->_processException($exception);
        $this->assertEquals($view->message, $exception->getMessage());
        $this->assertEquals($view->template, 'error-client.tpl');
    }

    private function _processException($exception)
    {
        if (!$exception instanceof Kwf_Exception_Abstract) {
            $exception = new Kwf_Exception_Other($exception);
        }

        $view = new Kwf_Exception_TestView();
        Kwf_Debug::setView($view);
        $exception->render(true);
        return $view;
    }
    /**
     * @group slow
     */
    public function testController()
    {
        $d = Zend_Registry::get('testDomain');
        $testCookie = md5(uniqid('testId', true));

        $client = new Zend_Http_Client("http://$d/kwf/test/kwf_exception_test/access-denied");
        $client->setCookie('unitTest', $testCookie);
        $response = $client->request();
        $this->assertEquals(401, $response->getStatus());

        $client = new Zend_Http_Client("http://$d/kwf/test/kwf_exception_test/not-found");
        $client->setCookie('unitTest', $testCookie);
        $response = $client->request();
        $this->assertEquals(404, $response->getStatus());

        $client = new Zend_Http_Client("http://$d/kwf/test/kwf_exception_test/client");
        $client->setCookie('unitTest', $testCookie);
        $response = $client->request();
        $this->assertEquals(200, $response->getStatus());
        $this->assertContains('client exception', $response->getBody());

        $client = new Zend_Http_Client("http://$d/kwf/test/kwf_exception_test/exception");
        $client->setCookie('unitTest', $testCookie);
        $response = $client->request();
        $this->assertEquals(500, $response->getStatus());

        $client = new Zend_Http_Client("http://$d/kwf/test/kwf_exception_test/exception-other");
        $client->setCookie('unitTest', $testCookie);
        $response = $client->request();
        $this->assertEquals(500, $response->getStatus());
    }
}
