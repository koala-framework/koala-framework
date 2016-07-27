
<?php foreach($this->feeds as $feed) { ?>
    <link href="<?=$feed->url?>" rel="alternate" type="application/rss+xml" title="<?=htmlspecialchars($feed->getTitle())?>" />
<?php } ?>
