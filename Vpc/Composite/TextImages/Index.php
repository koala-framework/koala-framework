<?php
class Vpc_Composite_TextImages_Index extends Vpc_Abstract
{
    const NAME = 'Standard.TextImages';
    public $text;
    public $images;
    protected $_settings = array(
        'text' => array(),
        'images' => array(),
        'enlarge' => false,
        'image_position' => 'alternate' // 'left', 'right', 'alternate'
    );
    protected $_tablename = 'Vpc_Composite_TextImage_IndexModel';

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['text'] = $this->text->getTemplateVars('');
        $return['image'] = $this->image->getTemplateVars('');
        $return['imagebig'] = $this->imagebig->getTemplateVars('');
        $return['image_position'] = 'right'; // TODO
        $return['enlarge'] = $this->getSetting('enlarge');
        $return['template'] = 'Composite/TextImage.html';
        return $return;
    }

    public function init()
    {
        // Text
        $st = isset($this->_settings['text']) ? $this->_settings['text'] : array();
        $this->text = $this->createComponent('Vpc_Basic_Text_Index', 0, $st);

        // Images
        $si = isset($this->_settings['images']) ? $this->_settings['images'] : array();
        $table = $this->getTable('Vpc_Composite_TextImages_ImagesModel');
        $where = array(
            'page_id = ?' => $this->getDbId(),
            'component_key = ?' => $this->getComponentKey()
        );
        foreach ($table->fetchAll($where) as $row) {
            $this->images[] = $this->createComponent('Vpc_Basic_Image_Index', $row->id, $si);
        }
    }

    public function getChildComponents()
    {
        return array_merge(array($this->text), $this->images);
    }

}