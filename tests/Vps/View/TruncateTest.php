<?php
class Vps_View_TruncateTest extends PHPUnit_Framework_TestCase
{
    public function testUtf8()
    {
        $testStr = 'das ist übertrieben';

        $h = new Vps_View_Helper_Truncate();
        $res = $h->truncate($testStr, 12, '...', true);
        $this->assertEquals('das ist ü...', $res);
    }
}
