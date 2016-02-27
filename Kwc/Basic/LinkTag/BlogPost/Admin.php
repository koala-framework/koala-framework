<?php
class Kwc_Basic_LinkTag_BlogPost_Admin extends Kwc_Basic_LinkTag_Abstract_Admin
{
    public function componentToString(Kwf_Component_Data $data)
    {
        $row = $data->getComponent()->getRow();
        $data = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId('blog_'.$row->blog_post_id, array('subroot' => $data));
        if (!$data) return '';
        return $data->name;
    }

    public function getCardForms()
    {
        $ret = array();
        $blogs = Kwf_Component_Data_Root::getInstance()
            ->getComponentsByClass('Kwc_Blog_Directory_Component', array('ignoreVisible'=>true));
        foreach ($blogs as $blog) {
            $form = Kwc_Abstract_Form::createComponentForm($this->_class, 'child');
            $form->fields['blog_post_id']->setBaseParams(array('blogComponentId'=>$blog->dbId));
            $form->fields['blog_post_id']->setFieldLabel($blog->getPage()->name);
            $form->fields['blog_post_id']->setData(new Kwc_Basic_LinkTag_BlogPost_BlogPostIdData());
            $ret[$blog->dbId] = array(
                'form' => $form,
                'title' => count($blogs) > 1 ? $blog->getTitle() : trlKwf('Blog')
            );
        }
        return $ret;
    }

    public function getVisibleCardForms($cardDbId)
    {
        $ret = array();
        foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($cardDbId, array('ignoreVisible'=>true)) as $card) {
            $blogs = Kwf_Component_Data_Root::getInstance()
                ->getComponentsByClass('Kwc_Blog_Directory_Component', array('subroot'=>$card, 'ignoreVisible'=>true));
            foreach ($blogs as $blog) {
                if (!in_array($blog->dbId, $ret)) {
                    $ret[] = $blog->dbId;
                }
            }
        }
        return $ret;
    }
}
