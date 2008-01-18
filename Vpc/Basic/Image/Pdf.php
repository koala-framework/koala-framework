<?php
class Vpc_Basic_Image_Pdf extends Vpc_Abstract_Pdf
{
    public function writeContent()
    {
        $image = $this->_component->getImageRow();
        $dimension = $image->getImageDimension();
        $file = $image->findParentRow('Vps_Dao_File');
        $area = $this->_pdf->getPageWidth() - ($this->_pdf->getRightMargin() + $this->_pdf->getLeftMargin());
        $width = $dimension["width"] * 0.5;
        $height = $dimension["height"] * 0.5;
        if ($width > $area){
            $height = $height / $width * $area;
            $width = $area;
        }
        $x = ($area - $width) / 2;

        if (($this->_pdf->getY()+$height) > 280) {
            $this->_pdf->addPage();
        }

        $this->_pdf->Image($file->getFileSource(), $this->_pdf->getLeftMargin() + $x, $this->_pdf->getY(), $width, $height, $file->extension);

        $this->_pdf->setY($this->_pdf->getY()+$height + 2);

    }

}
