<?php
class Vpc_Composite_Images_Index extends Vpc_Abstract
{
    const NAME = 'Standard.Images';
    public $images;
    protected $_settings = array(
        'extensions'        => array('jpg', 'gif', 'png'),
        'size'              => array(400, 300), // Leeres Array -> freie Wahl, array(width, height), array(array(width, height), ...)
        'allow'             => array(Vps_Media_Image::SCALE_BESTFIT),
        'filename'          => 'filename'
    );
    protected $_tablename = 'Vpc_Composite_Images_IndexModel';

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        foreach ($this->images as $c) {
            $return['images'][] = $c->getTemplateVars();
        }
        $return['template'] = 'Composite/Images.html';
        return $return;
    }

    public function init()
    {
        $table = $this->getTable('Vpc_Composite_Images_IndexModel');
        $where = array(
            'page_id = ?' => $this->getDbId(),
            'component_key = ?' => $this->getComponentKey()
        );
        if (!$this->showInvisible()) {
            $where['visible = ?'] = 1;
        }
        foreach ($table->fetchAll($where) as $row) {
            $this->images[$row->id] = $this->createComponent('Vpc_Basic_Image_Index', $row->id, $this->getSettings());
        }
    }

    public function getChildComponents()
    {
        return $this->images;
    }

}