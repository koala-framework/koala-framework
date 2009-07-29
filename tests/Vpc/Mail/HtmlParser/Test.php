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
        );
        $p = new Vpc_Mail_HtmlParser($styles);
        $html = '<p>Lorem Ipsum</p>';
        $html = $p->parse($html);
        $this->assertEquals('<p><font name="Verdana" size="3">Lorem Ipsum</font></p>', $html);

        $html = '<p class="red">Lorem Ipsum</p>';
        $html = $p->parse($html);
        $this->assertEquals('<p class="red"><font name="Verdana" size="3" color="red">Lorem Ipsum</font></p>', $html);

        $html = '<h1>Lorem Ipsum</h1>';
        $html = $p->parse($html);
        $this->assertEquals('<h1><font name="Verdana" size="4"><b>Lorem Ipsum</b></font></h1>', $html);
    }
}
