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

class ProjectNormalizer extends ObjectNormalizer
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
        return $type === Project::class;
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization(mixed $data, string $format = null)
    {
        return $data instanceof Project;
    }

    /**
     * @inheritDoc
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        /**
         * @var Project $project
         */
        $project = isset($data['id']) ? $this->em->find($type, $data['id']) : new $type;
        if (isset($data['title'])) {
            $project->setTitle($data['title']);
        }
        if (isset($data['description'])) {
            $project->setDescription($data['description']);
        }
        if (isset($data['client'])) {
            $project->setClient($data['client']);
        }
        if (isset($data['company'])) {
            $project->setCompany($data['company']);
        }

        return $project;
    }

    /**
     * @param Project $object
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        $data = [
            'id' => $object->getId(),
            'title' => $object->getTitle(),
            'description' => $object->getDescription(),
            'duration' => $object->getDuration(),
            'status' => $object->getStatus(),
            'client' => $object->getClient(),
            'company' => $object->getCompany(),
        ];

        return $data;
    }
}