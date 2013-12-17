<?php
/**
 * Just copy this into your bootstrap to find out which components have to be checked
 * It's returning a list of every page containing a component with alternative preview-image
 */
$data = Kwf_Registry::get('db')->query('SELECT * FROM `kwc_basic_image` WHERE data like \'%preview_image":true%\'')->fetchAll();
foreach ($data as $row) {
    $component = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($row['component_id'], array('ignoreVisible' => false));
    if ($component) {
        if ($component->componentClass != 'Kwc_Basic_LinkTag_Empty_Component') {
            if ($component->getComponent()->getRow()->kwf_upload_id) {
                //d($component->componentClass);
                $components[$component->getAbsoluteUrl()] = '<a href="'.$component->getAbsoluteUrl().'">'.$component->getAbsoluteUrl().'</a>';
            }
        }
//         $components[] = $component->getUrl();
    }
}
d(array_values($components));
