<?php
class Kwc_Posts_Detail_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['actions'] = 'Kwc_Posts_Detail_Actions_Component';
        $ret['generators']['child']['component']['signature'] = 'Kwc_Posts_Detail_Signature_Component';
        $ret['useGravatar'] = true;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $data = $this->getData();

        $ret['content'] = self::replaceCodes($data->row->content);
        $ret['user'] = null;
        $ret['avatar'] = null;

        $userDir = Kwf_Component_Data_Root::getInstance()
            ->getComponentByClass(
                'Kwc_User_Directory_Component',
                array('subroot' => $this->getData())
            );
        if ($userDir) {
            $userComponent = $userDir->getChildComponent('_'.$data->row->user_id);
            if ($userComponent) {
                $ret['user'] = $userComponent;

                $avatarComponent = $userComponent->getChildComponent('-general');
                if ($avatarComponent) {
                    $avatarComponent = $avatarComponent->getChildComponent('-avatar');
                    if ($avatarComponent) {
                        $avatarComponent = $avatarComponent->getChildComponent('-small');
                        if ($avatarComponent) {
                            $ret['avatar'] = $avatarComponent;
                        }
                    }
                }
            }
        } else {
            if (isset($data->row->name)) {
                $ret['user'] = $data->row->name;
            }
            if (isset($data->row->email) && $data->row->email && $this->_getSetting('useGravatar')) {
                $ret['avatar'] = 'http://www.gravatar.com/avatar/'.md5(trim(strtolower($data->row->email))).'?s=68&d=mm';
            }
        }
        $select = $data->parent->getGenerator('detail')->select($data->parent)
            ->where('create_time <= ?', $data->row->create_time);
        $ret['postNumber'] = $data->parent->countChildComponents($select);
        return $ret;
    }

    static public function replaceCodes($content)
    {
        // html entfernen
        $content = htmlspecialchars($content);

        // smileys
        $content = preg_replace('/:-?\)/', '<img src="/assets/silkicons/emoticon_smile.png" alt=":-)" />', $content);
        $content = preg_replace('/:-?D/', '<img src="/assets/silkicons/emoticon_grin.png" alt=":-D" />', $content);
        $content = preg_replace('/:-?P/', '<img src="/assets/silkicons/emoticon_tongue.png" alt=":-P" />', $content);
        $content = preg_replace('/:-?\(/', '<img src="/assets/silkicons/emoticon_unhappy.png" alt=":-(" />', $content);
        $content = preg_replace('/;-?\)/', '<img src="/assets/silkicons/emoticon_wink.png" alt=";-)" />', $content);

        // zitate
        $content = str_replace('[quote]', '<fieldset class="quote"><legend>Zitat</legend>', $content, $countOpened);

        $content = preg_replace('/\[quote=([^\]]*)\]/i',
            '<fieldset class="quote"><legend>Zitat von $1</legend>',
            $content,
            -1, $countOpenedPreg
        );

        $content = str_replace('[/quote]', '</fieldset>', $content, $closed);

        $open = $countOpened + $countOpenedPreg;

        while ($open > $closed) {
            $content .= '</fieldset>';
            $closed++;
        }
        while ($closed > $open) {
            $content = '<fieldset class="quote"><legend>Zitat</legend>'.$content;
            $open++;
        }

        // automatische verlinkung
        $truncate = new Kwf_View_Helper_Truncate();
        $pattern = '/((https?:\/\/www\.)|(https?:\/\/)|(www\.)){1,1}([a-z0-9äöü;\/?:@=&!*~#%\'+$.,_-]+)/i';
        $offset = 0;
        while (preg_match($pattern, $content, $matches, PREG_OFFSET_CAPTURE, $offset)) {
            if (!preg_match('/^http/', $matches[1][0])) $matches[1][0] = 'http://'.$matches[1][0];
            $showUrl = preg_replace('/https?:\/\//', '', $matches[1][0])
                .$truncate->truncate($matches[5][0], 60, '...', true);

            $replace = "<a href=\"{$matches[1][0]}{$matches[5][0]}\" "
                ."title=\"{$matches[1][0]}{$matches[5][0]}\" rel=\"popup_blank\">$showUrl</a>";
            $content = substr($content, 0, $matches[0][1])
                .$replace.substr($content, $matches[0][1] + strlen($matches[0][0]));
            $offset = $matches[0][1] + strlen($replace);
        }

        return nl2br($content);
    }

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta($componentClass);
        $ret[] = new Kwf_Component_Cache_Meta_Static_GeneratorRow();
        return $ret;
    }
}
