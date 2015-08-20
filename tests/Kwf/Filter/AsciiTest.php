<?php
class Kwf_Filter_AsciiTest extends Kwf_Test_TestCase
{
    public function testAscii()
    {
        $filter = new Kwf_Filter_Ascii();
        $this->assertEquals('abcdefghijklmno', $filter->filter('°a!b"c§d$e%f&g/h(i)j=k?l`m\n^o'));
        $this->assertEquals('oe', $filter->filter('#+~:;,.<>|ö'));
        $this->assertEquals('ueber_uns', $filter->filter('Über uns'));
        $this->assertEquals('kontakt', $filter->filter('контакт'));
        $this->assertEquals('ab', $filter->filter('a:b'));
        $this->assertEquals('a_b', $filter->filter('a  b'));
    }
}