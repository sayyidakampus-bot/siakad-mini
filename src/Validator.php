<?php

class Validator
{
    // cek field wajib diisi
    public static function required($value): bool
    {
        return isset($value) && trim($value) !== '';
    }

    // validasi email
    public static function email($email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    // panjang minimal
    public static function minLength($value, $min): bool
    {
        return strlen(trim($value)) >= $min;
    }

    // panjang maksimal
    public static function maxLength($value, $max): bool
    {
        return strlen(trim($value)) <= $max;
    }

    // validasi angka integer
    public static function integer($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    // range angka (misalnya SKS 1-6)
    public static function between($value, $min, $max): bool
    {
        if (!self::integer($value)) return false;

        return $value >= $min && $value <= $max;
    }

    // sanitize output (biar aman XSS)
    public static function sanitize($value): string
    {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }

    // validasi NIDN (contoh sederhana: harus 10 digit angka)
    public static function nidn($value): bool
    {
        return preg_match('/^[0-9]{10}$/', $value);
    }

    // validasi file upload (MIME image saja)
    public static function imageMime($tmpFile): bool
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $tmpFile);
        finfo_close($finfo);

        $allowed = ['image/jpeg', 'image/png', 'image/webp'];

        return in_array($mime, $allowed);
    }
}