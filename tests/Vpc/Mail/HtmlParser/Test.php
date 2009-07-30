<?php
/**
 * @group MailHtmlParser
 */
class Vpc_Mail_HtmlParser_Test extends PHPUnit_Framework_TestCase
{
    public function testIt()
    {
        $styles = array(
            array(
                'tag' => 'p',
                'styles' => array(
                    'font-family' => 'Verdana',
                    'font-size' => '3'
                )
            ),
            array(
                'tag' => 'p',
                'class' => 'red',
                'styles' => array(
                    'font-family' => 'Verdana',
                    'font-size' => '3',
                    'color' => 'red'
                )
            ),
            array(
                'tag' => 'h1',
                'styles' => array(
                    'font-family' => 'Verdana',
                    'font-size' => '4',
                    'font-weight' => 'bold'
                )
            ),
            array(
                'tag' => 'strong',
                'replaceTag' => 'b'
            ),
        );
        $p = new Vpc_Mail_HtmlParser($styles);
        $html = '<p>Lorem Ipsum</p>';
        $html = $p->parse($html);
        $this->assertEquals('<p><font face="Verdana" size="3">Lorem Ipsum</font></p>', $html);

        $html = '<p class="red">Lorem Ipsum</p>';
        $html = $p->parse($html);
        $this->assertEquals('<p class="red"><font face="Verdana" size="3" color="red">Lorem Ipsum</font></p>', $html);

        $html = '<h1>Lorem Ipsum</h1>';
        $html = $p->parse($html);
        $this->assertEquals('<h1><font face="Verdana" size="4"><b>Lorem Ipsum</b></font></h1>', $html);

        $html = '<div>Lorem</div><div>Ipsum</div>';
        $html = $p->parse($html);
        $this->assertEquals('<div>Lorem</div><div>Ipsum</div>', $html);

        $html = '<strong foo="bar">Lorem Ipsum</strong>';
        $html = $p->parse($html);
        $this->assertEquals('<b>Lorem Ipsum</b>', $html);

        $html = 'Lorem<br />Ipsum';
        $html = $p->parse($html);
        $this->assertEquals('Lorem<br />Ipsum', $html);

        $html = 'Lorem<br>Ipsum';
        $html = $p->parse($html);
        $this->assertEquals('Lorem<br />Ipsum', $html);
    }
}
