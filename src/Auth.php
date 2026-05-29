// Sistem pengecekan Session Login
<?php

session_start();

require_once __DIR__ . '/../config/database.php';

class Auth
{
    public static function login($username, $password)
    {
        if (!isset($_SESSION['login_attempt'])) {

            $_SESSION['login_attempt'] = 0;
        }

        if (!isset($_SESSION['blocked_time'])) {

            $_SESSION['blocked_time'] = 0;
        }

        if ($_SESSION['login_attempt'] >= 3) {

            if (time() - $_SESSION['blocked_time'] < 60) {

                die('Login diblokir 1 menit');
            }

            $_SESSION['login_attempt'] = 0;
        }

        $database = new Database();

        $db = $database->connect();

        $query = "
            SELECT *
            FROM users
            WHERE username = ?
            LIMIT 1
        ";

        $stmt = $db->prepare($query);

        $stmt->execute([$username]);

        $user = $stmt->fetch();

        if (
            $user &&
            password_verify(
                $password,
                $user['password_hash']
            )
        ) {

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];

            $_SESSION['username'] = $user['username'];

            $_SESSION['role'] = $user['role'];

            $_SESSION['login_attempt'] = 0;

            return true;
        }

        $_SESSION['login_attempt']++;

        $_SESSION['blocked_time'] = time();

        return false;
    }

    public static function check()
    {
        return isset($_SESSION['user_id']);
    }

    public static function guard()
    {
        if (!self::check()) {

            header("Location: login.php");

            exit;
        }
    }

    public static function role()
    {
        return $_SESSION['role'] ?? null;
    }

    public static function isAdmin()
    {
        return self::role() === 'admin';
    }

    public static function adminOnly()
    {
        if (!self::isAdmin()) {

            die("Akses ditolak");
        }
    }

    public static function logout()
    {
        session_start();
        
        session_unset();

        session_destroy();

        header("Location: login.php");

        exit;
    }
}