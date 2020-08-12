<?php

namespace App\Service;

use Exception;

final class AuthService
{
    private const LOGIN_FAILED_MESSAGE = 'Username oder Password exitiert nicht oder sind nicht korrekt';
    private const LOGINS = [
        'foo' => 'bar',
    ];

    public function login(): void
    {
        $this->showLogin();
        $this->checkCredentials();
    }

    private function showLogin(): void
    {
        if (isset($_SERVER['PHP_AUTH_USER'])) {
            return;
        }

        header('WWW-Authenticate: Basic realm="My Realm"');
        header('HTTP/1.0 401 Unauthorized');

        echo 'Text, der gesendet wird, falls der Benutzer auf Abbrechen drückt';
        exit;
    }

    private function checkCredentials(): void
    {
        $inputUsername = $_SERVER['PHP_AUTH_USER'];
        $inputPassword = $_SERVER['PHP_AUTH_PW'];

        if (!isset(self::LOGINS[$inputUsername])) {
            throw new Exception(self::LOGIN_FAILED_MESSAGE, 1);
        }

        $userPassword = self::LOGINS[$inputUsername];

        if ($userPassword !== $inputPassword) {
            throw new Exception(self::LOGIN_FAILED_MESSAGE, 2);
        }
    }
}