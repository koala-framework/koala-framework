{trlVps text="Hello [0]!" 0=$fullname}

{trlVps text="This email has been generated using the lost password function
at [0]" 0=$webUrl}
{trlVps text="Please use the following link to choose yourself a new password."}
{$webUrl}/vps/user/login/activate?code={$activationCode}

{trlVps text="If you did not request this email you may just ignore it and
use the login as before."}

{$applicationName}

--
{trlVps text="This email has been generated automatically. There may be no
recipient if you answer to this email."}