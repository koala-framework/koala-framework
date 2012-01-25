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
        $p = new Kwc_Mail_HtmlParser($styles);
        $html = '<p>Lorem Ipsum</p>';
        $html = $p->parse($html);
        $this->_assertHtmlEquals('<p><font face="Verdana" size="3">Lorem Ipsum</font></p>', $html);

        $html = '<p class="red">Lorem Ipsum</p>';
        $html = $p->parse($html);
        $this->_assertHtmlEquals('<p class="red"><font face="Verdana" size="3" color="red">Lorem Ipsum</font></p>', $html);

        $html = '<h1>Lorem Ipsum</h1>';
        $html = $p->parse($html);
        $this->_assertHtmlEquals('<h1><font face="Verdana" size="4"><b>Lorem Ipsum</b></font></h1>', $html);

        $html = '<div>Lorem</div><div>Ipsum</div>';
        $html = $p->parse($html);
        $this->_assertHtmlEquals('<div>Lorem</div><div>Ipsum</div>', $html);

        $html = '<strong foo="bar">Lorem Ipsum</strong>';
        $html = $p->parse($html);
        $this->_assertHtmlEquals('<p><font face="Verdana" size="3"><b>Lorem Ipsum</b></font></p>', $html);

        $html = 'Lorem<br />Ipsum';
        $html = $p->parse($html);
        $this->_assertHtmlEquals('<p><font face="Verdana" size="3">Lorem<br />Ipsum</font></p>', $html);

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
                    'font-size' => '2'
                ),
            )
        );
        $html = '<table><tr><td>Guten Tag, &NBSP; <strong>Frau Staterau!</strong></td><td>&nbsp;</td><td>Testtext</td></tr></table>';
        $expected= '<table><tr><td><font face="Verdana" size="2">Guten Tag, &NBSP; <strong>Frau Staterau!</strong></font></td><td><font face="Verdana" size="2">&nbsp;</font></td><td><font face="Verdana" size="2">Testtext</font></td></tr></table>';
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
                    'font-size' => '3'
                ),
            ),
            array(
                'selector' => 'table.foo p',
                'styles' => array(
                    'font-size' => '2'
                ),
            )
        );
        $html  = '<table><tr><td><p>Blu bla Bli</p></td></tr></table>';
        $html .= '<table class="foo"><tr><td><p>Blu bla Bli</p></td></tr></table>';
        $expected  = '<table><tr><td><p><font size="3">Blu bla Bli</font></p></td></tr></table>';
        $expected .= '<table class="foo"><tr><td><p><font size="2">Blu bla Bli</font></p></td></tr></table>';
        $p = new Kwc_Mail_HtmlParser($styles);
        $html = $p->parse($html);
        $this->_assertHtmlEquals($expected, $html);

    }
}
