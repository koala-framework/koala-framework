<?php
class Vpc_Composite_Images_Pdf extends Vpc_Abstract_Pdf
{
    public function writeContent()
    {
        $this->setX($this->getLeftMargin());
        $nr = 0;
        $components = $this->_component->getData()->getChildComponents(array('generator' => 'child'));
        $count = sizeof($components);
        $columns = Vpc_Abstract::getSetting(get_class($this->_component), 'pdfColumns');
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

}
