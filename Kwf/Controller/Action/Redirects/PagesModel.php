<?php
class Kwf_Controller_Action_Redirects_PagesModel extends Kwf_Component_Model
{
    protected function _getPages($page)
    {
        return $page->getChildComponents(array(
            'ignoreVisible' => true,
            'generatorFlags' => array(
                'showInLinkInternAdmin' => true
            )
        ));
    }

}
