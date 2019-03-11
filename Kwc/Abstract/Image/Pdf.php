<?php
class Kwc_Abstract_Image_Pdf extends Kwc_Abstract_Pdf
{
    private $_size;

    public function writeContent($setCoordinates = true)
    {
        $data = $this->_component->getImageData();

        if ($data && $data['file'] && is_file($data['file']->getFileSource())) {
            $source = $data['file']->getFileSource();
            $size = $this->getSize();

            $imageSize = array(
                'cover' => false,
                'width' => $this->_calculateMm($size['width']),
                'height' => $this->_calculateMm($size['height'])
            );
            $uploadId = isset($data['uploadId']) ? $data['uploadId'] : null;
            $content = Kwf_Media_Image::scale($source, $imageSize, $uploadId);
            $filter = new Kwf_Filter_Ascii();
            $tempFilename = tempnam('application/temp', 'pdfimage');
            file_put_contents($tempFilename, $content);
            $data = getimagesize($tempFilename);
            if ($data[2] == 2) { // nur jpgs ausgeben
                if ($setCoordinates && $this->addPageIfNecessary()) {
                    $x = $this->getX();
                    $this->SetY($this->getTopMargin());
                    $this->SetX($x);
                }
                $type = str_replace('image/', '', 'image/jpeg');
                $this->_pdf->Image(
                    $tempFilename, $this->getX(), $this->getY(),
                    $this->_calculatePx($data[0]), $this->_calculatePx($data[1]), $type
                );
                if ($setCoordinates) {
                    $this->SetY($this->getY() + $size['height'] + 2);
                }
            }
            unlink($tempFilename);
            return true;
        }
        return false;
    }

    public function addPageIfNecessary()
    {
        $size = $this->getSize();
        $pageExclBorders = $this->getPageHeight() - $this->getBottomMargin();
        if (($this->getY() + $size['height']) > $pageExclBorders) {
            if ($this->PageNo() == $this->getNumPages()) {
                $this->AddPage();
            } else {
                $this->SetPage($this->PageNo() + 1);
            }
            return true;
        }
        return false;
    }

    public function getSize()
    {
        if (!$this->_size) {
            $dimension = $this->_component->getImageDimensions();
            if (!$dimension) return null;

            $maxWidth = Kwc_Abstract::getSetting(get_class($this->_component), 'pdfMaxWidth');
            $area = $this->getMaxTextWidth();
            if ($maxWidth > $area || $maxWidth == 0) {
                $height = $dimension["height"] / $dimension["width"] * $area;
                $width = $area;
            } else {
                $height = $dimension["height"] / $dimension["width"] * $maxWidth;
                $width = $maxWidth;
            }
            $this->_size = array('width' => $width, 'height' => $height);
        }
        return $this->_size;
    }

    private function _calculateMm($px)
    {
        $dpi = Kwc_Abstract::getSetting(get_class($this->_component), 'pdfMaxDpi');
        return $px * $dpi / 25.4;
    }

    private function _calculatePx($mm)
    {
        $dpi = Kwc_Abstract::getSetting(get_class($this->_component), 'pdfMaxDpi');
        return 25.4 * $mm / $dpi;
    }
}
