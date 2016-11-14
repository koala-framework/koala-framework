<?php
class Kwf_Form_Field_Recaptcha extends Kwf_Form_Field_Abstract
{
    public function validate($row, $postData)
    {
        $ret = parent::validate($row, $postData);

        if (empty($_REQUEST["g-recaptcha-response"])) {
            $ret[] = array(
                'message' => trlKwf('Please check captcha checkbox'),
                'field' => $this
            );
            return $ret;
        }

        $validated = false;

        $client = new Zend_Http_Client('https://www.google.com/recaptcha/api/siteverify');
        $client->setParameterPost(array(
            'secret' => Kwf_Config::getValue('recaptcha.privateKey'),
            'response' => $_REQUEST["g-recaptcha-response"],
            'remoteip' => $_SERVER["REMOTE_ADDR"]
        ));
        $response = $client->request(Zend_Http_Client::POST);
        if ($response->isSuccessful()) {
            $result = json_decode($response->getBody(), true);
            $hostname = isset($_SERVER['HTTP_HOST']) ?
                $_SERVER['HTTP_HOST'] :
                Kwf_Config::getValue('server.domain');
            if ($result['success'] && $result['hostname'] == $hostname) {
                $validated = true;
            }
        }
        if (!$validated) {
            $ret[] = array(
                'message' => trlKwf('Please solve captcha correctly'),
                'field' => $this
            );
        }

        return $ret;
    }

    public function getTemplateVars($values, $fieldNamePostfix = '', $idPrefix = '')
    {
        $ret = parent::getTemplateVars($values, $fieldNamePostfix, $idPrefix);
        $fieldname = $this->getFieldName().$fieldNamePostfix;
        $siteKey = Kwf_Config::getValue('recaptcha.publicKey');
        $ret['html'] = "<div data-recaptcha=\"$fieldname\" data-site-key=\"$siteKey\"></div>";
        return $ret;
    }
}
