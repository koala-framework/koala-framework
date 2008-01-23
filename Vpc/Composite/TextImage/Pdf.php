<?php
class Vpc_Composite_TextImage_Pdf extends Vpc_Abstract_Pdf
{
    public function writeContent()
    {
        $image = $this->_component->image;
        $text = $this->_component->text;
        $position = $this->_component->getTextImageRow()->image_position;
        $area = $this->_pdf->getPageWidth() - ($this->_pdf->getRightMargin() + $this->_pdf->getLeftMargin());

        $startY = $this->_pdf->getY();
        $startPage = $this->_pdf->getPage();
        $marginPicLeft = 0;
        $marginPicRight = 0;
        $marginTextLeft = 0;
        $marginTextRight = $this->_pdf->getRightMargin();

        if ($position == "right")
        {
            $marginPicLeft = $this->_pdf->getLeftMargin() + $area / 3 * 2;
            $marginPicRight = $this->_pdf->getRightMargin();
            $marginTextRight = $this->_pdf->getPageWidth() - ($marginPicLeft);
            $marginTextLeft = $this->_pdf->getLeftMargin();
        }
        else
        {
            $marginTextLeft = $this->_pdf->getLeftMargin() + $area / 3;
            $marginTextRight = $this->_pdf->getRightMargin();
            $marginPicRight = $this->_pdf->getPageWidth() - ($marginTextLeft);
            $marginPicLeft = $this->_pdf->getLeftMargin();
        }


        $tempMarginLeft = $this->_pdf->getLeftMargin();
        $tempMarginRight = $this->_pdf->getRightMargin();

        $this->_pdf->setLeftMargin($marginPicLeft);
        $this->_pdf->setRightMargin($marginPicRight);
        $image->getPdfWriter($this->_pdf)->writeContent();




        $tempY = $this->_pdf->getY();

        if ($this->_pdf->getPage() == $startPage)
            $this->_pdf->setY($startY - 3.5);
        else
           $this->_pdf->setY($this->_pdf->getTopMargin() - 3.5);

        $this->_pdf->setLeftMargin($marginTextLeft);
        $this->_pdf->setRightMargin($marginTextRight);
        $text->getPdfWriter($this->_pdf)->writeContent();



        $this->_pdf->setLeftMargin($tempMarginLeft);
        $this->_pdf->setRightMargin($tempMarginRight);

        //$this->_pdf->setSite($this->_pdf->getPage());

        if ($tempY > $this->_pdf->getY())
            $this->_pdf->setY($tempY);


        // $this->_pdf->setLeftMargin($tempMargin);


        //$file = $image->findParentRow('Vps_Dao_FileComponent');
        //$this->_pdf->Image($file->getFileSource(), $this->_pdf->getX(), $this->_pdf->getY(), 0, 0, $file->extension);
    }

}
