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

    public function insertSubscriptionWithCategory(Kwc_Newsletter_Subscribe_Row $row, $categoryId)
    {
        $exists = $this->_subscriptionExists($row);
        $nl2cat = Kwf_Model_Abstract::getInstance('Kwc_NewsletterCategory_Subscribe_SubscriberToCategory');
        if ($exists) {
            //already subscribed
            $s = new Kwf_Model_Select();
            $s->whereEquals('email', $row->email);
            $s->whereEquals('newsletter_component_id', $this->getSubscribeToNewsletterComponent()->dbId);
            $s->where(new Kwf_Model_Select_Expr_Or(array(
                new Kwf_Model_Select_Expr_Equal('unsubscribed', 1),
                new Kwf_Model_Select_Expr_Equal('activated', 1)
            )));
            $row = $this->getForm()->getModel()->getRow($s);

            $s = $nl2cat->select()
                ->whereEquals('subscriber_id', $row->id)
                ->whereEquals('category_id', $categoryId);
            if ($nl2cat->countRows($s)) {
                //already subscribed to given category
                return false;
            }
        }

        if (!$exists) {
            $s = new Kwf_Model_Select();
            $s->whereEquals('email', $row->email);
            $s->whereEquals('newsletter_component_id', $this->getSubscribeToNewsletterComponent()->dbId);
            $s->where(new Kwf_Model_Select_Expr_Or(array(
                new Kwf_Model_Select_Expr_Equal('unsubscribed', 1),
                new Kwf_Model_Select_Expr_Equal('activated', 0)
            )));
            $deleteRow = $this->getForm()->getModel()->getRow($s);
            if ($deleteRow) {
                $deleteRow->delete();
            }
            $this->_beforeInsert($row);
            $row->save();
        }
        $nl2CatRow = $nl2cat->createRow();
        $nl2CatRow->subscriber_id = $row->id;
        $nl2CatRow->category_id = $categoryId;
        $nl2CatRow->save();
        if (!$exists) {
            $this->_afterInsert($row);
        }
        return true;
    }

    //this method is a big mess and will break soon
    public function processInput(array $postData)
    {
        $emailField = $this->getForm()->getByName('email')->getFieldName();
        if (!empty($postData[$this->getData()->componentId]) && !empty($postData[$emailField])) {
            if (!$this->getForm()) return;

            $model = $this->getForm()->getModel();
            //TODO: don't poke into $postData directly, get value from field instead
            $s = $model->select();
            $s->whereEquals('newsletter_component_id', $this->getSubscribeToNewsletterComponent()->dbId);
            $s->whereEquals('email', $postData[$emailField]);
            $s->whereEquals('unsubscribed', false);
            $exists = $model->getRow($s);
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

                        //TODO the following code is copied from Kwc_Form, why not make a protected method or something?
                        if ($this->getSuccessComponent() && $this->getSuccessComponent()->isPage &&
                            (!isset($postData['doNotRelocate']) || !$postData['doNotRelocate'])
                        ) {
                            Kwf_Util_Redirect::redirect($this->getSuccessComponent()->url);
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
}
