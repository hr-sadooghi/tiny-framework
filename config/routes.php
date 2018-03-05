<?php

return [
    'login'               => ['r' => '../resources/views/user/login.php'],
    'check-user'          => ['c' => 'User\\Controller\\UserController', 'a' => 'checkUser'],
    'user/password-reset' => ['c' => 'User\\Controller\\UserController', 'a' => 'passwordReset'],
    'logoff'              => ['c' => 'User\\Controller\\UserController', 'a' => 'logoff'],
];
