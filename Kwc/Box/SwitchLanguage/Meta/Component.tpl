<?foreach ($this->languages as $language) { ?>
<link rel="alternate" hreflang="<?=$language['page']->getLanguage()?>" href="<?=$language['page']->url?>" />
<? } ?>