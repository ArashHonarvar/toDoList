<?php

namespace App\Entity\Task;

use App\Entity\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Task\TaskRepository")
 * @Serializer\ExclusionPolicy("all")
 * @Hateoas\Relation(
 *     "logs",
 *     href=@Hateoas\Route(
 *          "api_task_logs_list",
 *          parameters={"taskId"= "expr(object.getId())"}
 *     )
 * )
 * @Hateoas\Relation(
 *     "self",
 *     href=@Hateoas\Route(
 *          "api_task_show",
 *          parameters={"taskId"= "expr(object.getId())"}
 *     )
 * )
 * @Hateoas\Relation(
 *     "delete",
 *     href=@Hateoas\Route(
 *          "api_task_delete",
 *          parameters={"taskId"= "expr(object.getId())"}
 *     )
 * )
 */
class Task
{

    const STATUS_READY = 'ready';
    const STATUS_DOING = 'doing';
    const STATUS_DONE = 'done';
    const STATUS_EXPIRED = 'expired';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Expose()
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Please enter a title")
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose()
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Serializer\Expose()
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Task\TaskLog" , mappedBy="task", cascade={"persist","remove"})
     */
    private $logs;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @Serializer\Expose()
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @Serializer\Expose()
     */
    private $createdBy;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status = self::STATUS_READY;

    /**
     * @Assert\NotBlank(message="Please enter a dueDate")
     * @ORM\Column(type="datetime")
     * @Serializer\Expose()
     */
    private $dueDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDeleted = false;

    /**
     * @ORM\Column(type="datetime" , options={"default": "CURRENT_TIMESTAMP"})
     * @Gedmo\Timestampable(on="update")
     * @Serializer\Expose()
     */
    private $updatedAt;

    /**
     * @return string
     * @Serializer\VirtualProperty(name="status")
     * @Serializer\SerializedName("status")
     */
    public function showStatus()
    {
        $status = $this->getStatus();
        if ($this->dueDate < new \DateTime()) {
            return self::STATUS_EXPIRED;
        } else {
            return $status;
        }
    }

    //    ***************** Auto Generated Functions **************
    public function __construct()
    {
        $this->logs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDueDate(): ?\DateTimeInterface
    {
        return $this->dueDate;
    }

    public function setDueDate(\DateTimeInterface $dueDate): self
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    public function getIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection|TaskLog[]
     */
    public function getLogs(): Collection
    {
        return $this->logs;
    }

    public function addLog(TaskLog $log): self
    {
        if (!$this->logs->contains($log)) {
            $this->logs[] = $log;
            $log->setTask($this);
        }

        return $this;
    }

    public function removeLog(TaskLog $log): self
    {
        if ($this->logs->contains($log)) {
            $this->logs->removeElement($log);
            // set the owning side to null (unless already changed)
            if ($log->getTask() === $this) {
                $log->setTask(null);
            }
        }

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

//    ***************** Auto Generated Functions **************


}
