<?php

declare(strict_types=1);

namespace ElastPro\Auth;

class HTTPAuth
{
    /**
     * Stored login credentials
     * @var array $auth_config
     */
    protected $auth_config;

    /**
     * Default login credentials
     * @var array $auth_default
     */
    private $auth_default = array(
        '0' => array(
        'admin_user' => 'admin',
        'admin_pass' => '$2y$10$.I8ji57GDlWHu6aWklGWZuTe57g980zelhV9VlYFyQfZ.eLd4b2/2',
        'purview' => RASPI_PURVIEW_ALL
        ));

    // Constructor
    public function __construct()
    {
        $this->auth_config = $this->getAuthConfig();
    }

    /*
     * Determines if user is logged in AND still exists in auth config.
     * If the user was deleted (e.g. via Authentication page), their session
     * is destroyed so they are immediately logged out.
     * return boolean
     */
    public function isLogged()
    {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        // Verify the user still exists in the auth file.
        // A deleted user's session must be invalidated immediately.
        if (!$this->userExists($_SESSION['user_id'])) {
            $this->logoutSession();
            return false;
        }

        return true;
    }

    /**
     * Check if a username still exists in the auth config file.
     */
    public function userExists(string $user): bool
    {
        $config = $this->getAuthConfig();
        foreach ($config as $entry) {
            if (is_array($entry) && isset($entry['admin_user']) && $entry['admin_user'] === $user) {
                return true;
            }
        }
        return false;
    }

    /**
     * Destroy the current session without redirect (used internally).
     */
    private function logoutSession(): void
    {
        session_regenerate_id(true);
        session_unset();
        session_destroy();
    }

    /*
     * Authenticate a user using HTTP basic auth
     */
    public function authenticate()
    {
        if (!$this->isLogged()) {
            $redirectUrl = $_SERVER['REQUEST_URI'];
            if (strpos($redirectUrl, '/login') === false) {
                header('Location: /login?action=' . urlencode($redirectUrl));
                exit();
            }
        }
    }

    /*
     * Attempt to login a user with supplied credentials
     * @var string $user
     * @var string $pass
     * return boolean
     */
    public function login(string $user, string $pass)
    {
        if ($this->isValidCredentials($user, $pass)) {
            $_SESSION['user_id'] = $user;
            return true;
        }
        return false;
    }

    /*
     * Logs out the administrative user
     */
    public function logout(): void
    {
        session_regenerate_id(true); // generate a new session id
        session_unset(); // unset all session variables
        session_destroy(); // destroy the session
        $redirectUrl = $_SERVER['REQUEST_URI'];
        if (strpos($redirectUrl, '/login') === false) {
            header('Location: /login?action=' . urlencode($redirectUrl));
            exit();
        }
    }

    /*
     * Gets the current authentication config
     * return array $config
     */
    public function getAuthConfig()
    {
        $config = $this->auth_default;

        if (file_exists(RASPI_ADMIN_DETAILS)) {
            if ($auth_details = fopen(RASPI_ADMIN_DETAILS, 'r')) {
                $i = 0;
                while (($line = fgets($auth_details)) !== false) {
                    // echo $i.':'.$line.PHP_EOL;
                    $result = explode(':', $line);
                    if (count($result) == 3) {
                        $config[$i]['admin_user'] = $result[0];
                        $config[$i]['admin_pass'] = $result[1];
                        $config[$i]['purview'] = $result[2];
                    }
                    
                    $i++;
                    unset($line);
                    unset($result);
                }
                // $config['admin_user'] = trim(fgets($auth_details));
                // $config['admin_pass'] = trim(fgets($auth_details));
                fclose($auth_details);
            }
        }

        return $config;
    }

    /*
     * Validates a set of credentials
     * @var string $user
     * @var string $pass
     * return boolean
     */
    protected function isValidCredentials(string $user, string $pass)
    {
        // return $this->validateUser($user) && $this->validatePassword($pass);
        $config = $this->getAuthConfig();
        foreach ($config as $key => $value) {
            if (is_array($value)) {
                if ($value['admin_user'] == $user) {
                    $validated = ($user == $value['admin_user']) && password_verify($pass, $value['admin_pass']);
                }  
            } else {
                $validated = ($user == $config['admin_user']) && password_verify($pass, $config['admin_pass']);
            }
        }

        return $validated;
    }

    /**
     * Validates a user
     *
     * @param string $user
     */
    protected function validateUser(string $user)
    {
        return $user == $this->auth_config['admin_user'];
    }

    /**
     * Validates a password
     *
     * @param string $pass
     */
    protected function validatePassword(string $pass)
    {
        return password_verify($pass, $this->auth_config['admin_pass']);
    }

}
