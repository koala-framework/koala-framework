<?php
class Vpc_Composite_ContentImage_IndexController extends Vps_Controller_Action
{
    public function indexAction()
    {
        $pageId = $this->component->getDbId();
        $componentKey = $this->component->getComponentKey();
        $config['itemUrls']['Content'] = "/component/edit/Vpc_ParagraphsIndex_Index/$pageId$componentKey-1/";
        $config['itemUrls']['Image'] = "/component/edit/Vpc_Basic_Image_Index/$pageId$componentKey-2/";
        $this->view->ext('Vpc.Composite.ContentImage.Index', $config);
    }
}