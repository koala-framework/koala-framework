<?php
class Kwc_FormDynamic_Basic_Form_Form_Component extends Kwc_Form_Dynamic_Form_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['success'] = 'Kwc_Form_Success_Component';
        return $ret;
    }

    protected function _createModel(array $config)
    {
        foreach ($config['referenceMap'] as $k=>$i) {
            if ($i['refModelClass'] == 'Kwf_Uploads_Model') {
                $config['referenceMap'][$k]['refModelClass'] = 'Kwc_FormDynamic_Basic_Form_Form_UploadsModel';
            }
        }
        $config['componentClass'] = get_class($this);
        $config['proxyModel'] = new Kwf_Model_FnF();
        $config['mailerClass'] = 'Kwc_FormDynamic_Basic_Form_Form_Mail';
        $config['spamFields'] = array();
        $ret = new Kwc_Form_Dynamic_Form_MailModel($config);

        $uploads = new Kwf_Uploads_TestModel();
        $dir = $uploads->getUploadDir().'/mailattachments';
        mkdir($uploads->getUploadDir().'/mailattachments');
        $ret->setAttachmentSaveFolder($dir);
        return $ret;
    }
}
