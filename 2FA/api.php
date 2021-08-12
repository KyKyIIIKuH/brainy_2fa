<?php declare(strict_types=1);
header('Content-Type: application/json');

ob_start(); //i'm too lazy to check when is sent what ;)
//set session cookie to be read only via http and not by JavaScript
ini_set('session.cookie_httponly', '1');

// Подключаем composer
require_once __DIR__.'/composer/vendor/autoload.php';
include_once __DIR__.'/Users.php';

//set this to false, if you don't want the token prefilled
$debug = false;

$data = array();
foreach ($argv as &$value) {
    $array_params = explode("=", $value);
    if(isset($array_params[1]) && empty($array_params[1]) || !isset($array_params[1]) && empty($array_params[1])) continue;
    $data[$array_params[0]] = (($array_params[1]));
}

$users = new Users();

if(isset($data["action"]) && !empty($data["action"]) && $data["action"] == "check_auth") {
    $user = $users->loadUser($data["username"]);
    
    // Если пользователь не включил 2FA
    if($user == NULL) {
        $response = array();
        $response["auth"] = NULL;
        echo json_encode( $response );
        die();
    }
    
    if($user == true) {
        if($user->auth($data['password'])) {
            $response = array();
            $response["auth"] = true;
            echo json_encode( $response );
        }
         else {
            $response = array();
            $response["auth"] = false;
            echo json_encode( $response );
        }
    }
    die();
}

if(isset($data["action"]) && !empty($data["action"]) && $data["action"] == "check_user") {
    $user = $users->loadUser($data["username"]);
    if($user) {
        $getSecret = $user->getSecret();
        if(isset($getSecret) && empty($getSecret) && !isset($getSecret) && empty($getSecret)) {
            $response = array();
            $response["user"] = false;
            echo json_encode( $response );
            die();
        }
    }
    if($user && $user->auth($data['password'])) {
        $response = array();
        $response["user"] = true;
        echo json_encode( $response );
    } else {
        $response = array();
        $response["user"] = false;
        echo json_encode( $response );
    }
    die();
}

//check if the user has a session, if not, show the login screen
if (isset($data["username"]) && !empty($data["username"])) {
    //load the user data from the json storage.
    $user = $users->loadUser($data["username"]);

    if ($user) {
        //try to authenticate the password and start the session if it's correct.
        if ($user->auth($data['password'])) {
            $user->startSession();
            //check if the user has a valid OTP cookie, so we don't have to
            // ask for the current token and can directly log in
            if ($user->hasValidOTPCookie()) {
                $user->doLogin();
            }
        }
        $user->doLogin();
        $user->doOTP();
    }

    //if the user is in the OTP phase and submit the OTP.
        if ($user->isOTP() && isset($data['otp'])) {
            $g = new \Google\Authenticator\GoogleAuthenticator();

            // check if the submitted token is the right one and log in
            if ($g->checkCode($user->getSecret(), $data['otp'])) {
                // do log-in the user
                $user->doLogin();
                $response = array();
                $response["error"] = false;
                $response["messages"] = "login ok";
                echo json_encode( $response );
            }
            //if the OTP is wrong, destroy the session and tell the user to try again
            else {
                //session_destroy();
                $response = array();
                $response["error"] = true;
                $response["messages"] = "login error";
                echo json_encode( $response );
            }
        }
        // if the user is neither logged in nor in the OTP phase, show the login form
        else {
            //session_destroy();
            $response = array();
            $response["error"] = true;
            $response["messages"] = "no login";
            echo json_encode( $response );
        }
    die();
} else {
    $response = array();
    $response["error"] = true;
    $response["messages"] = "no params";
    echo json_encode( $response );
}

?>