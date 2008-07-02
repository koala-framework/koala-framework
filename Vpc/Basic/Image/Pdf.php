<?php
class Vpc_Basic_Image_Pdf extends Vpc_Abstract_Pdf
{
    public function writeContent()
    {
        $maxWidth = Vpc_Abstract::getSetting(get_class($this->_component), 'pdfMaxWidth');
        $image = $this->_component->getImageRow();
        $file = $image->findParentRow('Vps_Dao_File');
        $area = $this->_pdf->getPageWidth() - ($this->_pdf->getRightMargin() + $this->_pdf->getLeftMargin());
        $dimension = $image->getImageDimensions();
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

            $x = ($area - $width) / 2;

            if (($this->_pdf->getY()+$height) > 280) {
                $this->_pdf->addPage();
            }

            $this->_pdf->Image($file->getFileSource(), $this->_pdf->getLeftMargin() + $x, $this->_pdf->getY(), $width, $height, $file->extension);
            $this->_pdf->setY($this->_pdf->getY()+$height + 2);

        }
    }


}
