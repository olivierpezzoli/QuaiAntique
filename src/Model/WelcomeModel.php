<?php

namespace App\Model;

class WelcomeModel
{



    private ?string $email = null;

    private ?string $password = null;

    private ?string $lastName = null;

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     */
    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }



    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }


    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }





}