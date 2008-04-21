<?php
class Vpc_Composite_ParagraphsImage_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName'     => 'ParagraphsImage',
            'componentIcon'     => new Vps_Asset('page_white_picture'),
            'childComponentClasses' => array(
                'paragraphs'   => 'Vpc_Paragraphs_Component',
                'image'        => 'Vpc_Basic_Image_Enlarge_Component'
            )
        ));
        $ret['assetsAdmin']['dep'][] = 'VpsTabPanel';
        return $ret;
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        return $return;
    }
}
