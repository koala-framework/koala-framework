<?php
class Vpc_Formular_Controller extends Vpc_Paragraphs_Controller
{
   protected $_columns = array(
            array('dataIndex' => 'component_class',
                  'header'    => 'Komponente',
                  'width'     => 300),
            array('dataIndex' => 'visible',
                  'header'    => 'Sichtbar',
                  'width'     => 50,
                  'editor'    => 'Checkbox',
                  ),
            array('dataIndex' => 'description',
                  'header'    => 'Bezeichnung',
                  'width'     => 150,
                  'editor'    => 'TextField',
                  ),
            array('dataIndex' => 'mandatory',
                  'header'    => 'Verpflichtend',
                  'width'     => 80,
                  'editor'    => 'Checkbox',
                  ),
            array('dataIndex' => 'no_cols',
                  'header'    => 'Ganze Breite',
                  'width'     => 50,
                  'editor'    => 'Checkbox',
                  ),
            array('dataIndex' => 'page_id',
                  'header'    => 'page_id',
                  'type'      => 'string',
                  'width'     => 50,
                  'hidden'   =>  true,
                  ),
            array('dataIndex' => 'id',
                  'header'    => 'id',
                  'width'     => 50,
                  'hidden'   =>  true,
                  )

            );
    protected $_tableName = 'Vpc_Formular_Model';
}