<?php
class Vpc_FormDynamic_Basic_Form_Form_Component extends Vpc_Form_Dynamic_Form_Component
{
    protected function _createModel($referenceMap)
    {
        foreach ($referenceMap as $k=>$i) {
            if ($i['refModelClass'] == 'Vps_Uploads_Model') {
                $referenceMap[$k]['refModelClass'] = 'Vpc_FormDynamic_Basic_Form_Form_UploadsModel';
            }
        }
        $ret = new Vps_Model_Mail(array(
            'componentClass' => get_class($this),
            'proxyModel' => new Vps_Model_FnF(),
            'referenceMap' => $referenceMap,
            'mailerClass' => 'Vps_Mail',
            'spamFields' => array()
        ));

        $uploads = new Vps_Uploads_TestModel();
        $dir = $uploads->getUploadDir().'/mailattachments';
        mkdir($uploads->getUploadDir().'/mailattachments');
        $ret->setAttachmentSaveFolder($dir);
        return $ret;
    }
}
