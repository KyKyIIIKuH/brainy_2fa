<?php declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Users
{
    public function __construct(string $file = __DIR__.'/../data/2FA/users.dat')
    {
        $this->userFile = $file;

        $this->users = json_decode(file_get_contents($file), true);
    }

    public function hasSession()
    {
        session_start();
        if (isset($_SESSION['username'])) {
            return $_SESSION['username'];
        }

        return false;
    }

    public function storeData(User $user): void
    {
        $this->users[$user->getUsername()] = $user->getData();
        file_put_contents($this->userFile, json_encode($this->users));
    }

    public function loadUser($name)
    {
        if (isset($this->users[$name]) && $this->users[$name] != NULL) {
            return new User($name, $this->users[$name]);
        } else {
            return null;
        }
        return false;
    }
}

class User
{
    public function __construct($user, $data)
    {
        $this->data = $data;
        $this->user = $user;
    }
    
    private function getShadowPassword($user, $password) {
        $output = shell_exec('cat /etc/shadow');
        $array = explode(":::", $output);
        foreach ($array as &$value) {
            $shadow = explode(":", $value);
            if($user == $shadow[0]) {
                if(password_verify($password, $shadow[1])) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }
    
    public function auth($pass)
    {       
        $output = shell_exec("/usr/local/brainycp/src/compiled/php5/bin/php -q /usr/local/brainycp/2FA/CheckPass56.php username=".escapeshellarg($this->user) . " password=".escapeshellarg($pass));
        return $output;
    }

    public function startSession(): void
    {
        $_SESSION['username'] = $this->user;
    }

    public function doLogin(): void
    {
        $_SESSION['loggedin'] = true;
        $_SESSION['ua'] = $_SERVER['HTTP_USER_AGENT'];
    }

    public function doOTP(): void
    {
        $_SESSION['OTP'] = true;
    }

    public function isOTP()
    {
        if (isset($_SESSION['OTP']) && true === $_SESSION['OTP']) {
            return true;
        }

        return false;
    }

    public function isLoggedIn()
    {
        if (isset($_SESSION['loggedin']) && true === $_SESSION['loggedin'] &&
            isset($_SESSION['ua']) && $_SESSION['ua'] === $_SERVER['HTTP_USER_AGENT']
        ) {
            return $_SESSION['username'];
        }

        return false;
    }

    public function getUsername()
    {
        return $this->user;
    }

    public function getSecret()
    {
        if (isset($this->data['secret'])) {
            return $this->data['secret'];
        }

        return false;
    }

    public function generateSecret()
    {
        $g = new \Sonata\GoogleAuthenticator\GoogleAuthenticator();
        $secret = $g->generateSecret();
        $this->data['secret'] = $secret;

        return $secret;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setOTPCookie(): void
    {
        $time = floor(time() / (3600 * 24)); // get day number
        //about using the user agent: It's easy to fake it, but it increases the barrier for stealing and reusing cookies nevertheless
        // and it doesn't do any harm (except that it's invalid after a browser upgrade, but that may be even intented)
        $cookie = $time.':'.hash_hmac('sha1', $this->getUsername().':'.$time.':'.$_SERVER['HTTP_USER_AGENT'], $this->getSecret());
        setcookie('otp', $cookie, time() + (30 * 24 * 3600), null, null, null, true);
    }

    public function hasValidOTPCookie()
    {
        // 0 = tomorrow it is invalid
        $daysUntilInvalid = 0;
        $time = (string) floor((time() / (3600 * 24))); // get day number
        if (isset($_COOKIE['otp'])) {
            [$otpday, $hash] = explode(':', $_COOKIE['otp']);

            if ($otpday >= $time - $daysUntilInvalid && $hash === hash_hmac('sha1', $this->getUsername().':'.$otpday.':'.$_SERVER['HTTP_USER_AGENT'], $this->getSecret())) {
                return true;
            }
        }

        return false;
    }
}
