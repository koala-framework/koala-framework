<?php
class Kwf_Date_Test extends Kwf_Test_TestCase
{
    public function testIt()
    {
        $d = new Kwf_Date('1983-06-09');
        $this->assertEquals('Donnerstag', $d->format('l', 'de'));
    }
}
