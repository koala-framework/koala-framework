<?php
/**
 * @group Headline
 */
class Vps_Media_HeadlineTest extends PHPUnit_Framework_TestCase
{
    public function testGetHeadlineStyles()
    {
        $content = '.foo { -vps-headline: graphic; font-size: 10px; }';
        $styles = Vps_Media_Headline::getHeadlineStyles($content);
        $this->assertEquals(count($styles), 1);
        $this->assertTrue(isset($styles['.foo']));
        $this->assertEquals(count($styles['.foo']), 1);
        $this->assertEquals($styles['.foo']['font-size'], '10px');

        $content = ".asdf {}\n   .foo {\ncolor: red;\n\n-vps-headline\t:graphic;
                        font-size: 10px; background-color: blue; }\n h1 { blub: bar; }";
        $styles = Vps_Media_Headline::getHeadlineStyles($content);
        $this->assertEquals(count($styles), 1);
        $this->assertTrue(isset($styles['.foo']));
        $this->assertEquals(count($styles['.foo']), 3);
        $this->assertEquals($styles['.foo']['font-size'], '10px');
        $this->assertEquals($styles['.foo']['color'], 'red');
        $this->assertEquals($styles['.foo']['background-color'], 'blue');

        $content = "asdf { xxx } .foo h1 { -vps-headline:graphic; color: red; -vps-color: blue; } xxx";
        $styles = Vps_Media_Headline::getHeadlineStyles($content);
        $this->assertTrue(isset($styles['.foo h1']));
        $this->assertEquals($styles['.foo h1']['color'], 'blue');
    }
}
