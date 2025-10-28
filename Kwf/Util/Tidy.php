<?php
class Kwf_Util_Tidy
{
    /**
     * @return bool
     */
    static public function supportsDropFontTags()
    {
        return strtotime(tidy_get_release()) < strtotime("2017-11-25");
    }
}
