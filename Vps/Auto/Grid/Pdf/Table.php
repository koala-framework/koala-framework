<?php
class Vps_Auto_Grid_Pdf_Table extends Vps_Auto_Grid_Pdf_Abstract
{

    protected $_fields = array();
    protected $_lines = array('x' => array(),
                              'y' => array());

    public function setFields($fields)
    {
        parent::setFields($fields);

        $numAutoWidth = 0;

        $remainingAutoWidth = $this->getPageWidth() - $this->GetX() * 2;
        foreach ($this->_fields as $options) {
            if ($options['width'] == 0) {
                $numAutoWidth++;
            } else {
                $remainingAutoWidth -= $options['width'];
            }
        }

        if ($remainingAutoWidth < $numAutoWidth) {
            $remainingAutoWidth = $numAutoWidth;
        }
        $autoWidth = $remainingAutoWidth / $numAutoWidth;

        foreach ($this->_fields as $field => $options) {
            if ($options['width'] == 0) {
                $this->_fields[$field]['width'] = $autoWidth;
            }
        }
    }

    protected function _setLineValue($xy, $value)
    {
        $xy = strtolower($xy);
        if ($xy != 'x' && $xy != 'y') {
            throw new Vps_Exception("Only `x` or `y` allowed for first parameter "
                                   ."of `setLineValue` (case-insensitive)");
        }

        if (!in_array($value, $this->_lines[$xy])) {
            $this->_lines[$xy][] = $value;
        }
    }

    protected function _setLineValueXY($valueX, $valueY)
    {
        $this->_setLineValue('x', $valueX);
        $this->_setLineValue('y', $valueY);
    }

    public function writeHeader()
    {
        $this->SetFont('vera', 'B', 8);
        $currentRowY = $this->GetY();
        $this->_setLineValue('y', $currentRowY);
        $nextX = $this->GetX();
        foreach ($this->_fields as $field => $options) {
            $this->_setLineValue('x', $nextX);
            $this->SetXY($nextX, $currentRowY);
            $this->MultiCell($options['width'], 4, $options['header'], 0, 'L');
            if (!isset($nextRowY) || $this->GetY() > $nextRowY) {
                $nextRowY = $this->GetY();
            }
            $nextX += $options['width'];
        }
        $this->SetY($nextRowY);

        $this->_setLineValueXY($nextX, $nextRowY);

        $this->SetFont('vera', '', 8);
    }

    public function writeRow($row)
    {
        $currentRowY = $this->GetY();
        $this->_setLineValue('y', $currentRowY);
        $nextX = $this->GetX();
        foreach ($this->_fields as $field => $options) {
            $this->_setLineValue('x', $nextX);
            $this->SetXY($nextX, $currentRowY);
            if (isset($row->$field)) {
                $this->MultiCell($options['width'], 4, $row->$field, 0, 'L');
            }
            if (!isset($nextRowY) || $this->GetY() > $nextRowY) {
                $nextRowY = $this->GetY();
            }
            $nextX += $options['width'];
        }
        $this->SetY($nextRowY);

        $this->_setLineValueXY($nextX, $nextRowY);
    }

    public function drawLines()
    {
        if (count($this->_lines['x']) >= 2 && count($this->_lines['y']) >= 2) {
            sort($this->_lines['x']);
            sort($this->_lines['y']);

            $minX = $this->_lines['x'][0];
            $maxX = $this->_lines['x'][count($this->_lines['x'])-1];

            $minY = $this->_lines['y'][0];
            $maxY = $this->_lines['y'][count($this->_lines['y'])-1];

            // draw horizontal lines
            foreach ($this->_lines['y'] as $lineY) {
                $this->Line($minX, $lineY, $maxX, $lineY);
            }

            // draw vertical lines
            foreach ($this->_lines['x'] as $lineX) {
                $this->Line($lineX, $minY, $lineX, $maxY);
            }

            $this->_lines = array('x' => array(),
                                  'y' => array());
        }
    }

}
