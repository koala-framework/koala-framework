{trlVps text="Hello [0]!" 0=$fullname}<br /><br />

{trlVps text="A new entry has been written in your guestbook."}<br />
<a href="{$webUrl}{$profileUrl}">{trlVps text="Click here to go directly to you profile"}</a><br /><br />

{trlVps text="This is the text that was saved in your guestbook:"}<br />
{$content}<br /><br />

{$applicationName}<br /><br />

--<br />
{trlVps text="This email has been generated automatically. There may be no recipient if you answer to this email."}