# IBU Chess - Full-Stack Web Application

A comprehensive chess management platform built as part of the IBU Web Engineering course. This project demonstrates modern web development practices with a complete RESTful API, database design, and interactive frontend.


### Milestone 1: Static Frontend (Completed)
- Single Page Application (SPA) with hash-based routing
- Responsive UI with Bootstrap 5 and glassmorphism design
- Complete view structure for all features
- Modern CSS with animations and effects

### Milestone 2: Database & Backend API (Completed)
- Normalized MySQL database schema
- RESTful API with full CRUD operations
- Custom PSR-4 autoloader
- Data Access Object (DAO) pattern
- Service layer architecture

### Milestone 3: Enhanced Business Logic & OpenAPI Documentation (Completed)
- FlightPHP framework integration
- Comprehensive input validation
- Business logic implementation for all entities
- Complete OpenAPI 3.0 specification
- Interactive Swagger UI documentation
- Enhanced error handling

## Tech Stack

### Backend
- **PHP 8.2+** - Modern PHP with strict typing
- **FlightPHP 3.17** - Lightweight micro-framework
- **MySQL 8.0** - Relational database
- **PDO** - Database abstraction with prepared statements
- **OpenAPI 3.0** - API documentation standard

### Frontend
- **HTML5** - Semantic markup
- **CSS3** - Modern styling with custom properties
- **Bootstrap 5.3** - Responsive framework
- **Vanilla JavaScript** - No framework dependencies
- **Bootstrap Icons** - Icon library

## 📋 Features

### User Management
- User registration with validation
- Role-based system (player, coach, arbiter, admin)
- User profiles with bio and rating
- Password hashing and security

### Tournament System
- Tournament creation and management
- Participant registration
- Tournament status tracking
- Prize pool management

### Game Management
- Record chess games with PGN notation
- Track game results
- Link games to tournaments
- Player statistics

### Move Tracking
- Record individual moves
- Chess notation validation
- Move comments and analysis
- Time tracking per move

### Review System
- Rate and review games
- 5-star rating system
- Comments and feedback
- Average rating calculation

## 🛠️ Installation & Setup

### Prerequisites
- PHP 8.2 or higher
- MySQL 8.0 or higher
- Composer (for dependency management)
- Web server (Apache/Nginx) or PHP built-in server

### Step 1: Clone the Repository
```bash
git clone https://github.com/zushiii11/ibu-chess-2025.git
cd ibu-chess-2025
```

### Step 2: Install Dependencies
```bash
composer install
```

### Step 3: Configure Database
Create a `.env` file in the `backend` directory:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=ibu_chess
DB_USER=root
DB_PASSWORD=your_password_here
DB_CHARSET=utf8mb4
```

### Step 4: Create Database Schema
```bash
# Login to MySQL
mysql -u root -p

# Create database and import schema
CREATE DATABASE ibu_chess CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ibu_chess;
SOURCE backend/sql/schema.sql;
```

### Step 5: Start the Server
```bash
# From project root directory
php -S localhost:8000 -t backend/public
```

The API will be available at `http://localhost:8000`

### Step 6: Access Documentation
- API Documentation: `http://localhost:8000/swagger-ui/`
- API Root: `http://localhost:8000/`
- Frontend (static): Open `frontend/index.html` in a browser

## 📚 API Documentation

### Interactive Documentation
Visit `http://localhost:8000/swagger-ui/` for the full interactive Swagger UI documentation where you can:
- Explore all available endpoints
- Test API calls directly in the browser
- View request/response schemas
- See validation requirements

### API Endpoints Overview

#### Users (`/api/users`)
- `GET /api/users` - List all users with filtering
- `GET /api/users/{id}` - Get user by ID
- `POST /api/users` - Create new user
- `PUT /api/users/{id}` - Update user
- `PATCH /api/users/{id}` - Partial update
- `DELETE /api/users/{id}` - Delete user

#### Tournaments (`/api/tournaments`)
- `GET /api/tournaments` - List tournaments
- `GET /api/tournaments/{id}` - Get tournament
- `GET /api/tournaments/{id}/participants` - Get tournament with participants
- `POST /api/tournaments` - Create tournament
- `PUT /api/tournaments/{id}` - Update tournament
- `PATCH /api/tournaments/{id}` - Partial update
- `DELETE /api/tournaments/{id}` - Delete tournament

#### Games (`/api/games`)
- `GET /api/games` - List games
- `GET /api/games/{id}` - Get game
- `GET /api/games/{id}/details` - Get game with moves and players
- `POST /api/games` - Create game
- `PUT /api/games/{id}` - Update game
- `PATCH /api/games/{id}` - Partial update
- `DELETE /api/games/{id}` - Delete game

#### Moves (`/api/moves`)
- `GET /api/moves` - List moves
- `GET /api/moves/{id}` - Get move
- `POST /api/moves` - Create move
- `PUT /api/moves/{id}` - Update move
- `PATCH /api/moves/{id}` - Partial update
- `DELETE /api/moves/{id}` - Delete move

#### Tournament Participants (`/api/tournament-participants`)
- `GET /api/tournament-participants` - List participants
- `GET /api/tournament-participants/{id}` - Get participant
- `POST /api/tournament-participants` - Register participant
- `PUT /api/tournament-participants/{id}` - Update participant
- `PATCH /api/tournament-participants/{id}` - Partial update
- `DELETE /api/tournament-participants/{id}` - Remove participant

#### Reviews (`/api/reviews`)
- `GET /api/reviews` - List reviews
- `GET /api/reviews/{id}` - Get review
- `POST /api/reviews` - Create review
- `PUT /api/reviews/{id}` - Update review
- `PATCH /api/reviews/{id}` - Partial update
- `DELETE /api/reviews/{id}` - Delete review

### Query Parameters
All list endpoints support:
- `limit` - Maximum number of results
- `offset` - Number of results to skip
- `order_by` - Field to sort by
- `order_dir` - Sort direction (ASC/DESC)
- Column-specific filters (e.g., `?role=player&rating=1500`)

### Response Codes
- `200 OK` - Successful GET/PUT/PATCH
- `201 Created` - Successful POST
- `204 No Content` - Successful DELETE
- `400 Bad Request` - Validation error
- `404 Not Found` - Resource not found
- `500 Internal Server Error` - Server error

## Project Structure

```
ibu-chess-2025/
├── backend/
│   ├── App/
│   │   ├── Config/          # Configuration classes
│   │   │   ├── Database.php
│   │   │   ├── Environment.php
│   │   │   └── jwt.php
│   │   ├── Dao/             # Data Access Objects
│   │   │   ├── BaseDao.php
│   │   │   ├── UserDao.php
│   │   │   ├── TournamentDao.php
│   │   │   ├── GameDao.php
│   │   │   ├── MoveDao.php
│   │   │   ├── TournamentParticipantDao.php
│   │   │   └── ReviewDao.php
│   │   ├── Services/        # Business logic layer
│   │   │   ├── BaseService.php
│   │   │   ├── UserService.php
│   │   │   ├── TournamentService.php
│   │   │   ├── GameService.php
│   │   │   ├── MoveService.php
│   │   │   ├── TournamentParticipantService.php
│   │   │   └── ReviewService.php
│   │   └── Routes/          # (Deprecated - now using FlightPHP)
│   ├── docs/
│   │   └── swagger.yaml     # OpenAPI specification
│   ├── public/
│   │   ├── index.php        # Application entry point
│   │   └── swagger-ui/      # API documentation UI
│   ├── sql/
│   │   └── schema.sql       # Database schema
│   └── bootstrap.php        # Autoloader & initialization
├── frontend/
│   ├── assets/              # Images and icons
│   ├── css/
│   │   └── main.css         # Custom styles
│   ├── js/
│   │   ├── router.js        # SPA routing
│   │   └── spa.js           # SPA initialization
│   ├── views/               # HTML view templates
│   └── index.html           # Main HTML file
├── composer.json            # PHP dependencies
├── composer.lock
└── README.md
```

## Database Schema

### Tables
1. **users** - User accounts and profiles
2. **tournaments** - Tournament information
3. **games** - Chess game records
4. **moves** - Individual move records
5. **tournament_participants** - Tournament registrations
6. **reviews** - Game reviews and ratings

### Key Relationships
- Games link to tournaments (optional)
- Games link to two users (white/black players)
- Moves link to games
- Tournament participants link users to tournaments
- Reviews link users to games (optional)

## Validation Rules

### User Validation
- Username: 3-50 characters, alphanumeric with underscores/hyphens
- Email: Valid email format, max 120 characters
- Password: Minimum 6 characters, hashed with bcrypt
- Rating: 0-3000 range
- Role: player, coach, arbiter, or admin

### Tournament Validation
- Name: 3-120 characters, required
- Location: Max 120 characters
- Dates: End date must be after start date
- Prize pool: Non-negative, max 9,999,999.99

### Game Validation
- Players: Both required, must be different
- Result: white, black, draw, or in_progress
- PGN: Optional, max 65535 characters

### Move Validation
- Game ID: Required
- Move number: Positive integer, required
- Notation: Valid chess notation, max 15 characters
- Time: Non-negative integer (seconds)

### Review Validation
- User ID: Required
- Rating: 1-5 stars, required
- Comment: Max 5000 characters

## Testing the API

### Using cURL

Create a user:
```bash
curl -X POST http://localhost:8000/api/users \
  -H "Content-Type: application/json" \
  -d '{
    "username": "magnus_carlsen",
    "email": "magnus@chess.com",
    "password": "password123",
    "role": "player",
    "rating": 2800
  }'
```

Get all users:
```bash
curl http://localhost:8000/api/users
```

Get users with filters:
```bash
curl "http://localhost:8000/api/users?role=player&limit=10&order_by=rating&order_dir=DESC"
```

### Using Swagger UI
1. Navigate to `http://localhost:8000/swagger-ui/`
2. Click on any endpoint
3. Click "Try it out"
4. Fill in the parameters
5. Click "Execute"
