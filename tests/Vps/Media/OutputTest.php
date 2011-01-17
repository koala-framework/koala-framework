<?php
class Vps_Media_OutputTest extends Vps_Test_TestCase
{
    public function testOutput()
    {
        $file = array(
            'contents' => 'output',
            'mimeType' => 'text/plain',
            'mtime' => time(),
            'etag' => 'asdf'
        );
        $output = Vps_Media_Output::getOutputData($file, array());
        $this->_assert200($output, $file);
        $this->_assertHeader($output, 'Content-Encoding', 'none');

        $headers = array(
            'If-Modified-Since' => $this->_date($file['mtime'])
        );
        $output = Vps_Media_Output::getOutputData($file, $headers);
        $this->_assertRepsonseCode($output, 304);
        $this->_assertHeader($output, 'Last-Modified', $this->_date($file['mtime']));
        $this->_assertNoHeader($output, 'ETag');
        $this->_assertNoHeader($output, 'Content-Encoding');
        $this->_assertNoHeader($output, 'Content-Type');
        $this->_assertNoHeader($output, 'Content-Length');

        $headers = array(
            'If-Modified-Since' => $this->_date($file['mtime']-1)
        );
        $output = Vps_Media_Output::getOutputData($file, $headers);
        $this->_assert200($output, $file);

        $headers = array(
            'If-None-Match' => $file['etag']
        );
        $output = Vps_Media_Output::getOutputData($file, $headers);
        $this->_assertRepsonseCode($output, 304);
        $this->_assertNoHeader($output, 'Last-Modified');
        $this->_assertHeader($output, 'ETag', $file['etag']);
        $this->_assertNoHeader($output, 'Content-Encoding');
        $this->_assertNoHeader($output, 'Content-Type');
        $this->_assertNoHeader($output, 'Content-Length');

        $headers = array(
            'If-None-Match' => 'blub'
        );
        $output = Vps_Media_Output::getOutputData($file, $headers);
        $this->_assert200($output, $file);
    }

    private function _assert200($output, $file)
    {
        $this->_assertRepsonseCode($output, 200);
        $this->assertEquals($output['contents'], $file['contents']);
        $this->_assertHeader($output, 'Content-Type', 'text/plain');
        $this->_assertHeader($output, 'Content-Length', strlen($file['contents']));
        $this->assertTrue(strtotime($this->_headerValue($output, 'Expires'))-time() > 24*60*60-10);
        $this->_assertHeader($output, 'Cache-Control', 'public, max-age=86400');
        $this->_assertHeader($output, 'Last-Modified', $this->_date($file['mtime']));
        $this->_assertHeader($output, 'ETag', $file['etag']);
    }

    private function _date($timestamp)
    {
        return gmdate("D, d M Y H:i:s \G\M\T", $timestamp);
    }

    private function _assertRepsonseCode($output, $code)
    {
        $ret = 200;
        foreach ($output['headers'] as $h) {
            if (is_array($h) && count($h) > 1) $ret = $h[2];
        }
        $this->assertEquals($code, $ret);
    }
    private function _headerValue($output, $headerName)
    {
        foreach ($output['headers'] as $h) {
            if (is_array($h)) $h = $h[0];
            if (substr($h, 0, strpos($h, ':')) == $headerName) {
                return substr($h, strpos($h, ':')+2);
            }
        }
        return null;
    }

    private function _assertHeader($output, $headerName, $headerValue)
    {
        $this->assertEquals($this->_headerValue($output, $headerName), $headerValue);
    }
    private function _assertNoHeader($output, $headerName)
    {
        $this->assertEquals($this->_headerValue($output, $headerName), null);
    }


    public function testEncoding()
    {
        $file = array(
            'contents' => 'output',
            'mimeType' => 'text/plain',
            'mtime' => time(),
            'etag' => 'asdf',
            'encoding' => 'gzip'
        );
        $output = Vps_Media_Output::getOutputData($file, array());
        $this->_assert200($output, $file);
        $this->_assertHeader($output, 'Content-Encoding', 'gzip');

        unset($file['encoding']);
        $_SERVER['HTTP_ACCEPT_ENCODING'] = 'gzip';
        $output = Vps_Media_Output::getOutputData($file, array());
        unset($_SERVER['HTTP_ACCEPT_ENCODING']);
        $this->_assertHeader($output, 'Content-Encoding', 'gzip');
        $this->assertEquals($file['contents'], gzinflate(substr($output['contents'], 10)));
    }

    public function testNothing()
    {
        $this->setExpectedException('Vps_Exception_NotFound');
        Vps_Media_Output::getOutputData(array(), array());
    }
}
