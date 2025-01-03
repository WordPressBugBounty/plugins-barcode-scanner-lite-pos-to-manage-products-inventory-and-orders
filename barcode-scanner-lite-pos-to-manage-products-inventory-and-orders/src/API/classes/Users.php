<?php

namespace UkrSolution\BarcodeScanner\API\classes;

class Users
{
    private static $userId = 0;
    private static $userRole = '';

    public static function setUserId($userId)
    {
        self::$userId = $userId;
    }

    public static function userId()
    {
        return self::$userId ? self::$userId : get_current_user_id();
    }

    public static function setUserRole($userRole)
    {
        self::$userRole = $userRole;
    }

    public static function userRole()
    {
        $role = self::userId() ? Users::getUserRole(self::userId()) : '';
        return self::$userRole ? self::$userRole : $role;
    }

    public static function getUserId($request)
    {
        global $wpdb;

        $userId = get_current_user_id();
        $token = $request->get_param("token");

        if (!$userId && $token) {

            try {
                if (preg_match("/^([0-9]+)/", @base64_decode($token), $m)) {
                    if ($m && count($m) > 0 && is_numeric($m[0])) {
                        $userId = $m[0];
                    }
                } else {
                    $meta = $wpdb->get_row("SELECT * FROM {$wpdb->usermeta} WHERE meta_key = 'barcode_scanner_app_otp' AND meta_value = '{$token}';");
                    $userId = $meta ? $meta->user_id : $userId;
                }
            } catch (\Throwable $th) {
            }
        }

        return $userId;
    }

    public static function getUserRole($userId)
    {
        if ($userId) {
            $user = get_user_by('ID', $userId);
            $roles = $user && isset($user->roles) ? (array)$user->roles : array();
            return $roles && count($roles) ? $roles[0] : '';
        }

        return '';
    }

    public static function getUToken($userId, $platform)
    {
        try {
            $token = get_user_meta($userId, 'barcode_scanner_web_otp', true);

            if ($token) {
                return $token;
            }
            else {
                $token = md5(time());
                update_user_meta($userId, 'barcode_scanner_web_otp', $token);
                return $token;
            }
        } catch (\Throwable $th) {
            return "";
        }
    }

    public static function getUsersAppUsesTimeData()
    {
        global $wpdb;

        return $wpdb->get_results("SELECT * FROM {$wpdb->usermeta} AS UM WHERE UM.meta_key = 'barcode_scanner_app_last_used';");
    }

    public static function updateAppUsesTime($userId)
    {
        try {
            if ($userId) {
                update_user_meta($userId, 'barcode_scanner_app_last_used', time());
            }
        } catch (\Throwable $th) {
            return "";
        }
    }

    public static function getNewUserRoles()
    {
        global $wp_roles;

        if ($wp_roles && $wp_roles->roles) {
            $roles = $wp_roles->roles;
            $filtered_roles = array();

            foreach ($roles as $role_name => $role) {
                if (!isset($role['capabilities'])) {
                    continue;
                }

                $has_higher_level = false;
                foreach ($role['capabilities'] as $cap => $value) {
                    if (preg_match('/^level_([1-9]|10)$/', $cap) && $value) {
                        $has_higher_level = true;
                        break;
                    }
                }

                if (!$has_higher_level) {
                    $filtered_roles[$role_name] = array('name' => $role['name']);
                }
            }

            $filtered_roles = apply_filters('barcode_scanner_new_user_roles', $filtered_roles);

            return $filtered_roles;
        }

        return array();
    }
}
