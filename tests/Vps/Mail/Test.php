<?php
class Vps_Mail_Test extends PHPUnit_Framework_TestCase
{
    private $_root;
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Mail_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testMailComponent()
    {
        $c = $this->_root->getChildComponent('-both');
        $m = new Vps_Mail($c);
        $this->assertEquals(dirname(__FILE__).'/Both/Component.txt.tpl', $m->getTxtTemplate());
        $this->assertEquals(dirname(__FILE__).'/Both/Component.html.tpl', $m->getHtmlTemplate());
        $this->assertEquals($c->componentClass, $m->getTemplateForDbVars());

        $c = $this->_root->getChildComponent('-both');
        $m = new Vps_Mail($c->getComponent());
        $this->assertEquals(dirname(__FILE__).'/Both/Component.txt.tpl', $m->getTxtTemplate());
        $this->assertEquals(dirname(__FILE__).'/Both/Component.html.tpl', $m->getHtmlTemplate());
        $this->assertEquals($c->componentClass, $m->getTemplateForDbVars());


        $c = $this->_root->getChildComponent('-txtonly');
        $m = new Vps_Mail($c);
        $this->assertEquals(dirname(__FILE__).'/TxtOnly/Component.txt.tpl', $m->getTxtTemplate());
        $this->assertEquals(null, $m->getHtmlTemplate());
        $this->assertEquals($c->componentClass, $m->getTemplateForDbVars());
    }

    public function testMailString()
    {
        $m = new Vps_Mail('UserActivation');
        $this->assertEquals('mails/UserActivation.txt.tpl', $m->getTxtTemplate());
        $this->assertEquals('mails/UserActivation.html.tpl', $m->getHtmlTemplate());
        $this->assertEquals('UserActivation', $m->getTemplateForDbVars());


        $m = new Vps_Mail('Bar');
        $this->assertEquals('mails/Bar.txt.tpl', $m->getTxtTemplate());
        $this->assertEquals(null, $m->getHtmlTemplate());
        $this->assertEquals('Bar', $m->getTemplateForDbVars());
    }

    public function testMailSending()
    {
        $mockMail = $this->getMock('Vps_Mail_Fixed', array('send'));

        $m = new Vps_Mail('Send');
        $m->setMailVarsClassName(null);
        $m->setMail($mockMail);
        $m->subject = 'a special subject';
        $m->foo = 'bar';
        $m->send();

        $this->assertEquals('a special subject', $m->getMail()->getSubject());
        $this->assertEquals('The foo variable is: bar', $m->getMail()->getBodyText(true));
        $this->assertEquals('The foo variable is:<br />bar', $m->getMail()->getBodyHtml(true));
    }

    /**
     * @expectedException Vps_Exception
     */
    public function testNoAbsolutePath()
    {
        $m = new Vps_Mail(dirname(__FILE__));
    }

    /**
     * @expectedException Vps_Exception
     */
    public function testNotExistingFileComponentData()
    {
        $c = $this->_root->getChildComponent('-notpl');
        $m = new Vps_Mail($c);
    }

    /**
     * @expectedException Vps_Exception
     */
    public function testNotExistingTxt()
    {
        $c = $this->_root->getChildComponent('-htmlonly');
        $m = new Vps_Mail($c);
    }

    /**
     * @expectedException Vps_Exception
     */
    public function testNotExistingFile()
    {
        new Vps_Mail('DoesNotExist');
    }
}
