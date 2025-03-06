<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testCreateTask(): void
    {
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'pending',
            'due_date' => '2025-12-31T23:59:59+00:00'
        ];

        $this->client->request(
            'POST',
            '/api/tasks',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($taskData)
        );

        $this->assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals($taskData['title'], $responseData['title']);
    }

    public function testCreateTaskWithInvalidData(): void
    {
        $taskData = [
            'title' => '', // Empty title should fail validation
            'status' => 'invalid_status' // Invalid status should fail validation
        ];

        $this->client->request(
            'POST',
            '/api/tasks',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($taskData)
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function testListTasks(): void
    {
        $this->client->request('GET', '/api/tasks');

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseData);
    }

    public function testShowTask(): void
    {
        // First create a task
        $taskData = ['title' => 'Test Task'];
        $this->client->request(
            'POST',
            '/api/tasks',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($taskData)
        );
        $task = json_decode($this->client->getResponse()->getContent(), true);

        // Then retrieve it
        $this->client->request('GET', '/api/tasks/' . $task['id']);

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals($task['id'], $responseData['id']);
    }

    public function testUpdateTask(): void
    {
        // First create a task
        $taskData = ['title' => 'Test Task'];
        $this->client->request(
            'POST',
            '/api/tasks',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($taskData)
        );
        $task = json_decode($this->client->getResponse()->getContent(), true);

        // Then update it
        $updateData = [
            'title' => 'Updated Task',
            'status' => 'in_progress'
        ];
        $this->client->request(
            'PUT',
            '/api/tasks/' . $task['id'],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($updateData)
        );

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals($updateData['title'], $responseData['title']);
        $this->assertEquals($updateData['status'], $responseData['status']);
    }

    public function testDeleteTask(): void
    {
        // First create a task
        $taskData = ['title' => 'Test Task'];
        $this->client->request(
            'POST',
            '/api/tasks',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($taskData)
        );
        $task = json_decode($this->client->getResponse()->getContent(), true);

        // Then delete it
        $this->client->request('DELETE', '/api/tasks/' . $task['id']);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());

        // Verify it's gone
        $this->client->request('GET', '/api/tasks/' . $task['id']);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }
}
