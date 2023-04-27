<?php

namespace App\Entity;

use App\Helper\StatusCalculator;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ORM\Index(fields: ["deletedAt"])]
#[ORM\Index(fields: ["status"])]
class Project implements SoftDeletableInterface
{
    use SoftDeleteTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $title = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = '';

    #[ORM\Column(type: Types::SMALLINT, enumType: Status::class)]
    private Status $status = Status::New;

    #[ORM\Column]
    private int $duration = 0;

    #[ORM\Column(length: 255)]
    private ?string $client = '';

    #[ORM\Column(length: 255)]
    private ?string $company = '';

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Task::class)]
    private Collection $tasks;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
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

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function recalcDuration(): self
    {
        $sum = 0;
        foreach ($this->getTasks() as $task) {
            $sum += $task->getDuration();
        }
        $this->duration = $sum;

        $this->recalcStatus();

        return $this;
    }

    public function recalcStatus(): self
    {
        $statuses = array_map(fn(Task $task) => $task->getStatus(), $this->getTasks()->toArray());
        $this->status = StatusCalculator::getOverallStatus(...$statuses);

        return $this;
    }

    public function getClient(): ?string
    {
        return $this->client;
    }

    public function setClient(string $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(string $company): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setProject($this);

            $this->recalcStatus();
            $this->recalcDuration();
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getProject() === $this) {
                $task->setProject(null);
            }

            $this->recalcStatus();
            $this->recalcDuration();
        }

        return $this;
    }

    public function __toString(): string
    {
        return substr($this->title, 0, 20);
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context, $payload): void
    {
        if (empty($this->getClient()) || empty($this->getCompany())) {
            $context->buildViolation('You should populate Client or Company!')
                ->atPath(empty($this->getClient()) ? 'client' : 'company')
                ->addViolation();
        }
    }
}
