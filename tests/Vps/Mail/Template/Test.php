<?php
/**
 * @group Mail
 * @group Mail_Template
 */
class Vps_Mail_Template_Test extends PHPUnit_Framework_TestCase
{
    private $_root;
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Mail_Template_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testMailComponent()
    {
        $path = realpath(dirname(__FILE__));

        $c = $this->_root->getChildComponent('-both');
        $m = new Vps_Mail_Template($c);
        $this->assertEquals($path.'/Both/Component.txt.tpl', realpath($m->getTxtTemplate()));
        $this->assertEquals($path.'/Both/Component.html.tpl', realpath($m->getHtmlTemplate()));
        $this->assertEquals($c->componentClass, $m->getTemplateForDbVars());

        $c = $this->_root->getChildComponent('-both');
        $m = new Vps_Mail_Template($c->getComponent());
        $this->assertEquals($path.'/Both/Component.txt.tpl', realpath($m->getTxtTemplate()));
        $this->assertEquals($path.'/Both/Component.html.tpl', realpath($m->getHtmlTemplate()));
        $this->assertEquals($c->componentClass, $m->getTemplateForDbVars());

        $c = $this->_root->getChildComponent('-both');
        $classname = get_class($c->getComponent());
        $m = new Vps_Mail_Template($classname);
        $this->assertEquals($path.'/Both/Component.txt.tpl', realpath($m->getTxtTemplate()));
        $this->assertEquals($path.'/Both/Component.html.tpl', realpath($m->getHtmlTemplate()));
        $this->assertEquals($c->componentClass, $m->getTemplateForDbVars());


        $c = $this->_root->getChildComponent('-txtonly');
        $m = new Vps_Mail_Template($c);
        $this->assertEquals($path.'/TxtOnly/Component.txt.tpl', realpath($m->getTxtTemplate()));
        $this->assertEquals(null, $m->getHtmlTemplate());
        $this->assertEquals($c->componentClass, $m->getTemplateForDbVars());
    }

    public function testMailString()
    {
        $m = new Vps_Mail_Template('UserActivation');
        $this->assertEquals('mails/UserActivation.txt.tpl', $m->getTxtTemplate());
        $this->assertEquals('mails/UserActivation.html.tpl', $m->getHtmlTemplate());
        $this->assertEquals('UserActivation', $m->getTemplateForDbVars());
    }

    public function testMailSending()
    {
        $mockMail = $this->getMock('Vps_Mail', array('send'));

        $c = $this->_root->getChildComponent('-both');
        $m = new Vps_Mail_Template($c);
        $m->getView()->addScriptPath('.');
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
        $m = new Vps_Mail_Template(dirname(__FILE__));
    }

    /**
     * @expectedException Vps_Exception
     */
    public function testNotExistingFileComponentData()
    {
        $c = $this->_root->getChildComponent('-notpl');
        $m = new Vps_Mail_Template($c);
    }

    /**
     * @expectedException Vps_Exception
     */
    public function testNotExistingTxt()
    {
        $c = $this->_root->getChildComponent('-htmlonly');
        $m = new Vps_Mail_Template($c);
    }

    /**
     * @expectedException Vps_Exception
     */
    public function testNotExistingFile()
    {
        new Vps_Mail_Template('DoesNotExist');
    }
}
