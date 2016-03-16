<? if ($this->id) { ?>
<!-- Google Code for Remarketing Tag -->
<!--------------------------------------------------
Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. See more information and instructions on how to setup the tag on: http://google.com/ads/remarketingsetup
--------------------------------------------------->
<? if ($this->ignoreCode) { echo "<!-- Ignore because of config settings\n"; } ?>
<script type="text/javascript">
    /* <![CDATA[ */
    var google_conversion_id = <?=$this->id?>;
    <? if ($this->label) { ?>var google_conversion_label = "<?=$this->label?>";<?php } ?>

    var google_custom_params = window.google_tag_params;
    var google_remarketing_only = true;
    /* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
    <div style="display:inline;">
        <img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/<?=$this->id?>/?value=0&amp;guid=ON&amp;script=0"/>
    </div>
</noscript>
<? if ($this->ignoreCode) { echo "\n-->"; } ?>
<? } ?>
