# Task Management API

A RESTful API built with Symfony for managing tasks. The API uses SQLite as its database and provides CRUD operations for tasks.

## Setup

1. Clone the repository
2. Run `composer install`
3. Create the database: `php bin/console doctrine:database:create`
4. Run migrations: `php bin/console doctrine:migrations:migrate`
5. Start the server (using Symfony CLI ): `symfony server:start`

## API Endpoints

### Create Task
- **POST** `/api/tasks`
- **Body:**
```json
{
    "title": "New Task",
    "description": "Task description",
    "status": "pending",
    "due_date": "2024-12-31 12:00:00"
}
```

### List Tasks
- **GET** `/api/tasks`
- Optional query parameter: `status` (pending, in_progress, completed)
  Example: `/api/tasks?status=completed`

### Get Single Task
- **GET** `/api/tasks/{id}`

### Update Task
- **PUT** `/api/tasks/{id}`
- **Body:** (all fields optional)
```json
{
    "title": "Updated Task",
    "description": "Updated description",
    "status": "in_progress",
    "due_date": "2024-12-31 12:00:00"
}
```

### Delete Task
- **DELETE** `/api/tasks/{id}`

## Response Formats

### Success Response
```json
{
    "id": 1,
    "title": "Task Title",
    "description": "Task Description",
    "status": "pending",
    "due_date": "2024-12-31 12:00:00",
    "created_at": "2024-03-04 09:00:00",
    "updated_at": "2024-03-04 09:00:00"
}
```

### Error Response
```json
{
    "error": "Error message"
}
```

## Status Codes
- 200: Success
- 201: Created
- 400: Bad Request
- 404: Not Found
