<?php

namespace App\Entity\User;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\User\UserRepository")
 * @UniqueEntity(
 *     fields={"username"},
 *     message="This username was taken before, choose another one."
 * )
 * @UniqueEntity(
 *     fields={"email"},
 *     message="This email was taken before, choose another one."
 * )
 * @Serializer\ExclusionPolicy("all")
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
     * @Assert\NotBlank(message="Please enter a username")
     * @ORM\Column(type="string", unique=true , length=191)
     * @Serializer\Expose()
     */
    private $username;

    /**
     * @Assert\NotBlank(message="Please enter an email address")
     * @ORM\Column(type="string", unique=true , length=191)
     * @Serializer\Expose()
     */
    private $email;

    /**
     * @Assert\NotBlank(message="Please enter a password")
     * @ORM\Column(type="string" , length=191)
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User\ApiToken" , mappedBy="createdBy")
     */
    private $tokens;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @Serializer\Expose()
     */
    private $createdAt;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    public function __construct()
    {
        $this->tokens = new ArrayCollection();
    }

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

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
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

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return Collection|ApiToken[]
     */
    public function getTokens(): Collection
    {
        return $this->tokens;
    }

    public function addToken(ApiToken $token): self
    {
        if (!$this->tokens->contains($token)) {
            $this->tokens[] = $token;
            $token->setCreatedBy($this);
        }

        return $this;
    }

    public function removeToken(ApiToken $token): self
    {
        if ($this->tokens->contains($token)) {
            $this->tokens->removeElement($token);
            // set the owning side to null (unless already changed)
            if ($token->getCreatedBy() === $this) {
                $token->setCreatedBy(null);
            }
        }

        return $this;
    }


}
