<?php

namespace App\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;


/**
 * @Serializer\ExclusionPolicy("all")
 * @ORM\Entity(repositoryClass="App\Repository\User\ApiTokenRepository")
 */
class ApiToken
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100 , unique=true)
     * @Serializer\Expose()
     */
    private $accessToken;

    /**
     * @ORM\Column(type="string", length=100 , unique=true)
     * @Serializer\Expose()
     */
    private $refreshToken;

    /**
     * @ORM\Column(type="datetime")
     * @Serializer\Expose()
     */
    private $expiredAt;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @Serializer\Expose()
     */
    private $createdAt;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User" , inversedBy="tokens")
     * @Serializer\Expose()
     */
    private $createdBy;

    public function __construct()
    {
        $this->accessToken = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->refreshToken = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
    }

    /**
     * @return boolean
     * @Serializer\VirtualProperty()
     */
    public function isExpired(): bool
    {
        return $this->getExpiredAt() <= new \DateTime();
    }


    //    ***************** Auto Generated Functions **************

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(string $refreshToken): self
    {
        $this->refreshToken = $refreshToken;

        return $this;
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

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getExpiredAt(): ?\DateTimeInterface
    {
        return $this->expiredAt;
    }

    public function setExpiredAt(\DateTimeInterface $expiredAt): self
    {
        $this->expiredAt = $expiredAt;

        return $this;
    }

}
