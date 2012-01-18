<?php
class Kwc_NewsletterCategory_Subscribe_Component extends Kwc_Newsletter_Subscribe_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['mail'] = 'Kwc_NewsletterCategory_Subscribe_Mail_Component';
        $ret['extConfig'] = 'Kwc_NewsletterCategory_Subscribe_ExtConfig';
        return $ret;
    }

    public function processInput(array $postData)
    {
        if (!empty($postData[$this->getData()->componentId]) && !empty($postData['form_email'])) {
            if (!$this->getForm()) return;

            $model = $this->getForm()->getModel();
            //TODO: don't poke into $postData directly, get value from field instead
            $exists = $model->getRow($model->select()->whereEquals('email', $postData['form_email']));
            if ($exists) {
                //already subscribed
                $categories = $this->getForm()->getCategories();
                if (count($categories) == 1) {
                    $catKeys = array_keys($categories);
                    $toModel = Kwf_Model_Abstract::getInstance('Kwc_NewsletterCategory_Subscribe_SubscriberToCategory');
                    $toRow = $toModel->getRow($toModel->select()
                        ->whereEquals('subscriber_id', $exists->id)
                        ->whereEquals('category_id', $catKeys[0])
                    );
                    if ($toRow) {
                        parent::processInput($postData);
                    } else {
                        $this->_setProcessed();
                        $this->getForm()->addCategoryIfOnlyOne($exists);

                        if ($this->getSuccessComponent() && $this->getSuccessComponent()->isPage &&
                            (!isset($postData['doNotRelocate']) || !$postData['doNotRelocate'])
                        ) {
                            header('Location: ' . $this->getSuccessComponent()->url);
                            exit;
                        }
                    }
                } else {
                    // if more than one category: anything special neccessary here?
                    //TODO: implement "neccessary special"
                    parent::processInput($postData);
                }
            } else {
                //not yet subscribed, form inserts new row plus category
                parent::processInput($postData);
            }
        } else {
            //no post
            parent::processInput($postData);
        }
    }

    protected function _initForm()
    {
        $this->_form = new Kwc_NewsletterCategory_Subscribe_FrontendForm(
            'form', $this->getData()->componentId
        );
    }
}
