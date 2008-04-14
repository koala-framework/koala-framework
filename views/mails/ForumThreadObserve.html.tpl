{trlVps text="Hello [0]!" 0=$fullname}

{trlVps text="A new Post has been written in thread:"} <a href="{$webUrl}{$threadUrl}">{$threadName}</a>
{trlVps text="in the forum of:"} <a href="{$webUrl}">{$webUrl}</a>

{trlVps text="Click the following link to go directly to the mentioned thread:"}
<a href="{$webUrl}{$threadUrl}">{$threadName}</a>

{$applicationName}

--
{trlVps text="This email has been generated automatically. There may be no recipient if you answer to this email."}