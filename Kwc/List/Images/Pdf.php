<?php
class Kwc_List_Images_Pdf extends Kwc_Abstract_Pdf
{
    public function writeContent()
    {
        $this->setX($this->getLeftMargin());
        $nr = 0;
        $components = $this->_component->getData()->getChildComponents(array('generator' => 'child'));
        $count = sizeof($components);
        $columns = $this->_getGalleryColumns();
        if ($columns == 0) { $columns = 1; }
        foreach ($components as $component) {
            // immer y vor x setzen, weil sety setzt x auf leftmargin"
            $image = $component->getComponent()->getPdfWriter($this->_pdf);
            $size = $image->getSize();
            if (($nr % $columns == 0 || $nr == $count) &&
                $image->AddPageIfNecessary())
            {
                $this->setY($this->getTopMargin());
                $this->setX($this->getLeftMargin());
            }

            $ret = $image->writeContent(false);
            if ($ret) {
                $nr++;
                if ($nr % $columns == 0 || $nr == $count) {
                    $this->setY($this->getY() + $size['height'] + 2);
                    $this->setX($this->getLeftMargin());
                } else {
                    $this->setY($this->getY());
                    $this->setX($this->getX() + $size['width'] + 2);
                }
            }
        }
    }

    protected function _getGalleryColumns()
    {
        return Kwc_Abstract::getSetting(get_class($this->_component), 'pdfColumns');
    }
}
