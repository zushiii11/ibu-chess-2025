# Milestone 3: Building a Professional REST API

**IBU Web Engineering Course**  
**Deadline**: November 17, 2025 (8:00 AM)  
**Student**: IBU Chess Team  
**Status**: Completed ✓

---

## Introduction

For Milestone 3, I focused on transforming the basic backend from Milestone 2 into a production-ready RESTful API. The main goals were to add comprehensive business logic validation, integrate a proper PHP framework, and create professional API documentation that anyone can understand and use.

This milestone taught me a lot about real-world API development practices, including proper input validation, error handling, and the importance of good documentation.

---

## What I Built

### 1. Business Logic & Validation (2 points)

The first major task was implementing robust validation for all database entities. Instead of just passing data straight to the database, I created validation layers in each service that check everything before it touches the database.

#### User Management
For the user service, I implemented:
- Username checks (must be 3-50 characters, only letters, numbers, underscores and hyphens)
- Email validation using PHP's built-in filter
- Password requirements (minimum 6 characters, automatically hashed using bcrypt)
- Rating boundaries (chess ratings typically range from 0-3000)
- Role restrictions (only allowing: player, coach, arbiter, or admin)

The password hashing was particularly important here - I used PHP's `password_hash()` function with bcrypt to ensure user passwords are stored securely.

#### Tournament System
Tournaments needed more complex validation:
- Names must be descriptive (3-120 characters)
- Date logic to ensure tournaments don't end before they start
- Prize pool validation (can't be negative, and there's a reasonable maximum)
- Special method to fetch tournaments with all their participants in one go

#### Game Records
Chess games have specific requirements:
- Both white and black players must be specified
- The two players can't be the same person (caught this edge case during testing!)
- Results can only be: white wins, black wins, draw, or in progress
- PGN notation storage for full game records

#### Move Tracking
Individual moves needed careful validation:
- Chess notation format checking (using regex to validate algebraic notation)
- Sequential move numbering
- Time tracking for competitive games
- Comments and analysis support

#### Tournament Participants
Managing who's in which tournament:
- Proper linking between tournaments and users
- Status tracking (registered, checked in, eliminated, or winner)
- Seeding system for tournament brackets

#### Review System
Let players rate and review games:
- Star rating system (1-5 stars only)
- Comment length limits to keep things reasonable
- Calculate average ratings for games

### 2. FlightPHP Integration (1 point)

I migrated the entire API from the custom router in Milestone 2 to FlightPHP, a lightweight but powerful micro-framework. This was a significant improvement because:

**Why FlightPHP?**
- Much cleaner route definitions
- Built-in request/response handling
- Better error management
- Easy to learn and use
- Perfect for REST APIs

**What Changed:**
- Replaced the manual routing system with Flight's elegant route syntax
- Added proper CORS middleware so the frontend can actually talk to the API
- Implemented comprehensive error handling that returns proper HTTP status codes
- Set up JSON request/response handling automatically

**The Result:**
Instead of manually parsing URLs and managing responses, I now have clean routes like:
```php
Flight::route('GET /api/users', function () use ($userService) {
    $filters = Flight::request()->query->getData();
    jsonResponse($userService->getAll($filters));
});
```

Much more readable and maintainable!

### 3. OpenAPI Documentation (2 points)

This was probably the most time-consuming part, but also the most rewarding. I created complete API documentation using the OpenAPI 3.0.3 standard.

**What's Included:**
- Every single one of the 38 endpoints documented
- Request and response schemas for all operations
- All validation rules clearly specified
- Query parameter documentation
- Example requests and responses
- Proper HTTP status code documentation

**Interactive Swagger UI:**
I set up a custom Swagger UI interface that:
- Looks professional with a dark theme matching the project aesthetic
- Shows all endpoints organized by resource
- Lets you test API calls directly in the browser
- Displays response data in real-time
- Has a search/filter feature to find specific endpoints quickly

The UI uses custom styling to match the IBU Chess branding, with gradient effects and proper color coding for different HTTP methods (GET is green, POST is blue, DELETE is red, etc.).

---

## Technical Implementation

### Architecture Overview

The project follows a clean layered architecture:

**Config Layer** → Handles database connections and environment variables  
**DAO Layer** → Communicates with the database (6 different DAOs)  
**Service Layer** → Contains business logic and validation (6 services)  
**Presentation Layer** → FlightPHP routes that handle HTTP requests  
**Documentation Layer** → OpenAPI spec and Swagger UI

### Key Technical Decisions

**1. Validation Approach**
I put all validation in the service layer rather than in the controllers or DAOs. This keeps the business logic centralized and makes it easy to maintain. When validation fails, it throws an `InvalidArgumentException` which FlightPHP catches and converts to a 400 Bad Request response.

**2. Password Security**
Used PHP's built-in `password_hash()` with bcrypt algorithm. This is industry standard and handles salting automatically. Even if someone got access to the database, the passwords would be useless.

**3. Error Handling**
Set up a global error handler in FlightPHP that:
- Catches validation errors (400 status)
- Catches not found errors (404 status)  
- Catches server errors (500 status)
- Always returns JSON with a clear error message

**4. CORS Setup**
Added middleware to handle Cross-Origin requests so the frontend (which might be on a different port) can communicate with the API without browser security issues.

---

## API Endpoints

I implemented full CRUD (Create, Read, Update, Delete) for six main resources:

| Resource | Endpoints | Purpose |
|----------|-----------|---------|
| Users | 6 endpoints | User accounts and authentication |
| Tournaments | 7 endpoints | Tournament management (extra endpoint for participants) |
| Games | 7 endpoints | Chess game records (extra endpoint for moves/players) |
| Moves | 6 endpoints | Individual move tracking |
| Tournament Participants | 6 endpoints | Registration and status management |
| Reviews | 6 endpoints | Game ratings and feedback |

**Total: 38 fully documented and tested endpoints**

### Advanced Features

Beyond basic CRUD, I added some useful features:

**Pagination & Filtering**
Every list endpoint supports:
- `limit` - how many results to return
- `offset` - skip X results (for pagination)
- `order_by` - sort by any column
- `order_dir` - ASC or DESC
- Column filters - filter by any field value

**Enhanced Endpoints**
- `GET /api/tournaments/{id}/participants` - Get a tournament with all its participants in one request
- `GET /api/games/{id}/details` - Get a game with all moves and player information

---

## Testing & Validation

I tested every endpoint thoroughly using both Swagger UI and curl commands. Here are some examples of validation in action:

**Valid Request:**
```bash
curl -X POST http://localhost:8000/api/users \
  -H "Content-Type: application/json" \
  -d '{"username":"player1","email":"player@chess.com","password":"secure123"}'
```
Response: 201 Created with user data

**Invalid Request (Bad Email):**
```bash
curl -X POST http://localhost:8000/api/users \
  -H "Content-Type: application/json" \
  -d '{"username":"player1","email":"not-an-email","password":"secure123"}'
```
Response: 400 Bad Request with error message "Invalid email format"

**Invalid Request (Short Password):**
```bash
curl -X POST http://localhost:8000/api/users \
  -H "Content-Type: application/json" \
  -d '{"username":"player1","email":"test@test.com","password":"abc"}'
```
Response: 400 Bad Request with "Password must be at least 6 characters long"

This validation prevents bad data from ever reaching the database.

---

## Documentation Quality

The OpenAPI documentation includes:

### For Each Endpoint:
- Clear description of what it does
- All required parameters marked
- All optional parameters documented
- Request body schema with examples
- Response schema for success (200/201)
- Response schema for errors (400/404/500)
- Validation requirements spelled out

### For Each Schema:
- All fields documented
- Data types specified (string, integer, date, etc.)
- Min/max lengths where applicable
- Enum values for restricted fields
- Required vs optional fields clearly marked

### Overall:
- Organized by resource type (Users, Tournaments, etc.)
- Searchable and filterable in Swagger UI
- Professional appearance
- Interactive testing capability

---

## Challenges & Solutions

### Challenge 1: FlightPHP Route Conflicts
**Problem**: Initially had issues with routes like `/api/games/{id}/details` conflicting with `/api/games/{id}`

**Solution**: Ordered routes carefully so more specific routes are registered before general ones.

### Challenge 2: Validation Complexity
**Problem**: Needed to validate some fields only on creation, others only on update, and some always.

**Solution**: Added a `$isCreate` parameter to validation methods to handle creation vs. update logic differently.

### Challenge 3: Password Handling
**Problem**: Don't want to return password hashes in API responses, but need them stored securely.

**Solution**: Hash passwords in the service layer before saving, and modified the DAO to never return password_hash fields in responses.

### Challenge 4: Swagger UI Path Issues
**Problem**: Swagger UI couldn't find the swagger.yaml file due to directory structure.

**Solution**: Copied swagger.yaml to the public directory and updated the path reference.

---

## What I Learned

This milestone taught me several important lessons about backend development:

1. **Validation is Critical**: Never trust user input. Always validate on the server side, even if you have client-side validation.

2. **Error Messages Matter**: Clear, specific error messages make APIs much easier to use. "Invalid input" is useless; "Username must be at least 3 characters" is helpful.

3. **Documentation is Essential**: Even though writing documentation takes time, it's absolutely necessary. The Swagger UI makes the API accessible to anyone.

4. **Frameworks Save Time**: FlightPHP reduced hundreds of lines of routing code down to clean, readable route definitions.

5. **Security Can't Be an Afterthought**: Password hashing, SQL injection prevention, and input validation need to be built in from the start.

---

## Code Quality

Throughout this milestone, I focused on maintaining high code quality:

- **Type Safety**: Used PHP 8.2+ strict typing throughout
- **DRY Principle**: No code duplication; common logic in base classes
- **Single Responsibility**: Each class has one clear purpose
- **Meaningful Names**: Variables and methods named clearly
- **Consistent Style**: Same formatting and conventions everywhere
- **Error Handling**: Try-catch blocks where needed, proper error propagation

---

## Conclusion

Milestone 3 successfully transformed the basic backend from Milestone 2 into a professional, production-ready REST API. The combination of comprehensive validation, clean framework integration, and thorough documentation creates an API that's both powerful and easy to use.

The 38 endpoints cover all the necessary functionality for the chess platform, and the OpenAPI documentation ensures that anyone (including future me!) can understand and use the API without confusion.

I'm particularly proud of the custom Swagger UI design - it looks professional and matches the project's aesthetic perfectly. The dark theme with the chess branding creates a cohesive experience across the entire platform.

---

## Files Modified/Created

**Core API Files:**
- `backend/public/index.php` - Complete rewrite with FlightPHP
- `backend/App/Services/UserService.php` - 120 lines of validation logic
- `backend/App/Services/TournamentService.php` - Tournament validation
- `backend/App/Services/GameService.php` - Game validation  
- `backend/App/Services/MoveService.php` - Move notation validation
- `backend/App/Services/TournamentParticipantService.php` - Participant management
- `backend/App/Services/ReviewService.php` - Review validation

**Documentation:**
- `backend/docs/swagger.yaml` - 1000+ lines of OpenAPI specification
- `backend/public/swagger-ui/index.html` - Custom styled documentation UI
- `MILESTONE3.md` - This comprehensive write-up
- `README.md` - Updated with Milestone 3 information

**Configuration:**
- `composer.json` - Added FlightPHP dependency
- `backend/.env.example` - Environment configuration template

---

## Ready for Submission

This milestone is complete and ready for review. The API is fully functional, thoroughly tested, and professionally documented. 

To test it yourself:
1. Start the server: `php -S localhost:8000 -t backend/public`
2. Visit the Swagger UI: `http://localhost:8000/swagger-ui/`
3. Try out any endpoint right in the browser!

**Repository**: https://github.com/zushiii11/ibu-chess-2025  
**Documentation**: Available at `/swagger-ui/` when running  
**Grade Expectation**: 5/5 points

---

*Submitted by IBU Chess Team - November 13, 2025*
