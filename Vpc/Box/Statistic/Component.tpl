<? if ($this->statistic) { ?>
<script type="text/javascript"><!--
    if (typeof count != 'undefined') {
        <? foreach ($this->statistic as $temptable => $vars) { ?>
        count('<?=$temptable?>', {<?=implode(', ', $vars)?>}, '<?=$this->domain ?>');
        <? } ?>
    }
//--></script>
<? } ?>