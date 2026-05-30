# Complete API Endpoints Reference

All endpoints require JWT authentication unless otherwise specified.

Base URL: `http://localhost:8000/api`

## 🔐 Authentication Endpoints

### Register New User
**POST** `/auth/register`

**No Auth Required**

**Request Body:**
```json
{
  "email": "user@example.com",
  "username": "username",
  "password": "password123",
  "full_name": "John Doe"
}
```

**Response (201):**
```json
{
  "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "token_type": "bearer",
  "user": {
    "id": 1,
    "email": "user@example.com",
    "username": "username",
    "full_name": "John Doe",
    "is_admin": false,
    "created_at": "2024-05-30T10:00:00"
  }
}
```

---

### Login User
**POST** `/auth/login`

**No Auth Required**

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response (200):**
```json
{
  "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "token_type": "bearer",
  "user": {
    "id": 1,
    "email": "user@example.com",
    "username": "username",
    "full_name": "John Doe",
    "is_admin": false,
    "created_at": "2024-05-30T10:00:00"
  }
}
```

---

### Get Current User
**GET** `/auth/me`

**Auth Required**

**Response (200):**
```json
{
  "id": 1,
  "email": "user@example.com",
  "username": "username",
  "full_name": "John Doe",
  "is_admin": false,
  "created_at": "2024-05-30T10:00:00"
}
```

---

## 📊 Crime Data Endpoints

### Get All Crimes (with filtering)
**GET** `/crimes`

**Auth Required**

**Query Parameters:**
- `city` (optional): Filter by city name
- `state` (optional): Filter by state name
- `crime_type` (optional): Filter by crime type
- `year` (optional): Filter by year
- `skip` (optional, default=0): Pagination offset
- `limit` (optional, default=100): Records per page

**Example Request:**
```
GET /crimes?city=Mumbai&state=Maharashtra&skip=0&limit=10
```

**Response (200):**
```json
[
  {
    "id": 1,
    "city": "Mumbai",
    "state": "Maharashtra",
    "year": 2024,
    "crime_type": "Financial Fraud",
    "reported_cases": 22,
    "solved_cases": 8,
    "unsolved_cases": 14,
    "monetary_loss": 1262214.68,
    "victim_age": 23,
    "victim_gender": "Other",
    "victim_profession": "Engineer",
    "population": 18349464,
    "literacy_rate": 63.04,
    "internet_penetration": 55.32,
    "unemployment_rate": 2.3
  }
]
```

---

### Get Available Cities
**GET** `/crimes/cities`

**Auth Required**

**Response (200):**
```json
[
  { "city": "Mumbai" },
  { "city": "Delhi" },
  { "city": "Bangalore" },
  { "city": "Hyderabad" }
]
```

---

### Get Available States
**GET** `/crimes/states`

**Auth Required**

**Response (200):**
```json
[
  { "state": "Maharashtra" },
  { "state": "Delhi" },
  { "state": "Karnataka" },
  { "state": "Telangana" }
]
```

---

### Get Crime Types
**GET** `/crimes/types`

**Auth Required**

**Response (200):**
```json
[
  { "type": "Financial Fraud" },
  { "type": "Data Breach" },
  { "type": "Ransomware" },
  { "type": "Phishing" },
  { "type": "Online Harassment" }
]
```

---

### Get Dashboard Statistics
**GET** `/crimes/stats`

**Auth Required**

**Response (200):**
```json
{
  "total_crimes": 1000,
  "total_reports": 45,
  "total_users": 25,
  "top_crime_type": "Phishing",
  "total_monetary_loss": 5000000.50
}
```

---

## 📝 Crime Report Endpoints

### Create Crime Report
**POST** `/reports`

**Auth Required**

**Request Body:**
```json
{
  "city": "Mumbai",
  "crime_type": "Phishing",
  "description": "Suspicious email attempting to steal credentials",
  "severity": "HIGH"
}
```

**Response (201):**
```json
{
  "id": 1,
  "user_id": 1,
  "city": "Mumbai",
  "crime_type": "Phishing",
  "description": "Suspicious email attempting to steal credentials",
  "severity": "HIGH",
  "created_at": "2024-05-30T10:00:00"
}
```

---

### Get User's Reports
**GET** `/reports`

**Auth Required**

(For admins: returns all reports; for users: returns only their reports)

**Response (200):**
```json
[
  {
    "id": 1,
    "user_id": 1,
    "city": "Mumbai",
    "crime_type": "Phishing",
    "description": "Suspicious email attempting to steal credentials",
    "severity": "HIGH",
    "created_at": "2024-05-30T10:00:00"
  }
]
```

---

### Delete Crime Report
**DELETE** `/reports/{report_id}`

**Auth Required**

(Only creator or admin can delete)

**Response (200):**
```json
{
  "message": "Report deleted"
}
```

**Error Response (404):**
```json
{
  "detail": "Report not found"
}
```

---

## 💬 Social/Message Endpoints

### Create Message
**POST** `/messages`

**Auth Required**

**Request Body:**
```json
{
  "content": "Just experienced a ransomware attack in our company!"
}
```

**Response (201):**
```json
{
  "id": 1,
  "user_id": 1,
  "content": "Just experienced a ransomware attack in our company!",
  "created_at": "2024-05-30T10:00:00"
}
```

---

### Get All Messages
**GET** `/messages`

**Auth Required**

**Query Parameters:**
- `skip` (optional, default=0): Pagination offset
- `limit` (optional, default=50): Records per page

**Response (200):**
```json
[
  {
    "id": 1,
    "user_id": 1,
    "content": "Just experienced a ransomware attack!",
    "created_at": "2024-05-30T10:00:00"
  }
]
```

---

## 🔮 Prediction Endpoints

### Create Prediction
**POST** `/predictions`

**Auth Required (Admin Only)**

**Request Body:**
```json
{
  "city": "Mumbai",
  "state": "Maharashtra",
  "crime_type": "Phishing"
}
```

**Response (201):**
```json
{
  "id": 1,
  "city": "Mumbai",
  "state": "Maharashtra",
  "crime_type": "Phishing",
  "predicted_cases": 120,
  "confidence": 0.75,
  "created_at": "2024-05-30T10:00:00"
}
```

**Error Response (403):**
```json
{
  "detail": "Admin access required"
}
```

---

### Get All Predictions
**GET** `/predictions`

**Auth Required**

**Response (200):**
```json
[
  {
    "id": 1,
    "city": "Mumbai",
    "state": "Maharashtra",
    "crime_type": "Phishing",
    "predicted_cases": 120,
    "confidence": 0.75,
    "created_at": "2024-05-30T10:00:00"
  }
]
```

---

## 👨‍💼 Admin Endpoints

### Get All Users
**GET** `/admin/users`

**Auth Required (Admin Only)**

**Response (200):**
```json
[
  {
    "id": 1,
    "email": "user@example.com",
    "username": "username",
    "full_name": "John Doe",
    "is_admin": false,
    "created_at": "2024-05-30T10:00:00"
  }
]
```

---

### Promote User to Admin
**POST** `/admin/users/{user_id}/promote`

**Auth Required (Admin Only)**

**Response (200):**
```json
{
  "message": "User promoted to admin"
}
```

---

### Delete Crime Record
**DELETE** `/admin/crimes/{crime_id}`

**Auth Required (Admin Only)**

**Response (200):**
```json
{
  "message": "Crime record deleted"
}
```

---

## ✅ Health Check Endpoints

### Root/Welcome
**GET** `/`

**No Auth Required**

**Response (200):**
```json
{
  "message": "Cybercrime Rate Prediction API",
  "version": "1.0.0"
}
```

---

### Health Check
**GET** `/health`

**No Auth Required**

**Response (200):**
```json
{
  "status": "ok"
}
```

---

## 📋 Common HTTP Status Codes

| Code | Meaning | Example |
|------|---------|---------|
| 200 | Success | GET request successful |
| 201 | Created | Resource created successfully |
| 400 | Bad Request | Invalid input data |
| 401 | Unauthorized | Missing/invalid token |
| 403 | Forbidden | Admin access required |
| 404 | Not Found | Resource doesn't exist |
| 500 | Server Error | Internal server error |

---

## 🔑 Authentication Header Format

All authenticated endpoints require this header:

```
Authorization: Bearer {access_token}
```

Example with curl:
```bash
curl -H "Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..." \
  http://localhost:8000/api/crimes
```

---

## 🧪 Testing with cURL

### Register
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "username": "testuser",
    "password": "pass123",
    "full_name": "Test User"
  }'
```

### Login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "pass123"
  }'
```

### Get Crimes (requires token)
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
  http://localhost:8000/api/crimes?city=Mumbai
```

---

## 📖 Interactive API Documentation

Access the interactive API documentation at:
- **Swagger UI**: http://localhost:8000/docs
- **ReDoc**: http://localhost:8000/redoc

These pages provide:
- ✅ Complete endpoint documentation
- ✅ Request/response examples
- ✅ Try-it-out functionality
- ✅ Schema definitions

---

## 🎯 API Usage Tips

1. **Pagination**: Use `skip` and `limit` for large datasets
2. **Filtering**: Combine multiple filters for precise searches
3. **Tokens**: Tokens expire after 30 minutes by default
4. **Errors**: Always check response status and error messages
5. **Rate Limiting**: Plan for potential future rate limits

---

**Last Updated**: 2024-05-30
