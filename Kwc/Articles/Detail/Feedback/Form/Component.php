<?php
class Kwc_Articles_Detail_Feedback_Form_Component extends Kwc_Feedback_Form_Component
{
    protected function _getRecipient()
    {
        $article = $this->getData()->parent->parent->row;
        $author = $article->getParentRow('Author');
        return array(
            'email' => $author->feedback_email,
            'name' => $author->firstname . ' ' . $author->lastname
        );
    }
}
