<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[Gedmo\SoftDeleteable(hardDelete: false)]
class Task implements SoftDeletableInterface
{
    use SoftDeleteTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $title ='';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = '';

    #[ORM\Column(type: Types::SMALLINT, enumType: Status::class)]
    private Status $status = Status::New;

    #[ORM\Column]
    private int $duration = 0;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn]
    private ?Project $project = null;

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

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        $this->project?->recalcDuration();

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        if ($this->project === $project) {
            return $this;
        }

        $oldProject = $this->project;

        $this->project = $project;

        $oldProject?->recalcDuration();

        $this->project?->recalcDuration();

        return $this;
    }
}
