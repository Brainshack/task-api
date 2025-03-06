<?php

namespace App\Controller;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api')]
class TaskController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface     $validator
    )
    {
    }

    #[Route('/tasks', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $task = new Task();
        $task->setTitle($data['title']);
        if (isset($data['description'])) {
            $task->setDescription($data['description']);
        }
        if (isset($data['status'])) {
            $task->setStatus($data['status']);
        }
        if (isset($data['due_date'])) {
            $task->setDueDate(new \DateTime($data['due_date']));
        }

        $errors = $this->validator->validate($task);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string)$errors], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $this->json($task, Response::HTTP_CREATED);
    }

    #[Route('/tasks', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $repository = $this->entityManager->getRepository(Task::class);

        // Optional filtering by status
        $status = $request->query->get('status');
        if ($status) {
            $tasks = $repository->findBy(['status' => $status]);
        } else {
            $tasks = $repository->findAll();
        }

        return $this->json($tasks, Response::HTTP_OK);
    }

    #[Route('/tasks/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $task = $this->entityManager->getRepository(Task::class)->find($id);

        if (!$task) {
            return $this->json(['error' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($task, Response::HTTP_OK);
    }

    #[Route('/tasks/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $task = $this->entityManager->getRepository(Task::class)->find($id);

        if (!$task) {
            return $this->json(['error' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['title'])) {
            $task->setTitle($data['title']);
        }
        if (isset($data['description'])) {
            $task->setDescription($data['description']);
        }
        if (isset($data['status'])) {
            $task->setStatus($data['status']);
        }
        if (isset($data['due_date'])) {
            $task->setDueDate(new \DateTime($data['due_date']));
        }

        $errors = $this->validator->validate($task);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string)$errors], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        return $this->json($task, Response::HTTP_OK);
    }

    #[Route('/tasks/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $task = $this->entityManager->getRepository(Task::class)->find($id);

        if (!$task) {
            return $this->json(['error' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($task);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
