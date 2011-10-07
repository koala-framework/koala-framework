<? foreach($this->feeds as $feed) { ?>
    <link href="<?=$feed->url?>" rel="alternate" type="application/rss+xml" title="<?=$feed->getTitle()?>" />
<? } ?>
