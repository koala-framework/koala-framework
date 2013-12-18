<?php
class Kwf_Assets_Extensible_ProviderTest extends Kwf_Test_TestCase
{
    public function testObservable()
    {
        $l = new Kwf_Assets_Extensible_TestProviderList();
        $d = $l->findDependency('Extensible.calendar.menu.Event');
        $array = $d->getRecursiveFiles();
        $this->assertEquals(202, count($array));
    }
}
