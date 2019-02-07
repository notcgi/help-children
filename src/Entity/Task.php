<?php

// src/Entity/Task.php
namespace App\Entity;

class Task
{
    protected $email;
    protected $password;

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getPassword()
    {
        return $this->email;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }
}
