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
class Vps_Exception_ExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testExceptions()
    {
        // Ohne Mail
        $exception = new Vps_Exception();
        $mail = $this->getMock('Zend_Mail', array('send'));
        $mail->expects($this->never())->method('send');
        $exception->setMail($mail);

        $view = $this->_processException($exception);
        $this->assertEquals($view->message, $exception->getMessage());
        $this->assertTrue($view->debug);
        $this->assertEquals($view->template, 'error.tpl');

        // Mit Mail
        Zend_Registry::get('config')->debug->errormail = 'foo';
        $exception = new Vps_Exception();
        $mail = $this->getMock('Zend_Mail', array('send'));
        $mail->expects($this->once())->method('send');
        $exception->setMail($mail);

        $view = $this->_processException($exception);
        $this->assertEquals($view->message, $exception->getMessage());
        $this->assertFalse($view->debug);
        $headers = $mail->getHeaders();
        $this->assertEquals($headers['To'][0], '<vperror@vivid-planet.com>');
        $this->assertEquals($headers['Cc'][0], '<foo>');
        $this->assertEquals($view->template, 'error.tpl');

        // Nicht-Vps_Exception mit Mail
        Zend_Registry::get('config')->debug->errormail = 'foo';
        $e = new Zend_Exception();
        $exception = new Vps_Exception_Other($e);
        $mail = $this->getMock('Zend_Mail', array('send'));
        $mail->expects($this->once())->method('send');
        $exception->setMail($mail);

        $view = $this->_processException($exception);
        $this->assertEquals($view->message, $e->getMessage());
        $this->assertFalse($view->debug);
        $headers = $mail->getHeaders();
        $this->assertEquals($headers['To'][0], '<vperror@vivid-planet.com>');
        $this->assertEquals($headers['Cc'][0], '<foo>');
        $this->assertEquals($view->template, 'error.tpl');
        Zend_Registry::get('config')->debug->errormail = false;

        // Vps_Exception_NoMail mit Debug
        Zend_Registry::get('config')->debug->errormail = 'foo';
        $exception = new Vps_Exception_NoMail();
        $view = $this->_processException($exception);
        $this->assertEquals($view->message, $exception->getMessage());
        $this->assertFalse($view->debug);
        Zend_Registry::get('config')->debug->errormail = false;
        $this->assertEquals($view->template, 'error.tpl');

        // Vps_Exception_NoMail ohne Debug
        $exception = new Vps_Exception_NoMail();
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
    }

    private function _processException($exception)
    {
        $view = new Vps_Exception_TestView();
        Vps_Debug::setView($view);
        Vps_Debug::handleException($exception);
        return $view;
    }
}
