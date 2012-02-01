<?php
class Kwc_Newsletter_Subscribe_EmailValidator extends Kwf_Validate_Row_Unique
{
    public function __construct($newsletterComponentId)
    {
        parent::__construct();

        $this->addSelectExpr(new Kwf_Model_Select_Expr_Equal('newsletter_component_id', $newsletterComponentId));

        $this->addSelectExpr(new Kwf_Model_Select_Expr_Equal('unsubscribed', 0));
    }
}
