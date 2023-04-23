<?php

namespace App\Controller\Api;

use App\Entity\Task;
use App\Exception\ValidationViolationException;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/tasks')]
class TaskController extends AbstractFOSRestController
{
    public function __construct(
        protected SerializerInterface $serializer
    ) {
    }

    #[Route('/', name: 'api_task_index', methods: ['GET'])]
    public function index(TaskRepository $taskRepository, Request $request, PaginatorInterface $paginator, SerializerInterface $serializer): Response
    {
        $pagination = $taskRepository->getPaginated($request->query->getInt('page', 1));

        return $this->json([
            'status' => 0,
            'total' => $pagination->getTotalItemCount(),
            'data' => $pagination->getItems(),
        ], 200);
    }

    #[Route('/', name: 'api_task_new', methods: ['POST'])]
    public function new(Request $request, TaskRepository $taskRepository, ValidatorInterface $validator): Response
    {
        $task = $this->serializer->deserialize($request->getContent(), Task::class, 'json');

        $violations = $validator->validate($task);
        if (0 !== count($violations)) {
            throw new ValidationViolationException($violations);
        }

        $taskRepository->save($task, true);

        return $this->json(['status' => 0, 'data' => $task], 200);
    }

    #[Route('/{id}', name: 'api_task_show', methods: ['GET'])]
    public function show(Task $task): Response
    {
        return $this->json(['status' => 0, 'data' => $task], 200);
    }

    #[Route('/{id}', name: 'api_task_edit', methods: ['PUT'])]
    public function edit(Request $request, Task $task, TaskRepository $taskRepository, ValidatorInterface $validator): Response
    {
        $task = $this->serializer->deserialize($request->getContent(), Task::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $task]);
        //dd($task);
        $violations = $validator->validate($task);
        if (0 !== count($violations)) {
            throw new ValidationViolationException($violations);
        }

        $taskRepository->save($task, true);

        return $this->json(['status' => 0, 'data' => $task], 200);
    }

    #[Route('/{id}', name: 'api_task_delete', methods: ['DELETE'])]
    public function delete(Request $request, Task $task, TaskRepository $taskRepository): Response
    {
        $taskRepository->remove($task, true);

        return $this->json(['status' => 0], 200);
    }
}
