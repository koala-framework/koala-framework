<?php
class Kwc_Newsletter_Subscribe_EmailValidator extends Kwf_Validate_Row_Unique
{
    public function __construct($subscribeComponentId)
    {
        parent::__construct();

        $subscribe = Kwf_Component_Data_Root::getInstance()->getComponentById($subscribeComponentId);
        $this->addSelectExpr(new Kwf_Model_Select_Expr_Equal('newsletter_component_id', $subscribe->getComponent()->getSubscribeToNewsletterComponent()->dbId));

        $this->addSelectExpr(new Kwf_Model_Select_Expr_Equal('unsubscribed', 0));
    }
}
