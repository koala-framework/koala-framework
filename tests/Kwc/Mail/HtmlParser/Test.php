<?php
/**
 * @group MailHtmlParser
 */
class Kwc_Mail_HtmlParser_Test extends Kwf_Test_TestCase
{
    private function _assertHtmlEquals($expected, $html)
    {
        $html = preg_replace('#^ +#m', '', $html);
        $html = str_replace("\n", '', $html);
        $this->assertEquals($expected, $html);
    }

    public function testIt()
    {
        $styles = array(
            array(
                'tag' => 'p',
                'styles' => array(
                    'font-family' => 'Verdana',
                    'font-size' => '18px'
                )
            ),
            array(
                'tag' => 'p',
                'class' => 'red',
                'styles' => array(
                    'font-family' => 'Verdana',
                    'font-size' => '18px',
                    'color' => 'red'
                )
            ),
            array(
                'tag' => 'h1',
                'styles' => array(
                    'font-family' => 'Verdana',
                    'font-size' => '24px',
                    'font-weight' => 'bold'
                )
            ),
            array(
                'tag' => 'strong',
                'replaceTag' => 'b'
            ),
        );
        $p = new Kwc_Mail_HtmlParser($styles);
        $html = '<p>Lorem Ipsum</p>';
        $html = $p->parse($html);
        $this->_assertHtmlEquals('<p style="font-size: 18px; "><font face="Verdana">Lorem Ipsum</font></p>', $html);

        $html = '<p class="red">Lorem Ipsum</p>';
        $html = $p->parse($html);
        $this->_assertHtmlEquals('<p class="red" style="font-size: 18px; "><font face="Verdana" color="red">Lorem Ipsum</font></p>', $html);

        $html = '<h1>Lorem Ipsum</h1>';
        $html = $p->parse($html);
        $this->_assertHtmlEquals('<h1 style="font-size: 24px; "><font face="Verdana"><b>Lorem Ipsum</b></font></h1>', $html);

        $html = '<div>Lorem</div><div>Ipsum</div>';
        $html = $p->parse($html);
        $this->_assertHtmlEquals('<div>Lorem</div><div>Ipsum</div>', $html);

        $html = '<strong foo="bar">Lorem Ipsum</strong>';
        $html = $p->parse($html);
        $this->_assertHtmlEquals('<p style="font-size: 18px; "><font face="Verdana"><b>Lorem Ipsum</b></font></p>', $html);

        $html = 'Lorem<br />Ipsum';
        $html = $p->parse($html);
        $this->_assertHtmlEquals('<p style="font-size: 18px; "><font face="Verdana">Lorem<br />Ipsum</font></p>', $html);

        // wenn man ein nicht geschlossenes <br> rein gibt, macht das xml einen fehler,
        // geht aber normal weiter, deshalb dieser teil auskommentiert. siehe auch den kommentar beim HtmlParser
/*        $html = 'Lorem<br>Ipsum';
        $html = $p->parse($html);
        $this->_assertHtmlEquals('Lorem<br />Ipsum', $html);*/
    }

    public function testMore()
    {
        $styles = array(
            array(
                'tag' => 'td',
                'styles' => array(
                    'font-family' => 'Verdana',
                    'font-size' => '12px'
                ),
            )
        );
        $html = '<table><tr><td>Guten Tag, &NBSP; <strong>Frau Staterau!</strong></td><td>&nbsp;</td><td>Testtext</td></tr></table>';
        $expected= '<table><tr><td style="font-size: 12px; "><font face="Verdana">Guten Tag, &NBSP; <strong>Frau Staterau!</strong></font></td><td style="font-size: 12px; "><font face="Verdana">&nbsp;</font></td><td style="font-size: 12px; "><font face="Verdana">Testtext</font></td></tr></table>';
        $p = new Kwc_Mail_HtmlParser($styles);
        $html = $p->parse($html);
        $this->_assertHtmlEquals($expected, $html);

    }

    public function testSelector()
    {
        $styles = array(
            array(
                'tag' => 'p',
                'styles' => array(
                    'font-size' => '18px'
                ),
            ),
            array(
                'selector' => 'table.foo p',
                'styles' => array(
                    'font-size' => '12px'
                ),
            )
        );
        $html  = '<table><tr><td><p>Blu bla Bli</p></td></tr></table>';
        $html .= '<table class="foo"><tr><td><p>Blu bla Bli</p></td></tr></table>';
        $expected  = '<table><tr><td><p style="font-size: 18px; ">Blu bla Bli</p></td></tr></table>';
        $expected .= '<table class="foo"><tr><td><p style="font-size: 12px; ">Blu bla Bli</p></td></tr></table>';
        $p = new Kwc_Mail_HtmlParser($styles);
        $html = $p->parse($html);
        $this->_assertHtmlEquals($expected, $html);

    }
}
