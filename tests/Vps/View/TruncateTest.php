<?php
/**
 * @group Helper
 * @group Helper_Truncate
 */
class Vps_View_TruncateTest extends Vps_Test_TestCase
{
    public function testUtf8()
    {
        $testStr = 'das ist übertrieben';

        $h = new Vps_View_Helper_Truncate();
        $res = $h->truncate($testStr, 12, '...', true);
        $this->assertEquals('das ist ü...', $res);
    }

    public function testInputArrayBasic()
    {
        $testStr = array(
            'der erste string',
            ' ein zweiter str'
        );
        $h = new Vps_View_Helper_Truncate();

        $this->assertEquals('der erste st...', $h->truncate($testStr, 15, '...', true));
        $this->assertEquals('der erste str...', $h->truncate($testStr, 16, '...', true));
        $this->assertEquals('der erste string...', $h->truncate($testStr, 19, '...', true));

        $this->assertEquals('der erste string ein zw...', $h->truncate($testStr, 26, '...', true));
        $this->assertEquals('der erste string ein zweiter str', $h->truncate($testStr, 100, '...', true));
    }

    public function testInputArrayTag()
    {
        $testStr = array(
            array('string' => 'der erste string', 'tag' => 'span'),
            array('string' => ' - ein zweiter str', 'tag' => 'div')
        );
        $h = new Vps_View_Helper_Truncate();

        $this->assertEquals('<span>der erste st...</span>', $h->truncate($testStr, 15, '...', true));
        $this->assertEquals('<span>der erste string</span><div>...</div>', $h->truncate($testStr, 19, '...', true));

        $this->assertEquals('<span>der erste string</span><div> - ein zw...</div>', $h->truncate($testStr, 28, '...', true));
        $this->assertEquals('<span>der erste string</span><div> - ein zweiter str</div>', $h->truncate($testStr, 100, '...', true));
    }

    public function testInputArrayTagCss()
    {
        $testStr = array(
            array('string' => 'der erste string', 'tag' => 'span', 'cssClass' => 'foo'),
            array('string' => ' - ein zweiter str', 'tag' => 'div', 'cssClass' => 'bar')
        );
        $h = new Vps_View_Helper_Truncate();

        $this->assertEquals('<span class="foo">der erste st...</span>', $h->truncate($testStr, 15, '...', true));
        $this->assertEquals('<span class="foo">der erste string</span><div class="bar">...</div>', $h->truncate($testStr, 19, '...', true));

        $this->assertEquals('<span class="foo">der erste string</span><div class="bar"> - ein zw...</div>', $h->truncate($testStr, 28, '...', true));
        $this->assertEquals('<span class="foo">der erste string</span><div class="bar"> - ein zweiter str</div>', $h->truncate($testStr, 100, '...', true));
    }
}
