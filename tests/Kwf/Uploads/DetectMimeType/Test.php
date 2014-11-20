<?php
class Kwf_Uploads_DetectMimeType_Test extends Kwf_Test_TestCase
{
    public function testPng()
    {
        $mime = Kwf_Uploads_Row::detectMimeType(false, file_get_contents(KWF_PATH.'/images/information.png'));
        $this->assertEquals(substr($mime, 0, 9), 'image/png');
    }

    public function testDocx()
    {
        $mime = Kwf_Uploads_Row::detectMimeType(false, file_get_contents(KWF_PATH.'/tests/Kwf/Uploads/DetectMimeType/sample.docx'));
        //according to http://blogs.msdn.com/b/vsofficedeveloper/archive/2008/05/08/office-2007-open-xml-mime-types.aspx
        //application/vnd.openxmlformats-officedocument.wordprocessingml.document is correct
        //but on some servers with older file magic we still get application/msword
        //both should fine in pratice
        $this->assertTrue($mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || $mime == 'application/msword');
    }

    public function testOdt()
    {
        $mime = Kwf_Uploads_Row::detectMimeType(false, file_get_contents(KWF_PATH.'/tests/Kwf/Uploads/DetectMimeType/sample.odt'));
        $this->assertEquals($mime, 'application/vnd.oasis.opendocument.text');
    }
}
