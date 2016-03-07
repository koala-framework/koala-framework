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
        $this->_assertHtmlEquals('<html xmlns="http://www.w3.org/1999/xhtml"><head><title></title></head><p style="font-size: 18px; "><font face="Verdana">Lorem Ipsum</font></p></html>', $html);

        $html = '<p class="red">Lorem Ipsum</p>';
        $html = $p->parse($html);
        $this->_assertHtmlEquals('<html xmlns="http://www.w3.org/1999/xhtml"><head><title></title></head><p class="red" style="font-size: 18px; "><font face="Verdana" color="red">Lorem Ipsum</font></p></html>', $html);

        $html = '<h1>Lorem Ipsum</h1>';
        $html = $p->parse($html);
        $this->_assertHtmlEquals('<html xmlns="http://www.w3.org/1999/xhtml"><head><title></title></head><h1 style="font-size: 24px; "><font face="Verdana"><b>Lorem Ipsum</b></font></h1></html>', $html);

        $html = '<div>Lorem</div><div>Ipsum</div>';
        $html = $p->parse($html);
        $this->_assertHtmlEquals('<html xmlns="http://www.w3.org/1999/xhtml"><head><title></title></head><div>Lorem</div><div>Ipsum</div></html>', $html);

        $html = '<strong foo="bar">Lorem Ipsum</strong>';
        $html = $p->parse($html);
        $this->_assertHtmlEquals('<html xmlns="http://www.w3.org/1999/xhtml"><head><title></title></head><p style="font-size: 18px; "><font face="Verdana"><b>Lorem Ipsum</b></font></p></html>', $html);

        $html = 'Lorem<br />Ipsum';
        $html = $p->parse($html);
        $this->_assertHtmlEquals('<html xmlns="http://www.w3.org/1999/xhtml"><head><title></title></head><p style="font-size: 18px; "><font face="Verdana">Lorem<br />Ipsum</font></p></html>', $html);

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
        $expected= '<html xmlns="http://www.w3.org/1999/xhtml"><head><title></title></head><table><tr><td style="font-size: 12px; "><font face="Verdana">Guten Tag, &NBSP; <strong>Frau Staterau!</strong></font></td><td style="font-size: 12px; "><font face="Verdana">&nbsp;</font></td><td style="font-size: 12px; "><font face="Verdana">Testtext</font></td></tr></table></html>';
        $p = new Kwc_Mail_HtmlParser($styles);
        $html = $p->parse($html);
        $this->_assertHtmlEquals($expected, $html);

    }

    public function testMoreAddAttributesToExistingTag()
    {
        $styles = array(
            array(
                'tag' => 'td',
                'styles' => array(
                    'font-family' => 'Verdana',
                ),
                'appendTags' => array(
                    'font' => array(
                        'color' => 'red'
                    )
                )
            )
        );
        $html = '<table><tr><td>Testtext</td></tr></table>';
        $expected= '<html xmlns="http://www.w3.org/1999/xhtml"><head><title></title></head><table><tr><td><font color="red" face="Verdana">Testtext</font></td></tr></table></html>';
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
        $expected = '<html xmlns="http://www.w3.org/1999/xhtml">';
        $expected .= '<head><title></title></head><table><tr><td><p style="font-size: 18px; ">Blu bla Bli</p></td></tr></table>';
        $expected .= '<table class="foo"><tr><td><p style="font-size: 12px; ">Blu bla Bli</p></td></tr></table>';
        $expected .= '</html>';
        $p = new Kwc_Mail_HtmlParser($styles);
        $html = $p->parse($html);
        $this->_assertHtmlEquals($expected, $html);

    }

    public function testAppendTagsKeyAsTag()
    {
        $styles = array(
            array(
                'tag' => 'li',
                'appendTags' => array(
                    'img' => array(
                        'src' => 'image1.png',
                    ),
                )
            )
        );
        $html  = '<table><tr><td><ul><li>Here</li></ul></td></tr></table>';
        $expected = '<html xmlns="http://www.w3.org/1999/xhtml">';
        $expected .= '<head><title></title></head><table><tr><td><ul><li><img src="image1.png"/>Here</li></ul></td></tr></table>';
        $expected .= '</html>';

        $p = new Kwc_Mail_HtmlParser($styles);
        $html = $p->parse($html);
        $this->_assertHtmlEquals($expected, $html);
    }

    public function testSelectorMultipleClasses1()
    {
        $styles = array(
            array(
                'selector' => 'p.foo',
                'styles' => array(
                    'font-size' => '12px'
                ),
            ),
            array(
                'selector' => '.bar',
                'styles' => array(
                    'line-height' => '100%'
                ),
            )
        );

        $html = '<p class="foo bar">Lorem Ipsum</p>';
        $html .= '<p class="foo">Lorem Ipsum</p>';
        $html .= '<p class="bar">Lorem Ipsum</p>';

        $expected = '<html xmlns="http://www.w3.org/1999/xhtml">';
        $expected .= '<head><title></title></head>';
        $expected .= '<p class="foo bar" style="font-size: 12px; line-height: 100%; ">Lorem Ipsum</p>';
        $expected .= '<p class="foo" style="font-size: 12px; ">Lorem Ipsum</p>';
        $expected .= '<p class="bar" style="line-height: 100%; ">Lorem Ipsum</p>';
        $expected .= '</html>';

        $p = new Kwc_Mail_HtmlParser($styles);
        $html = $p->parse($html);
        $this->_assertHtmlEquals($expected, $html);
    }

    public function testSelectorMultipleClasses2()
    {
        $styles = array(
            array(
                'selector' => 'p.foo',
                'styles' => array(
                    'font-size' => '12px'
                ),
            ),
            array(
                'selector' => 'div.bar',
                'styles' => array(
                    'line-height' => '100%'
                ),
            )
        );

        $html = '<p class="foo bar">Lorem Ipsum</p>';
        $html .= '<p class="foo">Lorem Ipsum</p>';
        $html .= '<p class="bar">Lorem Ipsum</p>';

        $expected = '<html xmlns="http://www.w3.org/1999/xhtml">';
        $expected .= '<head><title></title></head>';
        $expected .= '<p class="foo bar" style="font-size: 12px; ">Lorem Ipsum</p>';
        $expected .= '<p class="foo" style="font-size: 12px; ">Lorem Ipsum</p>';
        $expected .= '<p class="bar">Lorem Ipsum</p>';
        $expected .= '</html>';

        $p = new Kwc_Mail_HtmlParser($styles);
        $html = $p->parse($html);
        $this->_assertHtmlEquals($expected, $html);
    }

    public function testSelectorMultipleClasses3()
    {
        $styles = array(
            array(
                'selector' => 'p',
                'styles' => array(
                    'font-size' => '12px'
                ),
            ),
            array(
                'selector' => '.bar',
                'styles' => array(
                    'line-height' => '100%'
                ),
            )
        );

        $html = '<p class="foo bar">Lorem Ipsum</p>';
        $html .= '<p class="foo">Lorem Ipsum</p>';
        $html .= '<p class="bar">Lorem Ipsum</p>';

        $expected = '<html xmlns="http://www.w3.org/1999/xhtml">';
        $expected .= '<head><title></title></head>';
        $expected .= '<p class="foo bar" style="font-size: 12px; line-height: 100%; ">Lorem Ipsum</p>';
        $expected .= '<p class="foo" style="font-size: 12px; ">Lorem Ipsum</p>';
        $expected .= '<p class="bar" style="font-size: 12px; line-height: 100%; ">Lorem Ipsum</p>';
        $expected .= '</html>';

        $p = new Kwc_Mail_HtmlParser($styles);
        $html = $p->parse($html);
        $this->_assertHtmlEquals($expected, $html);
    }

    public function testAppendTagsTagInArray()
    {
        $styles = array(
            array(
                'tag' => 'li',
                'appendTags' => array(
                    array(
                        'tag' => 'img',
                        'src' => 'image1.png',
                    )
                )
            )
        );
        $html  = '<table><tr><td><ul><li>Here</li></ul></td></tr></table>';
        $expected = '<html xmlns="http://www.w3.org/1999/xhtml">';
        $expected .= '<head><title></title></head><table><tr><td><ul><li><img src="image1.png"/>Here</li></ul></td></tr></table>';
        $expected .= '</html>';

        $p = new Kwc_Mail_HtmlParser($styles);
        $html = $p->parse($html);
        $this->_assertHtmlEquals($expected, $html);
    }

    public function testSelectorMultipleClasses4()
    {
        $styles = array(
            array(
                'selector' => 'div',
                'styles' => array(
                    'font-size' => '12px'
                ),
            ),
            array(
                'selector' => '.bar',
                'styles' => array(
                    'line-height' => '100%'
                ),
            )
        );

        $html = '<p class="foo bar">Lorem Ipsum</p>';
        $html .= '<p class="foo">Lorem Ipsum</p>';
        $html .= '<p class="bar">Lorem Ipsum</p>';

        $expected = '<html xmlns="http://www.w3.org/1999/xhtml">';
        $expected .= '<head><title></title></head>';
        $expected .= '<p class="foo bar" style="line-height: 100%; ">Lorem Ipsum</p>';
        $expected .= '<p class="foo">Lorem Ipsum</p>';
        $expected .= '<p class="bar" style="line-height: 100%; ">Lorem Ipsum</p>';
        $expected .= '</html>';

        $p = new Kwc_Mail_HtmlParser($styles);
        $html = $p->parse($html);
        $this->_assertHtmlEquals($expected, $html);
    }

    public function testAppendTagsMoreTagsImg()
    {
        $styles = array(
            array(
                'tag' => 'li',
                'appendTags' => array(
                    array(
                        'tag' => 'img',
                        'src' => 'image1.png',
                    ),
                    array(
                        'tag' => 'img',
                        'src' => 'image2.png'
                    )
                )
            )
        );

        $html  = '<table><tr><td><ul><li>Here</li></ul></td></tr></table>';
        $expected = '<html xmlns="http://www.w3.org/1999/xhtml">';
        $expected .= '<head><title></title></head><table><tr><td><ul><li><img src="image1.png"/><img src="image2.png"/>Here</li></ul></td></tr></table>';
        $expected .= '</html>';

        $p = new Kwc_Mail_HtmlParser($styles);
        $html = $p->parse($html);
        $this->_assertHtmlEquals($expected, $html);
    }


    public function testSelectorMultipleClasses5()
    {
        $styles = array(
            array(
                'selector' => '.foo.bar',
                'styles' => array(
                    'font-size' => '12px'
                ),
            )
        );

        $html = '<p class="foo bar">Lorem Ipsum</p>';
        $html .= '<p class="foo">Lorem Ipsum</p>';
        $html .= '<p class="bar">Lorem Ipsum</p>';

        $expected = '<html xmlns="http://www.w3.org/1999/xhtml">';
        $expected .= '<head><title></title></head>';
        $expected .= '<p class="foo bar" style="font-size: 12px; ">Lorem Ipsum</p>';
        $expected .= '<p class="foo">Lorem Ipsum</p>';
        $expected .= '<p class="bar">Lorem Ipsum</p>';
        $expected .= '</html>';

        $p = new Kwc_Mail_HtmlParser($styles);
        $html = $p->parse($html);
        $this->_assertHtmlEquals($expected, $html);
    }

    public function testAppendTagsMoreTagsSpan()
    {
        $styles = array(
            array(
                'tag' => 'li',
                'appendTags' => array(
                    array(
                        'tag' => 'span',
                        'style' => 'margin-left:10px',
                    ),
                    array(
                        'tag' => 'span',
                        'style' => 'margin-right:10px',
                    )
                )
            )
        );

        $html  = '<table><tr><td><ul><li>Here</li></ul></td></tr></table>';
        $expected = '<html xmlns="http://www.w3.org/1999/xhtml">';
        $expected .= '<head><title></title></head><table><tr><td><ul><li><span style="margin-left:10px"><span style="margin-right:10px">Here</span></span></li></ul></td></tr></table>';
        $expected .= '</html>';

        $p = new Kwc_Mail_HtmlParser($styles);
        $html = $p->parse($html);
        $this->_assertHtmlEquals($expected, $html);
    }

    public function testSelectorMultipleClasses6()
    {
        $styles = array(
            array(
                'selector' => 'p.foo.bar',
                'styles' => array(
                    'font-size' => '12px'
                ),
            )
        );

        $html = '<p class="foo bar">Lorem Ipsum</p>';
        $html .= '<p class="foo">Lorem Ipsum</p>';
        $html .= '<p class="bar">Lorem Ipsum</p>';

        $expected = '<html xmlns="http://www.w3.org/1999/xhtml">';
        $expected .= '<head><title></title></head>';
        $expected .= '<p class="foo bar" style="font-size: 12px; ">Lorem Ipsum</p>';
        $expected .= '<p class="foo">Lorem Ipsum</p>';
        $expected .= '<p class="bar">Lorem Ipsum</p>';
        $expected .= '</html>';

        $p = new Kwc_Mail_HtmlParser($styles);
        $html = $p->parse($html);
        $this->_assertHtmlEquals($expected, $html);
    }

    public function testSelectorMultipleClasses7()
    {
        $styles = array(
            array(
                'selector' => 'div.foo.bar',
                'styles' => array(
                    'font-size' => '12px'
                ),
            )
        );

        $html = '<p class="foo bar">Lorem Ipsum</p>';
        $html .= '<div class="foo bar">Lorem Ipsum</div>';
        $html .= '<p class="foo">Lorem Ipsum</p>';
        $html .= '<p class="bar">Lorem Ipsum</p>';

        $expected = '<html xmlns="http://www.w3.org/1999/xhtml">';
        $expected .= '<head><title></title></head>';
        $expected .= '<p class="foo bar">Lorem Ipsum</p>';
        $expected .= '<div class="foo bar" style="font-size: 12px; ">Lorem Ipsum</div>';
        $expected .= '<p class="foo">Lorem Ipsum</p>';
        $expected .= '<p class="bar">Lorem Ipsum</p>';
        $expected .= '</html>';

        $p = new Kwc_Mail_HtmlParser($styles);
        $html = $p->parse($html);
        $this->_assertHtmlEquals($expected, $html);
    }

    public function testSelectorMultipleClasses8()
    {
        $styles = array(
            array(
                'selector' => '.foo.bar.koala',
                'styles' => array(
                    'font-size' => '12px'
                ),
            ),
            array(
                'selector' => 'div.foo.bar.koala',
                'styles' => array(
                    'line-height' => '100%'
                ),
            )
        );

        $html = '<p class="foo koala bar">Lorem Ipsum</p>';
        $html .= '<div class="foo bar koala">Lorem Ipsum</div>';
        $html .= '<p class="foo bar">Lorem Ipsum</p>';

        $expected = '<html xmlns="http://www.w3.org/1999/xhtml">';
        $expected .= '<head><title></title></head>';
        $expected .= '<p class="foo koala bar" style="font-size: 12px; ">Lorem Ipsum</p>';
        $expected .= '<div class="foo bar koala" style="font-size: 12px; line-height: 100%; ">Lorem Ipsum</div>';
        $expected .= '<p class="foo bar">Lorem Ipsum</p>';
        $expected .= '</html>';

        $p = new Kwc_Mail_HtmlParser($styles);
        $html = $p->parse($html);
        $this->_assertHtmlEquals($expected, $html);
    }

    public function testSelectorMultipleClasses9()
    {
        $styles = array(
            array(
                'selector' => '.foo .bar',
                'styles' => array(
                    'font-size' => '12px'
                ),
            )
        );

        $html = '<p class="foo bar">Lorem Ipsum</p>';
        $html .= '<p class="foo"><span class="bar">Lorem Ipsum</span></p>';
        $html .= '<p class="bar">Lorem Ipsum</p>';

        $expected = '<html xmlns="http://www.w3.org/1999/xhtml">';
        $expected .= '<head><title></title></head>';
        $expected .= '<p class="foo bar">Lorem Ipsum</p>';
        $expected .= '<p class="foo"><span class="bar" style="font-size: 12px; ">Lorem Ipsum</span></p>';
        $expected .= '<p class="bar">Lorem Ipsum</p>';
        $expected .= '</html>';

        $p = new Kwc_Mail_HtmlParser($styles);
        $html = $p->parse($html);
        $this->_assertHtmlEquals($expected, $html);
    }
}
