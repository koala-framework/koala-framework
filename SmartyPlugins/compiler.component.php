<?php
function smarty_compiler_component($tag_attrs, &$compiler)
{
    $_params = $compiler->_parse_attrs($tag_attrs);

    $tag_attrs = preg_replace('#component=\\$([^ ]+)#', 'file=$\1.template component=$\1', $tag_attrs);
    $ret = "if(\$this->_tpl_vars['mode']=='fe') echo '<div id=\"container_'.".$_params['component']."['id'].'\" class=\"component\">' ?>";
    $ret .= "<?php if(isset(\$this->_tpl_vars['component']['template']) && \$this->_tpl_vars['component']['template']!='') { ?>";
    $ret .= $compiler->_compile_include_tag($tag_attrs);
    $ret .= "<?php } ?>";
    $ret .= "<?php if(\$this->_tpl_vars['mode']=='fe') echo '</div>'; ";
    return $ret;
}
?>
