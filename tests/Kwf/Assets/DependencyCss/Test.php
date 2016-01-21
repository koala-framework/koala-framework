<?php
class Kwf_Assets_DependencyCss_Test extends Kwf_Test_TestCase
{
    public function testRelativeUrl1()
    {
        $f = new Kwf_Assets_Dependency_File_Css('kwf/tests/Kwf/Assets/DependencyCss/relativeUrl1.css');
        $c = $f->getContentsPacked()->getFileContents();
        $this->assertEquals("body { background-image: url('/assets/kwf/tests/Kwf/Assets/DependencyCss/foo.png'); }
body { background-image: url('/assets/kwf/tests/Kwf/Assets/DependencyCss/foo.png'); }
body { background-image: url('/assets/web/images/foo.png'); }
body { background-image: url('/assets/web/images/foo.png'); }", trim($c));
    }

    public function testRelativeUrl2()
    {
        $f = new Kwf_Assets_Dependency_File_Css('kwf/tests/Kwf/Assets/DependencyCss/relativeUrl2.css');
        $c = $f->getContentsPacked()->getFileContents();
        $this->assertEquals('body { background-image: url("/assets/kwf/tests/Kwf/Assets/DependencyCss/foo.png"); }
body { background-image: url("/assets/kwf/tests/Kwf/Assets/DependencyCss/foo.png"); }
body { background-image: url("/assets/web/images/foo.png"); }
body { background-image: url("/assets/web/images/foo.png"); }', trim($c));
    }

    public function testRelativeUrl3()
    {
        $f = new Kwf_Assets_Dependency_File_Css('kwf/tests/Kwf/Assets/DependencyCss/relativeUrl3.css');
        $c = $f->getContentsPacked()->getFileContents();
        $this->assertEquals('body { background-image: url(/assets/kwf/tests/Kwf/Assets/DependencyCss/foo.png); }
body { background-image: url(/assets/kwf/tests/Kwf/Assets/DependencyCss/foo.png); }
body { background-image: url(/assets/web/images/foo.png); }
body { background-image: url(/assets/web/images/foo.png); }', trim($c));    }

    public function testHttpUrl()
    {
        $f = new Kwf_Assets_Dependency_File_Css('kwf/tests/Kwf/Assets/DependencyCss/httpUrl.css');
        $c = $f->getContentsPacked()->getFileContents();
        $this->assertEquals('body { background-image: url(http://vivid.com/foo.png); }
body { background-image: url(http://vivid.com/foo.png); }
body { background-image: url("http://vivid.com/foo.png"); }
body { background-image: url("http://vivid.com/foo.png"); }
body { background-image: url(\'http://vivid.com/foo.png\'); }
body { background-image: url(\'http://vivid.com/foo.png\'); }', trim($c));
    }

    public function testDataUrl()
    {
        $f = new Kwf_Assets_Dependency_File_Css('kwf/tests/Kwf/Assets/DependencyCss/dataUrl.css');
        $c = $f->getContentsPacked()->getFileContents();
        $this->assertEquals('body { background-image: url(data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7); }
body { background-image: url(data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7); }
body { background-image: url(\'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7\'); }
body { background-image: url(\'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7\'); }
body { background-image: url("data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"); }
body { background-image: url("data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"); }', trim($c));
    }

//     public function testCompass()
//     {
//         $f = new Kwf_Assets_Dependency_File_Scss('kwf/tests/Kwf/Assets/DependencyScss/file3.scss');
//         $c = $f->getContentsPacked()->getFileContents();
//         $this->assertContains("body{-webkit-border-radius:3px;-moz-border-radius:3px;-ms-border-radius:3px;-o-border-radius:3px;border-radius:3px}", trim($c));
//     }
}
