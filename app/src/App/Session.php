<?php
/**
 *
 */
namespace App;

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
        }

        $regenerate && self::regenerate();

        register_shutdown_function('App\Session::close');
    }

    /**
     * Get onetime value
     *
     * @param int $lifetime Remember login for ? seconds
     */
    public static function remember($lifetime = 0)
    {
        $lifetime = $lifetime ?: static::$ttl;
        if ($lifetime) {
            self::setCookie(self::$token, self::get(self::LOGIN), $lifetime);
        }
    }

    /**
     * Get onetime value
     *
     * @return bool
     */
    public static function remembered()
    {
        return self::getCookie(self::$token) != '';
    }

    /**
     *
     * @return bool
     */
    public static function checkRemembered()
    {
        return self::remembered() ? self::login(self::getCookie(self::$token)) : false;
    }

    /**
     * Get onetime value
     *
     * @param int $lifetime Remember login for ? seconds
     */
    public static function forget()
    {
        return self::removeCookie(self::$token);
    }

    /**
     * Get onetime value
     *
     * @param int $lifetime Remember login for ? seconds
     */
    public static function login($user = 'user', $lifetime = 0)
    {
        self::set(self::LOGIN, $user);
        return self::remember($lifetime);
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
     * /
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
        return session_regenerate_id();
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
        $_SESSION = [];
        session_destroy();
        self::close();
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
        return array_key_exists($key, $_SESSION) ? self::doDecode($_SESSION[$key]) : $default;
    }

    /**
     * Real implementation, ready to overwrite
     *
     * Set $_SESSION value
     */
    protected static function doSet($key, $value)
    {
        $_SESSION[$key] = self::doEncode($value);
    }

    /**
     * Real implementation, ready to overwrite
     *
     * Set $_SESSION value
     * /
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

    /**
     * Real implementation, ready to overwrite
     *
     * Set $_SESSION value
     */
    protected static function doEncode($data)
    {
        return json_encode($data);
    }

    /**
     * Real implementation, ready to overwrite
     *
     * Set $_SESSION value
     */
    protected static function doDecode($data)
    {
        return json_decode($data, true);
    }
}
