<?php
class Vpc_FormDynamic_Basic_Form_Form_Component extends Vpc_Form_Dynamic_Form_Component
{
    protected function _createModel(array $config)
    {
        foreach ($config['referenceMap'] as $k=>$i) {
            if ($i['refModelClass'] == 'Vps_Uploads_Model') {
                $config['referenceMap'][$k]['refModelClass'] = 'Vpc_FormDynamic_Basic_Form_Form_UploadsModel';
            }
        }
        $config['componentClass'] = get_class($this);
        $config['proxyModel'] = new Vps_Model_FnF();
        $config['mailerClass'] = 'Vps_Mail';
        $config['spamFields'] = array();
        $ret = new Vpc_Form_Dynamic_Form_MailModel($config);

        $uploads = new Vps_Uploads_TestModel();
        $dir = $uploads->getUploadDir().'/mailattachments';
        mkdir($uploads->getUploadDir().'/mailattachments');
        $ret->setAttachmentSaveFolder($dir);
        return $ret;
    }
}
