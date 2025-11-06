# Todo API - Laravel Training Project

A comprehensive Todo API built with Laravel for training junior developers. This API provides authentication, multiple todo lists per user, todos with images, notes, and reminders.

## Features

- **Authentication**: Register and login with Laravel Sanctum
- **Multiple Todo Lists**: Users can create multiple todo lists
- **Todo Management**: Full CRUD operations for todos
- **Image Support**: Todos can have images attached
- **Notes**: Todo lists can have notes
- **Reminders**: Todo lists can have reminders with dates

## Requirements

- PHP >= 8.1
- Composer
- MySQL or PostgreSQL
- Laravel 10.x

## Installation

1. **Clone the repository** (if applicable) or navigate to the project directory

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Update `.env` file** with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=todo_api
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Create storage link** (for image uploads)
   ```bash
   php artisan storage:link
   ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```

The API will be available at `http://localhost:8000`

## API Endpoints

### Authentication

#### Register
```http
POST /api/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

#### Login
```http
POST /api/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
    },
    "token": "1|xxxxxxxxxxxxx"
}
```

#### Logout
```http
POST /api/logout
Authorization: Bearer {token}
```

#### Get Authenticated User
```http
GET /api/user
Authorization: Bearer {token}
```

### Todo Lists

#### Get All Todo Lists
```http
GET /api/todo-lists
Authorization: Bearer {token}
```

#### Create Todo List
```http
POST /api/todo-lists
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "Work Tasks",
    "description": "Tasks for work"
}
```

#### Get Single Todo List
```http
GET /api/todo-lists/{id}
Authorization: Bearer {token}
```

#### Update Todo List
```http
PUT /api/todo-lists/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "Updated Title",
    "description": "Updated description"
}
```

#### Delete Todo List
```http
DELETE /api/todo-lists/{id}
Authorization: Bearer {token}
```

### Todos

#### Get All Todos
```http
GET /api/todos
Authorization: Bearer {token}
```

#### Create Todo
```http
POST /api/todos
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
    "todo_list_id": 1,
    "title": "Complete project",
    "description": "Finish the project",
    "completed": false,
    "image": [file]
}
```

#### Get Single Todo
```http
GET /api/todos/{id}
Authorization: Bearer {token}
```

#### Update Todo
```http
PUT /api/todos/{id}
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
    "title": "Updated title",
    "completed": true,
    "image": [file]
}
```

#### Delete Todo
```http
DELETE /api/todos/{id}
Authorization: Bearer {token}
```

### Todo List Notes

#### Get All Notes for a Todo List
```http
GET /api/todo-lists/{todoListId}/notes
Authorization: Bearer {token}
```

#### Create Note
```http
POST /api/todo-lists/{todoListId}/notes
Authorization: Bearer {token}
Content-Type: application/json

{
    "note": "This is a note for the todo list"
}
```

#### Get Single Note
```http
GET /api/todo-lists/{todoListId}/notes/{noteId}
Authorization: Bearer {token}
```

#### Update Note
```http
PUT /api/todo-lists/{todoListId}/notes/{noteId}
Authorization: Bearer {token}
Content-Type: application/json

{
    "note": "Updated note"
}
```

#### Delete Note
```http
DELETE /api/todo-lists/{todoListId}/notes/{noteId}
Authorization: Bearer {token}
```

### Todo List Reminders

#### Get All Reminders for a Todo List
```http
GET /api/todo-lists/{todoListId}/reminders
Authorization: Bearer {token}
```

#### Create Reminder
```http
POST /api/todo-lists/{todoListId}/reminders
Authorization: Bearer {token}
Content-Type: application/json

{
    "reminder_date": "2024-12-31 23:59:59"
}
```

#### Get Single Reminder
```http
GET /api/todo-lists/{todoListId}/reminders/{reminderId}
Authorization: Bearer {token}
```

#### Update Reminder
```http
PUT /api/todo-lists/{todoListId}/reminders/{reminderId}
Authorization: Bearer {token}
Content-Type: application/json

{
    "reminder_date": "2025-01-01 00:00:00"
}
```

#### Delete Reminder
```http
DELETE /api/todo-lists/{todoListId}/reminders/{reminderId}
Authorization: Bearer {token}
```

## Database Structure

### Tables

- **users**: User accounts
- **todo_lists**: Todo lists belonging to users
- **todos**: Individual todos in todo lists
- **todo_list_notes**: Notes attached to todo lists
- **todo_list_reminders**: Reminders for todo lists

### Relationships

- User has many TodoLists
- TodoList belongs to User
- TodoList has many Todos
- TodoList has many Notes
- TodoList has many Reminders
- Todo belongs to TodoList

## Image Storage

Images uploaded for todos are stored in `storage/app/public/todo-images/`. Make sure to run `php artisan storage:link` to create a symbolic link from `public/storage` to `storage/app/public`.

## Authentication

This API uses Laravel Sanctum for token-based authentication. Include the token in the Authorization header:

```
Authorization: Bearer {your-token-here}
```

## Learning Points for Junior Developers

1. **MVC Architecture**: Understanding Models, Views (API responses), and Controllers
2. **RESTful API Design**: Following REST conventions for API endpoints
3. **Authentication**: Implementing token-based authentication with Sanctum
4. **Database Relationships**: Working with Eloquent relationships (hasMany, belongsTo)
5. **Form Validation**: Using Form Request classes for validation
6. **File Uploads**: Handling file uploads and storage
7. **API Resource Responses**: Structuring JSON responses
8. **Middleware**: Using authentication middleware to protect routes
9. **Migrations**: Database schema management with migrations
10. **Eloquent ORM**: Working with Eloquent models and queries

## Testing the API

You can use tools like:
- **Postman**: Import the endpoints and test
- **cURL**: Command-line tool for testing
- **Thunder Client**: VS Code extension
- **Insomnia**: API testing tool

### Example cURL Request

```bash
# Register
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john@example.com","password":"password123","password_confirmation":"password123"}'

# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"password123"}'

# Get Todo Lists (replace {token} with actual token)
curl -X GET http://localhost:8000/api/todo-lists \
  -H "Authorization: Bearer {token}"
```

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── AuthController.php
│   │       ├── TodoListController.php
│   │       ├── TodoController.php
│   │       ├── TodoListNoteController.php
│   │       └── TodoListReminderController.php
│   └── Requests/
│       ├── RegisterRequest.php
│       ├── LoginRequest.php
│       ├── StoreTodoListRequest.php
│       ├── UpdateTodoListRequest.php
│       ├── StoreTodoRequest.php
│       ├── UpdateTodoRequest.php
│       ├── StoreTodoListNoteRequest.php
│       └── StoreTodoListReminderRequest.php
├── Models/
│   ├── User.php
│   ├── TodoList.php
│   ├── Todo.php
│   ├── TodoListNote.php
│   └── TodoListReminder.php
database/
└── migrations/
    ├── create_todo_lists_table.php
    ├── create_todos_table.php
    ├── create_todo_list_notes_table.php
    └── create_todo_list_reminders_table.php
routes/
└── api.php
```

## Next Steps for Learning

1. Add pagination to list endpoints
2. Implement search/filter functionality
3. Add email notifications for reminders
4. Create API documentation with Swagger/OpenAPI
5. Write unit tests using PHPUnit
6. Add soft deletes for todos and todo lists
7. Implement todo priorities
8. Add todo categories/tags
9. Create a frontend application to consume this API
10. Implement rate limiting for API endpoints

## License

This is a training project. Feel free to use and modify as needed.

## Support

For questions or issues, please refer to the Laravel documentation: https://laravel.com/docs
