<?php
class Kwc_Abstract_Image_Update_20150309Legacy40002 extends Kwf_Update
{
    //Test update: vps update --class=Update_19
    public function update()
    {
        parent::update();
        $select = new Kwf_Model_Select();
        $select->where(new Kwf_Model_Select_Expr_Or(array(
            new Kwf_Model_Select_Expr_Equals('dimension', 'customcrop'),
            new Kwf_Model_Select_Expr_Equals('dimension', 'custombestfit')
        )));
        $select->order('dimension', 'asc');
        $rows = Kwf_Model_Abstract::getInstance('Kwc_Abstract_Image_Model')->getRows($select);
        foreach ($rows as $row) {
            if ($row->dimension == 'customcrop') {
                $row->dimension = 'custom';
                $row->crop_x = null;
                $row->crop_y = null;
                $row->crop_width = null;
                $row->crop_heigth = null;
            } else if ($row->dimension == 'custombestfit') {
                $row->dimension = 'custom';
                if ($row->imageExists()) {
                    $targetSize = array(
                        'width' => $row->width,
                        'height' => $row->height,
                        'cover' => false,
                    );

                    $outputSize = Kwf_Media_Image::calculateScaleDimensions(
                        $row->getParentRow('Image')->getFileSource(), $targetSize);
                    $row->width = $outputSize['width'];
                    $row->height = $outputSize['height'];
                    $row->crop_x = $outputSize['crop']['x'];
                    $row->crop_y = $outputSize['crop']['y'];
                    $row->crop_width = $outputSize['crop']['width'];
                    $row->crop_height = $outputSize['crop']['height'];
                }
            }
            $row->save();
        }
    }
}
