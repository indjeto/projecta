<?php

namespace App\Controller\Api;

use App\Entity\Project;
use App\Exception\ValidationViolationException;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/projects')]
class ProjectController extends AbstractFOSRestController
{
    #[Route('/', name: 'api_project_index', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $projectRepository->getPaginated($request->query->getInt('page', 1));
        $view = $this->view([
            'status' => 0,
            'total' => $pagination->getTotalItemCount(),
            'data' => $pagination->getItems(),
        ], 200);

        return $this->handleView($view);
    }

    #[Route('/', name: 'api_project_new', methods: ['POST'])]
    public function new(Request $request, ProjectRepository $projectRepository, SerializerInterface $serializer, ValidatorInterface $validator): Response
    {
        $project = $serializer->deserialize($request->getContent(), Project::class, 'json');
        $violations = $validator->validate($project);
        if (0 !== count($violations)) {
            throw new ValidationViolationException($violations);
        }

        $projectRepository->save($project);

        $view = $this->view(['status' => 0, 'data' => $project], 200);
        return $this->handleView($view);
    }

    #[Route('/{id}', name: 'api_project_show', methods: ['GET'])]
    public function show(Project $project): Response
    {
        $view = $this->view(['status' => 0, 'data' => $project], 200);
        return $this->handleView($view);
    }

    #[Route('/{id}', name: 'api_project_edit', methods: ['PUT'])]
    public function edit(Request $request, Project $project, ProjectRepository $projectRepository, SerializerInterface $serializer, ValidatorInterface $validator): Response
    {
        $project = $serializer->deserialize($request->getContent(), Project::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $project]);
        //dd($project);
        $violations = $validator->validate($project);
        if (0 !== count($violations)) {
            throw new ValidationViolationException($violations);
        }

        $projectRepository->save($project);

        $view = $this->view(['status' => 0, 'data' => $project], 200);
        return $this->handleView($view);
    }

    #[Route('/{id}', name: 'api_project_delete', methods: ['DELETE'])]
    public function delete(Request $request, Project $project, ProjectRepository $projectRepository): Response
    {
        $project->setDeleted(true);
        $projectRepository->save($project, true);

        $view = $this->view(['status' => 0], 200);

        return $this->handleView($view);
    }
}
