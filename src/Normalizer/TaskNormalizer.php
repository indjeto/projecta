<?php

namespace App\Normalizer;

use App\Entity\Project;
use App\Entity\Status;
use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;

class TaskNormalizer extends ObjectNormalizer
{
    public function __construct(
        protected EntityManagerInterface $em,
        ?ClassMetadataFactoryInterface $classMetadataFactory = null,
        ?NameConverterInterface $nameConverter = null,
        ?PropertyAccessorInterface $propertyAccessor = null,
        ?PropertyTypeExtractorInterface $propertyTypeExtractor = null
    ) {
        parent::__construct($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor);
    }

    /**
     * @inheritDoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === Task::class;
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization(mixed $data, string $format = null)
    {
        return $data instanceof Task;
    }

    /**
     * @inheritDoc
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        /**
         * @var Task $task
         */
        $task = isset($data['id']) ? $this->em->find($type, $data['id']) : new $type;
        if (isset($data['title'])) {
            $task->setTitle($data['title']);
        }
        if (isset($data['description'])) {
            $task->setDescription($data['description']);
        }
        if (isset($data['duration'])) {
            $task->setDuration($data['duration']);
        }
        if (isset($data['status'])) {
            $task->setStatus(Status::from($data['status']));
        }
        if (isset($data['project'])) {
            $project = $this->em->find(Project::class, $data['project']);
            if (!$project) {
                throw new NotFoundHttpException('Project not found: '. $data['project']);
            }
            $task->setProject($project);
        }

        return $task;
    }

    /**
     * @param Task $object
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        $data = [
            'id' => $object->getId(),
            'title' => $object->getTitle(),
            'description' => $object->getDescription(),
            'duration' => $object->getDuration(),
            'status' => $object->getStatus(),
            'project' => $object->getProject()->getId(),
        ];

        return $data;
    }
}