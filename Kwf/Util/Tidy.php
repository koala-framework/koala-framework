<?php
class Kwf_Util_Tidy
{
    static public function repairHtml($html, $config = array())
    {
        if (class_exists('tidy')) {

            $config = array_merge(array(
                'indent'         => true,
                'output-xhtml'   => true,
                'clean'          => false,
                'wrap'           => '86',
                'doctype'        => 'omit',
                'drop-proprietary-attributes' => true,
                'word-2000'      => true,
                'show-body-only' => true,
                'bare'           => true,
                'enclose-block-text'=>true,
                'enclose-text'   => true,
                'join-styles'    => false,
                'join-classes'   => false,
                'logical-emphasis' => true,
                'lower-literals' => true,
                'literal-attributes' => false,
                'indent-spaces' => 2,
                'quote-nbsp'     => true,
                'output-bom'     => false,
                'char-encoding'  =>'utf8',
                'newline'        =>'LF',
                'uppercase-tags' =>false
            ), $config);

            $tidy = new tidy;
            $tidy->parseString($html, $config, 'utf8');
            $tidy->cleanRepair();
            $ret = $tidy->value;

        } else {

            require_once VENDOR_PATH.'/koala-framework/library-htmlawed/htmLawed.php';
            $ret = htmLawed($html);

        }
        return $ret;
    }
}
