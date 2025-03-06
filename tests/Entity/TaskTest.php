<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    private Task $task;

    protected function setUp(): void
    {
        $this->task = new Task();
    }

    public function testDefaultValues(): void
    {
        $this->assertNull($this->task->getId());
        $this->assertNull($this->task->getTitle());
        $this->assertNull($this->task->getDescription());
        $this->assertEquals('pending', $this->task->getStatus());
        $this->assertNull($this->task->getDueDate());
        $this->assertNull($this->task->getCreatedAt());
        $this->assertNull($this->task->getUpdatedAt());
    }

    public function testSetAndGetTitle(): void
    {
        $title = 'Test Task';
        $this->task->setTitle($title);
        $this->assertEquals($title, $this->task->getTitle());
    }

    public function testSetAndGetDescription(): void
    {
        $description = 'Test Description';
        $this->task->setDescription($description);
        $this->assertEquals($description, $this->task->getDescription());
    }

    public function testSetAndGetStatus(): void
    {
        $status = 'in_progress';
        $this->task->setStatus($status);
        $this->assertEquals($status, $this->task->getStatus());
    }

    public function testSetAndGetDueDate(): void
    {
        $dueDate = new \DateTime();
        $this->task->setDueDate($dueDate);
        $this->assertEquals($dueDate, $this->task->getDueDate());
    }

    public function testLifecycleCallbacks(): void
    {
        $this->task->setCreatedAtValue();
        $this->assertInstanceOf(\DateTime::class, $this->task->getCreatedAt());
        $this->assertInstanceOf(\DateTime::class, $this->task->getUpdatedAt());

        $originalUpdatedAt = $this->task->getUpdatedAt();
        sleep(1); // Wait 1 second to ensure different timestamp
        $this->task->setUpdatedAtValue();
        $this->assertNotEquals($originalUpdatedAt, $this->task->getUpdatedAt());
    }
}
