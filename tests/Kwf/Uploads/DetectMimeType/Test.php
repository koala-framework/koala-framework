<?php
class Kwf_Uploads_DetectMimeType_Test extends Kwf_Test_TestCase
{
    public function testIt()
    {
        $mime = Kwf_Uploads_Row::detectMimeType(false, file_get_contents(KWF_PATH.'/images/information.png'));
        $this->assertEquals(substr($mime, 0, 9), 'image/png');

        $mime = Kwf_Uploads_Row::detectMimeType(false, file_get_contents(KWF_PATH.'/tests/Kwf/Uploads/DetectMimeType/sample.docx'));
        $this->assertEquals($mime, 'application/msword');

        $mime = Kwf_Uploads_Row::detectMimeType(false, file_get_contents(KWF_PATH.'/tests/Kwf/Uploads/DetectMimeType/sample.odt'));
        $this->assertEquals($mime, 'application/vnd.oasis.opendocument.text');
    }
}
