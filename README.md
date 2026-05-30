# Cybercrime Rate Prediction System

A modern web application for analyzing, predicting, and reporting cybercrime data. Built with FastAPI backend and vanilla JavaScript frontend.

## 📋 Table of Contents
- [Project Overview](#project-overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Installation](#installation)
- [Database Setup](#database-setup)
- [Running the Application](#running-the-application)
- [API Documentation](#api-documentation)
- [Project Structure](#project-structure)
- [How It Works](#how-it-works)
- [Learning Resources](#learning-resources)

## 📌 Project Overview

This is a **complete rewrite** of the original PHP-based cybercrime prediction system into a modern Python FastAPI application. The goal is to provide a clean, educational baseline for students to understand modern web development practices.

### What Changed
- **Backend**: PHP → **FastAPI** (Python)
- **Frontend**: Rewritten in modern vanilla JavaScript
- **Database**: MySQL (kept the same for compatibility)
- **Architecture**: Microservices-ready with clean API design

## ✨ Features

### User Authentication
- Register new users with email/username
- Secure login with JWT tokens
- Session management with browser localStorage
- Password hashing with bcrypt

### Crime Data Dashboard
- Real-time statistics (total crimes, reports, users, monetary loss)
- Crime data explorer with advanced filtering
- Filter by: city, state, crime type, year
- Interactive data visualization tables

### Crime Reporting System
- Users can report crimes in their area
- Severity levels: LOW, MEDIUM, HIGH
- Track personal reports
- Admin can view all reports

### Predictions
- ML-based crime predictions (simple baseline model)
- Predict crimes by city, state, and type
- Confidence scores for predictions
- Historical prediction tracking

### Social Features
- Community message board
- Share thoughts and information
- Real-time message display
- User-friendly interface

### Admin Panel
- View all crime reports system-wide
- Create new predictions
- Promote users to admin
- Manage crime records
- Full system control

## 🛠️ Tech Stack

| Component | Technology |
|-----------|-----------|
| **Backend Framework** | FastAPI 0.104.1 |
| **Language** | Python 3.8+ |
| **Database** | MySQL 8.0+ |
| **ORM** | SQLAlchemy 2.0 |
| **Authentication** | JWT + bcrypt |
| **Frontend** | HTML5, CSS3, Vanilla JavaScript |
| **Server** | Uvicorn ASGI |

## 📦 Installation

### Prerequisites
- Python 3.8 or higher
- MySQL Server (or MySQL Community Edition)
- pip (Python package manager)

### Step 1: Clone/Navigate to Project
```bash
cd Cybercrime-Rate-Prediction
```

### Step 2: Create Virtual Environment
```bash
# On Windows
python -m venv venv
venv\Scripts\activate

# On macOS/Linux
python3 -m venv venv
source venv/bin/activate
```

### Step 3: Install Dependencies
```bash
pip install -r requirements.txt
```

### Step 4: Configure Environment Variables
```bash
# Copy example to actual .env file
cp .env.example .env

# Edit .env with your settings
# Database: mysql+mysql-connector-python://username:password@localhost/cybercrime_db
# SECRET_KEY: Generate a random secure key (use: python -c "import secrets; print(secrets.token_urlsafe())")
```

## 🗄️ Database Setup

### Option A: Using MySQL GUI (phpMyAdmin/MySQL Workbench)

1. **Open MySQL Client**
```sql
mysql -u root -p
```

2. **Create Database**
```sql
CREATE DATABASE cybercrime_db;
USE cybercrime_db;
```

3. **Run the Application** (it will auto-create tables)

### Option B: Command Line
```bash
mysql -u root -p -e "CREATE DATABASE cybercrime_db;"
```

### Load Crime Data
After tables are created:
```bash
python load_data.py
```

This loads all crime records from `cybercrime_db.csv` into the database.

## 🚀 Running the Application

### Start the FastAPI Server
```bash
# Development mode (with auto-reload)
python -m uvicorn main:app --reload --host 0.0.0.0 --port 8000

# Production mode
python -m uvicorn main:app --host 0.0.0.0 --port 8000
```

### Access the Application
- **Frontend**: http://localhost:8000/static/index.html
- **API Documentation (Swagger)**: http://localhost:8000/docs
- **Alternative API Docs (ReDoc)**: http://localhost:8000/redoc

## 📚 API Documentation

### Base URL
```
http://localhost:8000/api
```

### Authentication Endpoints

#### Register User
```http
POST /auth/register
Content-Type: application/json

{
  "email": "user@example.com",
  "username": "username",
  "password": "password123",
  "full_name": "John Doe"
}

Response: { "access_token": "token", "token_type": "bearer", "user": {...} }
```

#### Login
```http
POST /auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password123"
}

Response: { "access_token": "token", "token_type": "bearer", "user": {...} }
```

#### Get Current User
```http
GET /auth/me
Authorization: Bearer {token}

Response: { "id": 1, "email": "...", "username": "...", "is_admin": false, ... }
```

### Crime Data Endpoints

#### Get All Crimes (with filters)
```http
GET /crimes?city=Mumbai&state=Maharashtra&crime_type=Phishing&year=2024
Authorization: Bearer {token}

Response: [{ "id": 1, "city": "Mumbai", "state": "Maharashtra", ... }]
```

#### Get Available Cities
```http
GET /crimes/cities
Authorization: Bearer {token}

Response: [{ "city": "Mumbai" }, { "city": "Delhi" }, ...]
```

#### Get Available States
```http
GET /crimes/states
Authorization: Bearer {token}

Response: [{ "state": "Maharashtra" }, { "state": "Delhi" }, ...]
```

#### Get Crime Types
```http
GET /crimes/types
Authorization: Bearer {token}

Response: [{ "type": "Phishing" }, { "type": "Ransomware" }, ...]
```

#### Get Dashboard Statistics
```http
GET /crimes/stats
Authorization: Bearer {token}

Response: {
  "total_crimes": 1000,
  "total_reports": 45,
  "total_users": 25,
  "top_crime_type": "Phishing",
  "total_monetary_loss": 5000000.50
}
```

### Crime Report Endpoints

#### Create Crime Report
```http
POST /reports
Authorization: Bearer {token}
Content-Type: application/json

{
  "city": "Mumbai",
  "crime_type": "Phishing",
  "description": "Suspicious email attempting to steal credentials",
  "severity": "HIGH"
}

Response: { "id": 1, "user_id": 1, "city": "Mumbai", ... }
```

#### Get User's Reports
```http
GET /reports
Authorization: Bearer {token}

Response: [{ "id": 1, "city": "Mumbai", ... }]
```

#### Delete Report
```http
DELETE /reports/{report_id}
Authorization: Bearer {token}

Response: { "message": "Report deleted" }
```

### Social Endpoints

#### Create Message
```http
POST /messages
Authorization: Bearer {token}
Content-Type: application/json

{
  "content": "Just experienced a ransomware attack!"
}

Response: { "id": 1, "user_id": 1, "content": "...", "created_at": "..." }
```

#### Get All Messages
```http
GET /messages?skip=0&limit=50
Authorization: Bearer {token}

Response: [{ "id": 1, "user_id": 1, "content": "...", "created_at": "..." }, ...]
```

### Prediction Endpoints

#### Create Prediction (Admin Only)
```http
POST /predictions
Authorization: Bearer {token}
Content-Type: application/json

{
  "city": "Mumbai",
  "state": "Maharashtra",
  "crime_type": "Phishing"
}

Response: { "id": 1, "city": "Mumbai", "predicted_cases": 120, "confidence": 0.75, ... }
```

#### Get All Predictions
```http
GET /predictions
Authorization: Bearer {token}

Response: [{ "id": 1, "city": "Mumbai", ... }]
```

### Admin Endpoints

#### Get All Users
```http
GET /admin/users
Authorization: Bearer {token}

Response: [{ "id": 1, "email": "...", "is_admin": true, ... }]
```

#### Promote User to Admin
```http
POST /admin/users/{user_id}/promote
Authorization: Bearer {token}

Response: { "message": "User promoted to admin" }
```

#### Delete Crime Record
```http
DELETE /admin/crimes/{crime_id}
Authorization: Bearer {token}

Response: { "message": "Crime record deleted" }
```

## 📁 Project Structure

```
Cybercrime-Rate-Prediction/
├── main.py                  # FastAPI application & all routes
├── models.py                # SQLAlchemy database models
├── schemas.py               # Pydantic validation schemas
├── database.py              # Database connection & session
├── config.py                # Configuration management
├── auth.py                  # Authentication & JWT logic
├── load_data.py             # Script to load CSV data
├── requirements.txt         # Python dependencies
├── .env.example              # Environment variables template
├── cybercrime_db.csv        # Crime data (source data)
├── static/
│   ├── index.html           # Main HTML page
│   ├── style.css            # Application styles
│   └── app.js               # Frontend JavaScript logic
├── README.md                # This file
└── .git/                    # Git repository
```

## 🔍 How It Works

### Architecture Flow

```
User Browser
     ↓
  [Frontend - HTML/CSS/JS]
     ↓ (HTTP Requests)
  [FastAPI Server - Port 8000]
     ↓ (CRUD Operations)
  [SQLAlchemy ORM]
     ↓ (SQL Queries)
  [MySQL Database]
```

### Authentication Flow

1. **Register/Login**: User submits credentials
2. **Backend**: Hashes password (bcrypt), creates JWT token
3. **Frontend**: Stores token in localStorage
4. **Subsequent Requests**: Include token in Authorization header
5. **Backend**: Validates token, returns user data or rejects request

### Crime Prediction Logic (Baseline)

```python
# Simple baseline model
historical_average = sum(past_cases) / len(past_cases)
predicted_cases = int(historical_average * 1.1)  # 10% increase
confidence = 0.75
```

**Note**: In production, replace with ML models (Prophet, ARIMA, or Neural Networks)

### Database Schema

```sql
users:
  - id (PRIMARY KEY)
  - email (UNIQUE)
  - username (UNIQUE)
  - hashed_password
  - full_name
  - is_admin
  - created_at

crimes:
  - id (PRIMARY KEY)
  - city, state, year
  - crime_type, reported_cases, solved_cases
  - monetary_loss, victim demographics
  - population, literacy_rate, internet_penetration

crime_reports:
  - id (PRIMARY KEY)
  - user_id (FOREIGN KEY)
  - city, crime_type, description, severity

messages:
  - id (PRIMARY KEY)
  - user_id (FOREIGN KEY)
  - content, created_at

predictions:
  - id (PRIMARY KEY)
  - city, state, crime_type
  - predicted_cases, confidence
  - created_at
```

## 📖 Learning Resources

### Understanding the Code

1. **FastAPI Basics**: Check `main.py` for route definitions
2. **Database Models**: See `models.py` for SQLAlchemy patterns
3. **Authentication**: Study `auth.py` for JWT implementation
4. **Frontend Logic**: Read `static/app.js` for fetch API usage

### Key Concepts Covered

- ✅ RESTful API design
- ✅ Database modeling with ORMs
- ✅ User authentication & JWT
- ✅ Password hashing & security
- ✅ API validation with Pydantic
- ✅ Frontend-backend communication
- ✅ CORS handling
- ✅ Admin/role-based access control
- ✅ Data pagination & filtering

### Recommended Next Steps

1. **Add ML Predictions**: Replace baseline with scikit-learn/Prophet
2. **Add Visualization**: Integrate Chart.js or Plotly for graphs
3. **Email Notifications**: Use SendGrid for alerts
4. **Docker**: Containerize app for deployment
5. **Tests**: Add pytest for unit & integration tests
6. **API Rate Limiting**: Protect endpoints from abuse
7. **Logging**: Implement logging for debugging
8. **Caching**: Add Redis for performance

## 🔒 Security Features

- **Password Hashing**: bcrypt with salt
- **JWT Tokens**: Secure token-based auth
- **SQL Injection Prevention**: SQLAlchemy parameterized queries
- **CORS**: Configurable cross-origin requests
- **Role-Based Access**: Admin-only endpoints
- **Input Validation**: Pydantic schemas

## 🐛 Common Issues & Solutions

### Issue: "Database connection refused"
```bash
# Check MySQL is running
# Windows: Services → MySQL80 → Start
# macOS: brew services start mysql
# Linux: sudo service mysql start

# Verify credentials in .env file
```

### Issue: "ModuleNotFoundError: No module named 'fastapi'"
```bash
# Activate virtual environment first
pip install -r requirements.txt
```

### Issue: "CORS error in browser console"
- CORS is already enabled in `main.py`
- Ensure frontend URL matches allowed origins

### Issue: "CSV file not found"
```bash
# Ensure cybercrime_db.csv is in project root
# Run: python load_data.py
```

## 📝 Sample Test Data

### Create Test User (via API)
```json
{
  "email": "student@example.com",
  "username": "student123",
  "password": "secure_password",
  "full_name": "John Student"
}
```

### Create Test Report (via API)
```json
{
  "city": "Mumbai",
  "crime_type": "Phishing",
  "description": "Received suspicious phishing email",
  "severity": "MEDIUM"
}
```

## 🤝 Contributing

This is a student project. Improvements welcome! Consider:
- Adding more crime data
- Improving ML models
- Adding visualization
- Writing tests
- Improving documentation

## 📄 License

Educational project for learning purposes.

## 🎓 Learning Objectives Achieved

By completing this project, you've learned:

- ✅ How to build RESTful APIs with FastAPI
- ✅ Database design and SQLAlchemy ORM
- ✅ User authentication with JWT tokens
- ✅ Frontend-backend integration
- ✅ Data validation with Pydantic
- ✅ Role-based access control
- ✅ CORS and security best practices
- ✅ CSV data loading and ETL
- ✅ Error handling and HTTP status codes
- ✅ Clean code architecture

## 📧 Support

For questions or issues:
1. Check the API documentation at `/docs`
2. Review error messages in console
3. Check database connection settings
4. Verify all dependencies are installed

---

**Happy Learning! 🚀**

Built with ❤️ for students learning modern web development.
