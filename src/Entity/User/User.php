<?php

namespace App\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\User\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true , length=191)
     */
    private $username;

    /**
     * @ORM\Column(type="string", unique=true , length=191)
     */
    private $email;

    /**
     * @ORM\Column(type="string" , length=191)
     */
    private $password;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User\ApiToken" , inversedBy="createdBy")
     */
    private $tokens;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    //    ***************** Auto Generated Functions **************

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }
    public function setUsername($username)
    {
        $this->username = $username;
    }

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
        return $this->password;
    }
    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function getSalt()
    {
        return;
    }

    public function eraseCredentials()
    {
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getTokens(): ?ApiToken
    {
        return $this->tokens;
    }

    public function setTokens(?ApiToken $tokens): self
    {
        $this->tokens = $tokens;

        return $this;
    }
}
