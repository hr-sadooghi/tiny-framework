<?php

class User
{
    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        return array_key_exists('user', $_SESSION);
    }

    /**
     * Return Current User Id
     *
     * @return int
     */
    public function getId()
    {
        return (int)$_SESSION['user']['id'];
    }
}