# IBU Chess Application - Milestone 4

## Overview
A full-stack chess application built with PHP (FlightPHP) backend and vanilla JavaScript frontend, featuring user authentication, role-based access control, and multiple game modes.

## Milestone 4 Deliverables ✅

### 1. Authentication and Middleware (1pt) ✅
- ✅ **Request validation middleware** - Validates incoming requests and authentication tokens
- ✅ **Error handling** - Global error handling for API responses
- ✅ **Logging** - Basic logging for authentication and critical operations
- ✅ **User authentication** - Implemented using FlightPHP with JWT tokens
- ✅ **User registration and login** - Full registration and login flow
- ✅ **Password hashing** - Secure password hashing using bcrypt

### 2. Authorization (1pt) ✅
- ✅ **Role-based access control (RBAC)** - Implemented admin and regular user roles
- ✅ **Admin CRUD operations** - Admins can perform full CRUD on all entities
- ✅ **Restricted user access** - Regular users have limited access based on their role
- ✅ **Middleware enforcement** - Authorization checks enforced at the middleware level

### 3. Frontend Updates (3pts) ✅
- ✅ **Dynamic authenticated features** - Personalized dashboard for logged-in users
- ✅ **Admin panel** - Full admin interface for managing users, games, and tournaments
- ✅ **Role-based UI components** - UI elements (buttons, menus) visible based on user role
- ✅ **Fully connected frontend-backend** - All frontend features integrated with backend APIs

## Features

### Game Modes
- **Play vs AI** - Challenge the computer at different difficulty levels
- **Play vs Human** - Local two-player chess games
- **Challenge a Friend** - Send and receive game challenges

### User Management
- User registration and login
- Profile management with stats tracking
- User roles (Admin/Regular User)
- Password reset functionality

### Game Features
- Interactive chess board with drag-and-drop
- Move validation and game rules enforcement
- Game history and replay
- Rating system (ELO)

### Admin Features
- User management (CRUD operations)
- Game monitoring and management
- Tournament administration
- System analytics and reporting

## Tech Stack

### Backend
- **Framework**: FlightPHP (Micro-framework)
- **Database**: MySQL
- **Authentication**: JWT (JSON Web Tokens)
- **Password Hashing**: bcrypt
- **API Documentation**: Swagger/OpenAPI

### Frontend
- **Core**: Vanilla JavaScript (ES6+)
- **UI**: Custom CSS with modern design
- **Router**: Custom SPA router
- **API Integration**: Fetch API with authentication

## Project Structure

```
ibu-chess-2025-milestone4/
├── backend/
│   ├── config/         # Database and app configuration
│   ├── controllers/    # API controllers
│   ├── middleware/     # Authentication & authorization middleware
│   ├── models/         # Database models
│   ├── routes/         # API route definitions
│   └── index.php       # Entry point
├── frontend/
│   ├── assets/         # Images and static files
│   ├── css/           # Stylesheets
│   ├── js/            # JavaScript modules
│   │   ├── api.js     # API client with auth
│   │   ├── auth.js    # Authentication logic
│   │   ├── router.js  # SPA routing
│   │   └── app.js     # Main application
│   ├── views/         # HTML view templates
│   └── index.html     # Main entry point
└── vendor/            # PHP dependencies

```

## Installation & Setup

### Prerequisites
- PHP 7.4+
- MySQL 5.7+
- Composer
- Web server (Apache/Nginx)

### Steps
1. Clone the repository
2. Install PHP dependencies:
   ```bash
   composer install
   ```
3. Import the database schema from `database.sql`
4. Configure database connection in `backend/config/database.php`
5. Start the backend server
6. Open `frontend/index.html` in a browser

## API Endpoints

### Authentication
- `POST /api/auth/register` - User registration
- `POST /api/auth/login` - User login
- `POST /api/auth/logout` - User logout

### Users
- `GET /api/users` - Get all users (Admin only)
- `GET /api/users/:id` - Get user by ID
- `PUT /api/users/:id` - Update user
- `DELETE /api/users/:id` - Delete user (Admin only)

### Games
- `POST /api/games` - Create new game
- `GET /api/games` - Get all games
- `GET /api/games/:id` - Get game details
- `PUT /api/games/:id/move` - Submit a move
- `DELETE /api/games/:id` - Delete game (Admin only)

### Challenges
- `POST /api/challenges` - Send challenge
- `GET /api/challenges` - Get user challenges
- `PUT /api/challenges/:id/accept` - Accept challenge
- `PUT /api/challenges/:id/decline` - Decline challenge

## Security Features
- JWT-based authentication
- Bcrypt password hashing
- Role-based authorization
- Request validation
- SQL injection prevention (prepared statements)
- XSS protection

## Contributors
- Student Name
- Course: Web Programming Course
- Milestone: 4

## License
Educational project for IBU

---
**Status**: ✅ All Milestone 4 deliverables completed and ready for review
