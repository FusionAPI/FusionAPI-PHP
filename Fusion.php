<?php
if (!isset($_SESSION))
{
    session_start();
}
class Fusion
{
    private static $BaseURL = "https://fusionapi.dev/app/{APPID}/api";
    private static $ExecuteURL = "https://fusionapi.dev/executeapi/";

	public static function Check42FA($username)
    {
        $ch = curl_init(self::$BaseURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $values =
        [
        "action" => "has2fa",
        "username" => $username
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $values);
        $data = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if($data["status"] == "true")
            $_SESSION["validate_2fa"] = "true";
        else
            $_SESSION["validate_2fa"] = "false";
    }

    public static function Login($username, $password, $g2fa = null)
    {
        Fusion::Check42FA($username);
        $ch = curl_init(self::$BaseURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if ($_SESSION["validate_2fa"] == "true") {
            // Login Contents (Action, Username, Password, 2FA)
            $login_contents = [ "action" => "login", "username" => $username, "password" => $password, "2fa" => $g2fa ];
        }
        else {
            // Login Contents (Action, Username, Password)
            $login_contents = [ "action" => "login", "username" => $username, "password" => $password ];
        }

        curl_setopt($ch, CURLOPT_POSTFIELDS, $login_contents);
        $logindata = json_decode(curl_exec($ch), true);

        switch ($logindata["error"])
        {
            case "1":
                return false;
            break;
            case "0":
                $_SESSION["session"] = $logindata["session"];

                $myblob_contents = [ "action" => "myblob", "session" => $_SESSION["session"] ];
                $appblob_contents = [ "action" => "appblob", "session" => $_SESSION["session"] ];

                curl_setopt($ch, CURLOPT_POSTFIELDS, $myblob_contents);
                $myblobdata = json_decode(curl_exec($ch), true);

                $_SESSION["username"] = $username;
                $_SESSION["level"] = $myblobdata["blob"]["level"];
                $_SESSION["userid"] = $myblobdata["blob"]["uid"];
                $_SESSION["g2fa"] = $myblobdata["blob"]["2fa-code"];
                $_SESSION["expiry"] = gmdate("Y-m-d H:i:s", $myblobdata["blob"]["expiry"]);

                curl_setopt($ch, CURLOPT_POSTFIELDS, $appblob_contents);
                $appblobdata = json_decode(curl_exec($ch), true);

                $_SESSION["activeapis"] = $appblobdata["blob"]["activeapis"];
                $_SESSION["usercount"] = $appblobdata["blob"]["usercount"];
                $_SESSION["apicount"] = $appblobdata["blob"]["apicount"];
                $_SESSION["appname"] = $appblobdata["blob"]["label"];
                $_SESSION["appdesc"] = $appblobdata["blob"]["description"];
                
                return true;
            break;
            default:
            break;
        }

        curl_close($ch);
    }
    
    public static function Register($username, $password, $token)
    {
        $ch = curl_init(self::$BaseURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $values = [ "action" => "register", "token" => $token, "username" => $username, "password" => $password ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $values);
        $data = json_decode(curl_exec($ch), true);
        curl_close($ch);

        switch ($data["error"])
        {
            case "1":
                return false;
            break;
            case "0":
                return true;
            break;
            default:
            break;
        }
    }
    
    public static function ResetPassword($oldpassword, $newpassword)
    {
        $ch = curl_init(self::$BaseURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $values = [ "action" => "change-pass", "oldpassword" => $oldpassword, "newpassword" => $newpassword, "session" => $_SESSION["session"] ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $values);
        $data = json_decode(curl_exec($ch), true);
        curl_close($ch);

        switch ($data["error"])
        {
            case "1":
                echo $data["message"];
            break;
            case "0":
                echo $data["message"];
            break;
            default:
            break;
        }
    }
    
    public static function ValidateSession()
    {
        $ch = curl_init(self::$BaseURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $values = [ "action" => "validate-session", "session" => $_SESSION["session"] ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $values);
        $data = json_decode(curl_exec($ch), true);
        curl_close($ch);

        switch ($data["error"])
        {
            case "1":
                echo $data["message"];
            break;
            case "0":
                echo $data["message"];
            break;
            default:
            break;
        }
    }
    
    public static function GetUserVar($var)
    {
        $ch = curl_init(self::$BaseURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $values = [ "action" => "myvars", "session" => $_SESSION["session"] ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $values);
        $data = json_decode(curl_exec($ch), true);
        curl_close($ch);

        switch ($data["error"])
        {
            case "1":
                echo "Invalid response!";
            break;
            case "0":
                echo $data["vars"][$var];
            break;
            default:
            break;
        }
    }

    public static function SetUserVar($var, $value)
    {
        $ch = curl_init(self::$BaseURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $values = [ "action" => "set-user-vars", "session" => $_SESSION["session"], "key" => $var, "value" => $value ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $values);
        $data = json_decode(curl_exec($ch), true);
        curl_close($ch);

        switch ($data["error"])
        {
            case "1":
                echo "Invalid response!";
            break;
            case "0":
                echo $data["message"];
            break;
            default:
            break;
        }
    }

    public static function GetAppVar($var)
    {
        $ch = curl_init(self::$BaseURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $values = [ "action" => "get-app-vars", "session" => $_SESSION["session"] ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $values);
        $data = json_decode(curl_exec($ch), true);
        curl_close($ch);

        switch ($data["error"])
        {
            case "1":
                echo "Invalid response!";
            break;
            case "0":
                echo $data["vars"][$var];
            break;
            default:
            break;
        }
    }
    
    public static function ExecuteAPI($id, $data)
    {
        $ch = curl_init(self::$ExecuteURL . $id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $values = [ "data" => $data ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $values);
        $data = json_decode(curl_exec($ch), true);
        curl_close($ch);

        switch ($data["error"])
        {
            case "1":
                echo $data["message"];
            break;
            case "0":
                echo $data["response"];
            break;
            default:
            break;
        }
    }
    
    public static function ExecuteFullAPI($id, $data, $time)
    {
        $ch = curl_init(self::$ExecuteURL . $id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $values = [ "data" => $data, "time" => $time, "session" => $_SESSION["session"] ];
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $values);
        $data = json_decode(curl_exec($ch), true);
        curl_close($ch);

        switch ($data["error"])
        {
            case "1":
                echo $data["message"];
            break;
            case "0":
                echo $data["response"];
            break;
            default:
            break;
        }
    }
    
    public static function ExecuteTimeAPI($id, $data, $time)
    {
        $ch = curl_init(self::$ExecuteURL . $id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $values = [ "data" => $data, "time" => $time, "session" => $_SESSION["session"] ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $values);
        $data = json_decode(curl_exec($ch), true);
        curl_close($ch);

        switch ($data["error"])
        {
            case "1":
                echo $data["message"];
            break;
            case "0":
                echo $data["response"];
            break;
            default:
            break;
        }
    }

    public static function ExecuteAuthAPI($id, $data)
    {
        $ch = curl_init(self::$ExecuteURL . $id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $values = [ "data" => $data, "session" => $_SESSION["session"] ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $values);
        $data = json_decode(curl_exec($ch), true);
        curl_close($ch);

        switch ($data["error"])
        {
            case "1":
                echo $data["message"];
            break;
            case "0":
                echo $data["response"];
            break;
            default:
            break;
        }
    }
    
    public static function GetChat()
    {
        $ch = curl_init(self::$BaseURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $values = [ "action" => "get-app-chat", "session" => $_SESSION["session"] ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $values);
        $data = json_decode(curl_exec($ch), true);
        curl_close($ch);

        switch ($data["error"])
        {
            case "1":
                echo $data["message"];
            break;
            case "0":
                echo json_encode($data["chat"]);
            break;
            default:
            break;
        }
    }
    
    public static function DeleteMessage($messageid)
    {
        $ch = curl_init(self::$BaseURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $values = [ "action" => "del-app-msg", "mid" => $messageid, "session" => $_SESSION["session"] ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $values);
        $data = json_decode(curl_exec($ch), true);
        curl_close($ch);

        switch ($data["error"])
        {
            case "1":
                echo $data["message"];
            break;
            case "0":
                echo $data["message"];
            break;
            default:
            break;
        }
    }
        
    public static function EditMessage($messageid, $newmessage)
    {
        $ch = curl_init(self::$BaseURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $values = [ "action" => "edit-app-msg", "mid" => $messageid, "content" => $newmessage, "session" => $_SESSION["session"] ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $values);
        $data = json_decode(curl_exec($ch), true);
        curl_close($ch);

        switch ($data["error"])
        {
            case "1":
                echo $data["message"];
            break;
            case "0":
                echo $data["message"];
            break;
            default:
            break;
        }
    }
    
    public static function SendMessage($content)
    {
        $ch = curl_init(self::$BaseURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $values = [ "action" => "send-app-msg", "message" => $content, "session" => $_SESSION["session"] ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $values);
        $data = json_decode(curl_exec($ch), true);
        curl_close($ch);

        switch ($data["error"])
        {
            case "1":
                echo $data["message"];
            break;
            case "0":
                echo $data["message"];
            break;
            default:
            break;
        }
    }
}
