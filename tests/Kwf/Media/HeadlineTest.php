<?php
/**
 * @group Headline
 */
class Kwf_Media_HeadlineTest extends Kwf_Test_TestCase
{
    public function testGetHeadlineStyles()
    {
        $content = '.foo { -kwf-headline: graphic; font-size: 10px; }';
        $styles = Kwf_Media_Headline::getHeadlineStyles($content);
        $this->assertEquals(count($styles), 1);
        $this->assertTrue(isset($styles['.foo']));
        $this->assertEquals(count($styles['.foo']), 1);
        $this->assertEquals($styles['.foo']['font-size'], '10px');

        $content = ".asdf {}\n   .foo {\ncolor: red;\n\n-kwf-headline\t:graphic;
                        font-size: 10px; background-color: blue; }\n h1 { blub: bar; }";
        $styles = Kwf_Media_Headline::getHeadlineStyles($content);
        $this->assertEquals(count($styles), 1);
        $this->assertTrue(isset($styles['.foo']));
        $this->assertEquals(count($styles['.foo']), 3);
        $this->assertEquals($styles['.foo']['font-size'], '10px');
        $this->assertEquals($styles['.foo']['color'], 'red');
        $this->assertEquals($styles['.foo']['background-color'], 'blue');

        $content = "asdf { xxx } .foo h1 { -kwf-headline:graphic; color: red; -kwf-color: blue; } xxx";
        $styles = Kwf_Media_Headline::getHeadlineStyles($content);
        $this->assertTrue(isset($styles['.foo h1']));
        $this->assertEquals($styles['.foo h1']['color'], 'blue');
    }
}
