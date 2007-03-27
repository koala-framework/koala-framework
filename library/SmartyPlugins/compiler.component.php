<?php
function smarty_compiler_component($tag_attrs, &$compiler)
{
    $_params = $compiler->_parse_attrs($tag_attrs);

    $tag_attrs = preg_replace('#component=\\$([^ ]+)#', 'file=$\1.template component=$\1', $tag_attrs);
    $ret = "if(\$this->_tpl_vars['mode']=='edit') echo '<div id=\"container_'.".$_params['component']."['id'].'\">' ?>";
    $ret .= $compiler->_compile_include_tag($tag_attrs);
    $ret .= "<?php if(\$this->_tpl_vars['mode']=='edit') echo '</div>'; ";
    return $ret;
}
?>
