<?php
class Vpc_Basic_Image_Pdf extends Vpc_Abstract_Pdf
{
    public function writeContent($percentage)
    {
        $image = $this->_component->getImageRow();
        $file = $image->findParentRow('Vps_Dao_File');
        $area = $this->_pdf->getPageWidth() - ($this->_pdf->getRightMargin() + $this->_pdf->getLeftMargin());
        $dimension = $image->getImageDimension();
        if (!$dimension){
            $dimensions = getimagesize ($file->getFileSource());
            $dimension["width"] = $dimensions[0];
            $dimension["height"] = $dimensions[1];
        }


        if ($dimension["width"] > $area || !$dimension["width"]) {
            $height = $dimension["height"] / $dimension["width"] * $area * $percentage;
            $width = $area * $percentage;
        } else {
            $width = $dimension["width"];
            $height = $dimension["height"];
        }

        $x = ($area - $width) / 2;

        if (($this->_pdf->getY()+$height) > 280) {
            $this->_pdf->addPage();
        }

        $this->_pdf->Image($file->getFileSource(), $this->_pdf->getLeftMargin() + $x, $this->_pdf->getY(), $width, $height, $file->extension);

        $this->_pdf->setY($this->_pdf->getY()+$height + 2);

    }

}
