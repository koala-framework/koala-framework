{trlVps text="Hello [0]!" 0=$fullname}

{trlVps text="Your account at [0]
has just been created." 0=$webUrl}
{trlVps text="Please use the following link to choose yourself a password and to login"}
{$webUrl}{$activationUrl}?code={$activationCode}

{$applicationName}

--
{trlVps text="This email has been generated automatically. There may be no recipient if you answer to this email."}