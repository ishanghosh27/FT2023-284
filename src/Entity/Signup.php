<?php

namespace App\Entity;

use App\Repository\SignupRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SignupRepository::class)]
class Signup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $FirstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $LastName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $UserName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phoneNumber = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->FirstName;
    }

    public function setFirstName(?string $FirstName): self
    {
        $this->FirstName = $FirstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->LastName;
    }

    public function setLastName(?string $LastName): self
    {
        $this->LastName = $LastName;

        return $this;
    }

    public function getUserName(): ?string
    {
        return $this->UserName;
    }

    public function setUserName(?string $UserName): self
    {
        $this->UserName = $UserName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(?string $Email): self
    {
        $this->Email = $Email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->Password;
    }

    public function setPassword(?string $Password): self
    {
        $this->Password = $Password;

        return $this;
    }

    /**
     * This method stores the input details from all the signup form field in
     * signup table in the database.
     *
     *   @param string $fName
     *     Stores the first name.
     *   @param string $lName
     *     Stores the last name.
     *   @param string $uName
     *     Stores the username.
     *   @param string $phoneNo
     *     Stores the phone number.
     *   @param string $email
     *     Stores the email address.
     *   @param string $pwd
     *     Stores the password.
     */
    public function storeNewUser(string $fName, string $lName, string $uName, string $phoneNo, string $email, string $pwd) {
        $this->setFirstName($fName);
        $this->setLastName($lName);
        $this->setUserName($uName);
        $this->setPhoneNumber($phoneNo);
        $this->setEmail($email);
        $this->setPassword($pwd);
    }
}
