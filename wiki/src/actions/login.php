<?php

function login($path, $action, $content)
{
    $content['UserNav']->Active("Login");
    $content['Title'] = "Super Secret Login Form";

    if($_POST)
    {
        if($_POST['Password'] == LOGIN_PASSWORD)
        {
            $_SESSION['bypass'] = true;
            $content['Body'] = "YOU DID IT FRIEND!!<br /><br />You will now be brought back to your previous page.";
            $content['Body'] .= Redirect(str_replace("//", "/", "/$path"));
        }
    }
    
    if(empty($_SESSION['bypass']))
    {
        $content['Body'] .= "Protip: The super secret password is the same as the PIBDGAF ichc password.";
        
        $Form['_Options'] = "action:".str_replace("//", "/", "/$path/?login").";";
        $Form['Password']['Text'] = "Password:";
        $Form['Password']['Form'] = "name:Password; type:password;";
        $Form['Submit']['Form'] = "type:submit; value:Submit;";
        
        $content['Body'] .= Format($Form, Form);
    }

    return $content;
}
