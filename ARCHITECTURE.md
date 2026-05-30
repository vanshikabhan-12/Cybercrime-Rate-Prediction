# System Architecture & Design

A comprehensive guide to understanding the Cybercrime Rate Prediction system architecture.

## 🏗️ High-Level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                      USER BROWSER                            │
├─────────────────────────────────────────────────────────────┤
│  ┌──────────────────────────────────────────────────────┐   │
│  │   Frontend Layer (HTML/CSS/JavaScript)               │   │
│  │  - static/index.html                                 │   │
│  │  - static/style.css                                  │   │
│  │  - static/app.js                                     │   │
│  └──────────────────────────────────────────────────────┘   │
└────────────┬────────────────────────────────────────────────┘
             │ HTTP Requests/JSON
             │
┌────────────▼────────────────────────────────────────────────┐
│              FastAPI Application Server                      │
│              (Port 8000 - localhost:8000)                    │
├─────────────────────────────────────────────────────────────┤
│  ┌──────────────────────────────────────────────────────┐   │
│  │   API Layer (main.py)                                │   │
│  │  - /api/auth/*          (Authentication)             │   │
│  │  - /api/crimes/*        (Crime Data)                 │   │
│  │  - /api/reports/*       (Crime Reports)              │   │
│  │  - /api/messages/*      (Social Features)            │   │
│  │  - /api/predictions/*   (Predictions)                │   │
│  │  - /api/admin/*         (Admin Functions)            │   │
│  └──────────────────────────────────────────────────────┘   │
│                         │                                    │
│  ┌──────────────────────▼──────────────────────────────┐   │
│  │   Business Logic Layer                              │   │
│  │  - auth.py              (JWT & Password Management) │   │
│  │  - schemas.py           (Data Validation)           │   │
│  │  - models.py            (Data Models)               │   │
│  └──────────────────────────────────────────────────────┘   │
│                         │                                    │
│  ┌──────────────────────▼──────────────────────────────┐   │
│  │   Data Access Layer (SQLAlchemy ORM)                │   │
│  │  - database.py          (Database Connection)       │   │
│  │  - models.py            (ORM Models)                │   │
│  └──────────────────────────────────────────────────────┘   │
└────────────┬────────────────────────────────────────────────┘
             │ SQL Queries
             │
┌────────────▼────────────────────────────────────────────────┐
│              MySQL Database (Port 3306)                      │
│           Database: cybercrime_db                            │
├─────────────────────────────────────────────────────────────┤
│  Tables:                                                     │
│  - users               (User accounts)                       │
│  - crimes              (Crime data records)                  │
│  - crime_reports       (User reports)                        │
│  - messages            (Community messages)                  │
│  - predictions         (Crime predictions)                   │
└─────────────────────────────────────────────────────────────┘
```

---

## 📁 Directory Structure Explained

```
Cybercrime-Rate-Prediction/
│
├── main.py                      # ⭐ Main FastAPI application
│                                 # All API routes defined here
│                                 # ~350 lines, well-organized
│
├── models.py                    # SQLAlchemy ORM models
│                                 # User, Crime, Report, Message, Prediction
│
├── schemas.py                   # Pydantic validation schemas
│                                 # Input/output data validation
│
├── database.py                  # Database connection setup
│                                 # SessionLocal, engine, Base
│
├── auth.py                      # Authentication utilities
│                                 # JWT token creation/validation
│                                 # Password hashing
│
├── config.py                    # Configuration management
│                                 # Environment variables loading
│
├── load_data.py                 # CSV data loader script
│                                 # Populates database from CSV
│
├── requirements.txt             # Python dependencies
│
├── .env.example                 # Environment template
│
├── static/                      # Frontend assets
│   ├── index.html               # Main HTML page
│   ├── style.css                # CSS styling
│   └── app.js                   # JavaScript logic
│
├── cybercrime_db.csv            # Sample crime data
│
├── README.md                    # Complete documentation
├── QUICKSTART.md                # Quick start guide
├── API_ENDPOINTS.md             # API reference
└── ARCHITECTURE.md              # This file
```

---

## 🔄 Data Flow Diagram

### User Registration Flow
```
1. User enters email, password, name in frontend
         ↓
2. JavaScript sends POST /auth/register
         ↓
3. FastAPI receives request
         ↓
4. Validate input with Pydantic schema
         ↓
5. Check if email/username exists
         ↓
6. Hash password with bcrypt
         ↓
7. Create User record in database
         ↓
8. Generate JWT token
         ↓
9. Return token + user data to frontend
         ↓
10. Frontend stores token in localStorage
         ↓
11. User logged in!
```

### Crime Report Submission Flow
```
1. Authenticated user fills report form
         ↓
2. JavaScript sends POST /reports with token
         ↓
3. FastAPI verifies JWT token
         ↓
4. Identify user from token
         ↓
5. Validate report data
         ↓
6. Create CrimeReport record
         ↓
7. Return report confirmation
         ↓
8. Frontend shows success message
         ↓
9. Reload reports list
```

### Crime Prediction Flow
```
1. Admin requests prediction via API
         ↓
2. System queries historical crime data
         ↓
3. Calculate average reported cases
         ↓
4. Apply prediction logic (baseline: avg * 1.1)
         ↓
5. Calculate confidence score
         ↓
6. Store prediction in database
         ↓
7. Return prediction to admin
         ↓
8. Displayed in predictions dashboard
```

---

## 🔐 Authentication & Security

### JWT Token Flow
```
┌─────────────────────────────────────────┐
│  1. User Credentials (email + password) │
└─────────────────┬───────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────┐
│  2. Backend verifies password            │
│     - Compares with bcrypt hash          │
└─────────────────┬───────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────┐
│  3. Create JWT Token if valid            │
│     Payload: { "sub": user_id, "exp": } │
│     Signed with SECRET_KEY               │
└─────────────────┬───────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────┐
│  4. Return token to frontend             │
└─────────────────┬───────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────┐
│  5. Frontend stores in localStorage      │
└─────────────────┬───────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────┐
│  6. Include in Authorization header      │
│     Header: "Authorization: Bearer {jwt}"│
└─────────────────┬───────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────┐
│  7. Backend validates token              │
│     - Check signature                    │
│     - Check expiration                   │
│     - Get user from database             │
└─────────────────┬───────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────┐
│  8. Process request with user context    │
└─────────────────────────────────────────┘
```

### Security Layers
```
1. Password Security
   - User password → bcrypt hashing → database stores hash only
   - On login: verify entered password against stored hash

2. JWT Token Security
   - Token signed with SECRET_KEY
   - Token expires after 30 minutes
   - Token includes user_id for identification

3. SQL Injection Prevention
   - SQLAlchemy parameterized queries
   - No string concatenation in SQL

4. CORS Protection
   - Configured in FastAPI
   - Allows specific origins (currently all)

5. Role-Based Access Control
   - Admin-only endpoints checked via is_admin flag
   - Users can only access their own data
```

---

## 📊 Database Schema

### Users Table
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE,
    username VARCHAR(255) UNIQUE,
    hashed_password VARCHAR(255),
    full_name VARCHAR(255),
    is_admin BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### Crimes Table
```sql
CREATE TABLE crimes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    city VARCHAR(255),
    state VARCHAR(255),
    year INT,
    crime_type VARCHAR(255),
    reported_cases INT,
    solved_cases INT,
    unsolved_cases INT,
    monetary_loss FLOAT,
    victim_age INT,
    victim_gender VARCHAR(50),
    victim_profession VARCHAR(255),
    population INT,
    literacy_rate FLOAT,
    internet_penetration FLOAT,
    unemployment_rate FLOAT,
    INDEX idx_city (city),
    INDEX idx_state (state),
    INDEX idx_year (year),
    INDEX idx_crime_type (crime_type)
);
```

### Crime Reports Table
```sql
CREATE TABLE crime_reports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    city VARCHAR(255),
    crime_type VARCHAR(255),
    description TEXT,
    severity VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### Messages Table
```sql
CREATE TABLE messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    content TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### Predictions Table
```sql
CREATE TABLE predictions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    city VARCHAR(255),
    state VARCHAR(255),
    crime_type VARCHAR(255),
    predicted_cases INT,
    confidence FLOAT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

---

## 🔌 API Endpoint Structure

### Route Organization

```
/api/
├── auth/
│   ├── POST   /register      Create new user
│   ├── POST   /login         Authenticate user
│   └── GET    /me            Get current user
│
├── crimes/
│   ├── GET    /              Get all crimes (filterable)
│   ├── GET    /cities        Get distinct cities
│   ├── GET    /states        Get distinct states
│   ├── GET    /types         Get crime types
│   └── GET    /stats         Get dashboard statistics
│
├── reports/
│   ├── POST   /              Create new report
│   ├── GET    /              Get user/all reports
│   └── DELETE /{id}          Delete report
│
├── messages/
│   ├── POST   /              Create message
│   └── GET    /              Get all messages
│
├── predictions/
│   ├── POST   /              Create prediction (admin)
│   └── GET    /              Get all predictions
│
└── admin/
    ├── GET    /users         Get all users (admin)
    ├── POST   /users/{id}/promote  Promote to admin
    └── DELETE /crimes/{id}   Delete crime record
```

---

## 🎯 Key Design Decisions

### 1. **Monolithic Architecture**
Why: Simple for student project, single deployment
Advantage: Easy to understand, debug, deploy locally

### 2. **SQLAlchemy ORM**
Why: Type safety, query building, SQL injection prevention
Advantage: Better than raw SQL for beginners

### 3. **Pydantic Schemas**
Why: Automatic data validation
Advantage: Invalid requests caught before database

### 4. **JWT Tokens**
Why: Stateless authentication, works with APIs
Advantage: No session storage needed, scalable

### 5. **Vanilla JavaScript**
Why: No build step, understand fundamentals
Advantage: Learning opportunity, no npm complexity

### 6. **Baseline ML Model**
Why: Demonstrate predictions simply
Advantage: Foundation for real ML later

---

## 🚀 Performance Considerations

### Database Indexing
- Crimes table indexed on: city, state, year, crime_type
- Enables fast filtering

### Pagination
- Crimes endpoint: limit 100 records default
- Messages endpoint: limit 50 records default
- Prevents loading entire database

### Connection Pooling
- SQLAlchemy manages connection pool
- Reuses connections efficiently

### Future Optimizations
- Cache frequent queries (Redis)
- Add database query caching
- Implement API rate limiting
- Use CDN for static files

---

## 🔧 Extensibility Points

### Add New Features

#### 1. New Endpoint
```python
@app.get("/api/newfeature")
async def new_feature(current_user: User = Depends(get_current_user)):
    # Your logic here
    return {"data": "result"}
```

#### 2. New Database Model
```python
class NewModel(Base):
    __tablename__ = "new_model"
    id = Column(Integer, primary_key=True)
    # Add columns
```

#### 3. New Validation Schema
```python
class NewSchema(BaseModel):
    field1: str
    field2: int
    
    class Config:
        from_attributes = True
```

#### 4. New Frontend Section
```html
<section id="newSection" style="display:none;" class="section">
    <!-- Your HTML -->
</section>

<script>
function showNew() {
    hideAllSections();
    document.getElementById('newSection').style.display = 'block';
    loadNewData();
}
</script>
```

---

## 📈 Scalability Path

### Phase 1: Current (Single Server)
- FastAPI on local/single server
- MySQL on local/single server
- Suitable for: Learning, 10-100 users

### Phase 2: Docker (Containerization)
- Add Dockerfile
- Docker Compose for easy deployment
- Suitable for: Testing, 100-1000 users

### Phase 3: Cloud (Scalable)
- Deploy to AWS/GCP/Azure
- Separate database server
- Load balancer
- Suitable for: 1000+ users

### Phase 4: Microservices (Advanced)
- Split into separate services
- Message queue (RabbitMQ)
- Service discovery
- Suitable for: Enterprise, 10000+ users

---

## 🧪 Testing Strategy

### Unit Tests
```python
def test_password_hashing():
    hashed = hash_password("password")
    assert verify_password("password", hashed)
```

### Integration Tests
```python
def test_user_registration():
    response = client.post("/api/auth/register", json={...})
    assert response.status_code == 201
```

### E2E Tests
```python
def test_complete_workflow():
    # Register → Login → Create report → View report
```

---

## 📚 Code Quality Guidelines

### Style
- PEP 8 compliant Python code
- Meaningful variable names
- Single responsibility principle

### Documentation
- Docstrings for complex functions
- Comments for non-obvious logic
- README for usage

### Error Handling
- HTTP appropriate status codes
- Meaningful error messages
- Validation at boundaries

---

## 🔍 Debugging Tips

### 1. Enable Logging
```python
import logging
logging.basicConfig(level=logging.DEBUG)
```

### 2. Check Database
```bash
mysql -u root -p cybercrime_db
SELECT * FROM users;
```

### 3. Inspect Network
Browser DevTools → Network tab → inspect requests

### 4. API Documentation
Visit http://localhost:8000/docs for interactive API explorer

### 5. Terminal Logs
FastAPI server logs all requests and errors

---

## 📞 Architecture Support

For questions about specific components:
- **Database**: See `database.py`
- **Models**: See `models.py`
- **API Routes**: See `main.py`
- **Frontend**: See `static/app.js`
- **Auth**: See `auth.py`

---

**Architecture Last Updated**: 2024-05-30
**Version**: 1.0.0
