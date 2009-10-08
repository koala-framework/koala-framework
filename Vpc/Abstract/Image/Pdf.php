<?php
class Vpc_Abstract_Image_Pdf extends Vpc_Abstract_Pdf
{
    private $_size;

    public function writeContent($setCoordinates = true)
    {
        $image = $this->_component->getImageRow();
        $file = $image->getParentRow('Image');

        if ($file && is_file($file->getFileSource())) {
            $source = $file->getFileSource();
            $size = $this->getSize();

            $imageSize = array(
                'scale' => Vps_Media_Image::SCALE_BESTFIT,
                'width' => $this->_calculateMm($size['width']),
                'height' => $this->_calculateMm($size['height'])
            );
            $content = Vps_Media_Image::scale($source, $imageSize);
            $filter = new Vps_Filter_Ascii();
            $tempFilename = 'temp_'.$filter->filter($file->filename);
            $handle = fopen($tempFilename, 'wb');
            fwrite($handle, $content);
            fclose($handle);
            $data = getimagesize($tempFilename);
            if ($data[2] == 2) { // nur jpgs ausgeben
                if ($setCoordinates && $this->addPageIfNecessary()) {
                    $x = $this->getX();
                    $this->SetY($this->getTopMargin());
                    $this->SetX($x);
                }
                $this->_pdf->Image(
                    $tempFilename, $this->getX(), $this->getY(),
                    $this->_calculatePx($data[0]), $this->_calculatePx($data[1]), $file->extension
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
            $file = $this->_component->getImageRow()->getParentRow('Image');
            if (!$file || !is_file($file->getFileSource())) {
                return null;
            }

            $dimension = $this->_component->getImageDimensions();
            if (!$dimension){
                $dimensions = getimagesize($file->getFileSource());
                $dimension["width"] = $dimensions[0];
                $dimension["height"] = $dimensions[1];
            }

            $maxWidth = Vpc_Abstract::getSetting(get_class($this->_component), 'pdfMaxWidth');
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
        $dpi = Vpc_Abstract::getSetting(get_class($this->_component), 'pdfMaxDpi');
        return $px * $dpi / 25.4;
    }

    private function _calculatePx($mm)
    {
        $dpi = Vpc_Abstract::getSetting(get_class($this->_component), 'pdfMaxDpi');
        return 25.4 * $mm / $dpi;
    }
}
