<?php
namespace Civi\Micro;

class SecurityContext {
    private static $identity;

    public static function setIdentity(Identity $identity) {
        self::$identity = $identity;
    }

    public static function getIdentity(): Identity {
        return self::$identity;
    }
}