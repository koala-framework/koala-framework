<?php
class Kwf_Assets_Dependency_Test extends Kwf_Test_TestCase
{
    public function testFileJs()
    {
        $f = new Kwf_Assets_Dependency_File_Js('kwf/Kwf_js/Kwf.js');
        $this->assertEquals('text/javascript', $f->getMimeType());
        $this->assertContains('Kwf.clone', $f->getContents('en'));
        $this->assertEquals(array(), $f->getDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_ALL));
    }
    public function testFileJsPacked()
    {
        $f = new Kwf_Assets_Dependency_File_Js('kwf/Kwf_js/Kwf.js');
        $this->assertEquals('text/javascript', $f->getMimeType());
        $c = $f->getContents('en');
        $cPacked = $f->getContentsPacked('en')->getFileContents();
        $this->assertContains('Kwf.clone', $cPacked);
        $this->assertTrue(strlen($c) > strlen($cPacked));
    }

    public function testFileCreateDependency()
    {
        $f = Kwf_Assets_Dependency_File::createDependency('kwf/Kwf_js/Kwf.js', new Kwf_Assets_Dependency_EmptyProviderList());
        $this->assertTrue($f instanceof Kwf_Assets_Dependency_File_Js);
        $this->assertEquals('text/javascript', $f->getMimeType());
        $this->assertContains('Kwf.clone', $f->getContents('en'));
        $this->assertEquals(array(), $f->getDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_ALL));
    }

    public function testDependency()
    {
        $files = array(new Kwf_Assets_Dependency_File_Js('kwf/Kwf_js/Kwf.js'));
        $dep = array();
        $d = new Kwf_Assets_Dependency_Dependencies($files);
        $this->assertEquals($files, $d->getDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_ALL));
        $this->assertEquals(null, $d->getContents('en'));
    }

    public function testRecursiveIterator()
    {
        $file1 = new Kwf_Assets_Dependency_File_Js('kwf/Kwf_js/Kwf.js');
        $file2 = new Kwf_Assets_Dependency_File_Js('kwf/Kwf_js/OnReadyExt.js');

        $d = new Kwf_Assets_Dependency_Dependencies(array(
            $file1,
            new Kwf_Assets_Dependency_Dependencies(array(
                $file2
            ))
        ));

        $it = new RecursiveIteratorIterator(new Kwf_Assets_Dependency_Iterator_Recursive($d, Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_ALL));

        $array = iterator_to_array($it, false);
        $this->assertEquals(array($file1, $file2), $array);

        $array = array();
        foreach ($it as $i) {
            $array[] = $i;
        }
        $this->assertEquals(array($file1, $file2), $array);
    }

    public function testRecursiveIteratorFilter()
    {
        $file1 = new Kwf_Assets_Dependency_File_Js('kwf/Kwf_js/Kwf.js');
        $file2 = new Kwf_Assets_Dependency_File_Css('kwf/css/web.css');

        $d = new Kwf_Assets_Dependency_Dependencies(array(
            $file1,
            new Kwf_Assets_Dependency_Dependencies(array(
                $file2
            ))
        ));

        $it = new RecursiveIteratorIterator(new Kwf_Assets_Dependency_Iterator_Recursive($d, Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_ALL));
        $filterIt = new Kwf_Assets_Dependency_Iterator_MimeTypeFilter($it, 'text/css');

        $array = iterator_to_array($filterIt, false);
        $this->assertEquals(array($file2), $array);

        $filterIt = new Kwf_Assets_Dependency_Iterator_MimeTypeFilter($it, 'text/javascript');

        $array = iterator_to_array($filterIt, false);
        $this->assertEquals(array($file1), $array);

        $filterIt = new Kwf_Assets_Dependency_Iterator_MimeTypeFilter($it, 'text/invalid');

        $array = iterator_to_array($filterIt, false);
        $this->assertEquals(array(), $array);
    }
}
