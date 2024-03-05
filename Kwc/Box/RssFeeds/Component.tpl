
<?php foreach($this->feeds as $feed) { ?>
    <link href="<?=$feed->url?>" rel="alternate" type="application/rss+xml" title="<?=Kwf_Util_HtmlSpecialChars::filter($feed->getTitle())?>" />
<?php } ?>
