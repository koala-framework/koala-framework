<?php
class Vpc_Basic_Image_Pdf extends Vpc_Abstract_Pdf
{
    public function writeContent()
    {
        $maxWidth = Vpc_Abstract::getSetting(get_class($this->_component), 'pdfMaxWidth');
        $image = $this->_component->getImageRow();
        $file = $image->findParentRow('Vps_Dao_File');
        $dimension = $image->getImageDimensions();
        $area = $this->getMaxTextWidth();
        $source = $file->getFileSource();
        $tempstring = 'temp_'.$file->filename;
        if ($file) {
            if (!$dimension){
                $dimensions = getimagesize($file->getFileSource());
                $dimension["width"] = $dimensions[0];
                $dimension["height"] = $dimensions[1];
            }

            if ($maxWidth > $area || $maxWidth == 0) {
                $height = $dimension["height"] / $dimension["width"] * $area;
                $width = $area;
            } else {
                $height = $dimension["height"] / $dimension["width"] * $maxWidth;
                $width = $maxWidth;
            }

            $size = array();
            $size['scale'] = Vps_Media_Image::SCALE_DEFORM;
            $size['width'] = $this->_calculateDpi($width);
            $size['height'] = $this->_calculateDpi($height);

            Vps_Media_Image::scale($source, $tempstring, $size);
            $pageExclBorders = $this->getPageHeight()
                                - $this->getBottomMargin();

            if (($this->getY() + $height) > ($pageExclBorders)) {
                if ($this->PageNo() == $this->getNumPages()) {
                    $this->AddPage();
                } else {
                    $this->SetPage($this->PageNo() + 1);
                }
                $this->SetY($this->getTopMargin());
            }
            $this->_pdf->Image($tempstring, $this->getLeftMargin(),
                                    $this->getY(), $width, $height, $file->extension);
            $this->SetY($this->getY() + $height + 2);
        }

        unlink($tempstring);
    }

    private function _calculateDpi ($mm)
    {
        $dpi = Vpc_Abstract::getSetting(get_class($this->_component), 'pdfMaxDpi');
        return ($mm / 2.54) * ($dpi / 10);
    }


}
