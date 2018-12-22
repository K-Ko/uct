<?php
/**
 *
 */
namespace Core;

/**
 *
 */
class Session
{
    /**
     *
     */
    const LOGIN = '_session_login';

    /**
     *
     */
    const FLASH = '_session_flash';

    /**
     *
     */
    public static $sessionName = 'PHPSESSID';

    /**
     *
     */
    public static $tokenName = 'token';

    /**
     *
     */
    public static $ttl = 0;

    /**
     * Session's initializer
     * This function call session_start() if not initialized
     *
     * @param bool $regenerate true: call self::regenerateId();
     */
    public static function start($regenerate = true)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(self::$sessionName);
            session_start();

            self::$token = sha1(sha1(__FILE__.$_SERVER['HTTP_HOST'].$_SERVER['HTTP_USER_AGENT']));
            self::$flash = self::get(self::FLASH) ?: array();
            self::set(self::FLASH, array());
        }

        $regenerate && self::regenerate();

        register_shutdown_function('Core\Session::close');
    }

    /**
     * Get onetime value
     *
     * @param int $lifetime Remember login for ? seconds
     */
    public static function remember($lifetime)
    {
        $lifetime = $lifetime ?: static::$ttl;
        if ($lifetime > 0) {
            self::setCookie(self::$tokenName, self::$token, $lifetime);
        }
    }

    /**
     * Get onetime value
     *
     * @return bool
     */
    public static function remembered()
    {
        return (self::getCookie(self::$tokenName) === self::$token);
    }

    /**
     *
     * @return bool
     */
    public static function checkRemembered($user = 'user', $lifetime = 0)
    {
        return self::remembered() ? self::login($user, $lifetime) : false;
    }

    /**
     * Get onetime value
     *
     * @param int $lifetime Remember login for ? seconds
     */
    public static function forget()
    {
        return self::removeCookie(self::$tokenName);
    }

    /**
     * Get onetime value
     *
     * @param int $lifetime Remember login for ? seconds
     */
    public static function login($user = 'user', $lifetime = 0)
    {
        static::remember($lifetime);
        return self::set(self::LOGIN, $user);
    }

    /**
     * Get onetime value
     *
     * @param int $lifetime Remember login for ? seconds
     */
    public static function loggedIn($user = 'user')
    {
        $loggedIn = (self::get(self::LOGIN) === $user);
        if (!$loggedIn) {
            self::forget();
        }
        return $loggedIn;
    }

    /**
     * Get onetime value
     *
     * @param int $lifetime Remember login for ? seconds
     */
    public static function logout()
    {
        self::delete(self::LOGIN);
        return self::forget();
    }

    /**
     * Get onetime value
     *
     * @param mixed $key key name
     * @return mixed value or null if not set
     */
    public static function flash($data = null)
    {
        if (is_null($data)) {
            return self::$flash;
        } else {
            self::$flash[] = $data;
            self::add(self::FLASH, $data);
        }
    }

    /**
     *
     */
    public static function setCookie($name, $data = null, $ttl = 0)
    {
        $_COOKIE[$name] = $data;
        $p = session_get_cookie_params();
        return setcookie(
            $name,
            $data,
            time()+$ttl,
            $p['path'],
            $p['domain'],
            $p['secure'],
            $p['httponly']
        );
    }

    /**
     *
     */
    public static function getCookie($name, $default = null)
    {
        return array_key_exists($name, $_COOKIE) ? $_COOKIE[$name] : $default;
    }

    /**
     *
     */
    public static function removeCookie($name)
    {
        $p = session_get_cookie_params();
        setcookie(
            $name,
            '',
            time()-4200,
            $p['path'],
            $p['domain'],
            $p['secure'],
            $p['httponly']
        );
        unset($_COOKIE[$name]);
    }

    /**
     * Get $_SESSION value
     *
     * @return mixed value or null if not set
     */
    public static function get($key, $default = null)
    {
        return self::doGet($key, $default);
    }

    /**
     * Get $_SESSION value and remove
     *
     * @return mixed value or null if not set
     */
    public static function take($key, $default = null)
    {
        $value = self::get($key, $default);
        self::delete($key);
        return $value;
    }

    /**
     * Set $_SESSION value
     */
    public static function set($key, $value)
    {
        return self::doSet($key, $value);
    }

    /**
     * Set $_SESSION value
     */
    public static function add($key, $value)
    {
        return self::doAdd($key, $value);
    }

    /**
     * Set $_SESSION value
     */
    public static function delete($key)
    {
        return self::doDelete($key);
    }

    /**
     * Alias session_regenerate_id($delete_old)
     */
    public static function regenerate()
    {
        return session_regenerate_id(true);
    }

    /**
     * Write Session data
     */
    public static function close()
    {
        return session_write_close();
    }

    /**
     * Drop session
     */
    public static function destroy()
    {
        $_SESSION = array();
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time()-42000,
                $p['path'],
                $p['domain'],
                $p['secure'],
                $p['httponly']
            );
        }
        self::forget();
        session_destroy();
        self::close();
        self::start();
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected static $token;

    /**
     * Real implementation, ready to overwrite
     *
     * @return mixed value or null if not set
     */
    protected static function doGet($key, $default = null)
    {
        return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
    }

    /**
     * Real implementation, ready to overwrite
     *
     * Set $_SESSION value
     */
    protected static function doSet($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Real implementation, ready to overwrite
     *
     * Set $_SESSION value
     */
    protected static function doAdd($key, $value)
    {
        if (is_array($_SESSION[$key])) {
            $_SESSION[$key][] = $value;
        } else {
            $_SESSION[$key] .= $value;
        }
    }

    /**
     * Real implementation, ready to overwrite
     *
     * Set $_SESSION value
     */
    protected static function doDelete($key)
    {
        unset($_SESSION[$key]);
    }

    // -----------------------------------------------------------------------
    // PRIVATE
    // -----------------------------------------------------------------------

    /**
     *
     */
    private static $flash = [];
}
