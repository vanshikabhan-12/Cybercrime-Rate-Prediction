# Quick Start Guide

Get the Cybercrime Rate Prediction system running in 5 minutes!

## ⚡ TL;DR (Quick Setup)

```bash
# 1. Create virtual environment
python -m venv venv
venv\Scripts\activate  # Windows
# source venv/bin/activate  # macOS/Linux

# 2. Install dependencies
pip install -r requirements.txt

# 3. Setup .env file
copy .env.example .env
# Edit .env with your MySQL credentials

# 4. Create database
mysql -u root -p -e "CREATE DATABASE cybercrime_db;"

# 5. Load data
python load_data.py

# 6. Start server
python -m uvicorn main:app --reload

# 7. Open browser
# Frontend: http://localhost:8000/static/index.html
# API Docs: http://localhost:8000/docs
```

## 📋 Detailed Steps

### Step 1: Prerequisites Check

Verify you have installed:
```bash
python --version        # Should be 3.8+
mysql --version         # Should be 8.0+
pip --version           # Python package manager
```

### Step 2: Project Setup

```bash
# Navigate to project
cd Cybercrime-Rate-Prediction

# Create virtual environment
python -m venv venv

# Activate virtual environment
# Windows:
venv\Scripts\activate

# macOS/Linux:
source venv/bin/activate
```

You should see `(venv)` in your terminal prompt.

### Step 3: Install Requirements

```bash
pip install -r requirements.txt
```

Expected output:
```
Successfully installed fastapi-0.104.1 uvicorn-0.24.0 sqlalchemy-2.0.23 ...
```

### Step 4: Configure Database

#### Create .env file
```bash
copy .env.example .env
```

Edit `.env` with your MySQL credentials:
```env
DATABASE_URL=mysql+mysql-connector-python://root:root@localhost/cybercrime_db
SECRET_KEY=your-random-secret-key-here-use-python-secrets
ALGORITHM=HS256
ACCESS_TOKEN_EXPIRE_MINUTES=30
```

Generate a secure SECRET_KEY:
```bash
python -c "import secrets; print(secrets.token_urlsafe())"
```

#### Create Database

Option A (Command line):
```bash
mysql -u root -p -e "CREATE DATABASE cybercrime_db;"
```

Option B (MySQL client):
```sql
mysql -u root -p
> CREATE DATABASE cybercrime_db;
> EXIT;
```

### Step 5: Load Sample Data

```bash
python load_data.py
```

Expected output:
```
Loaded 100 records...
Loaded 200 records...
Successfully loaded 1000 crime records!
```

### Step 6: Start the Server

```bash
python -m uvicorn main:app --reload --host 0.0.0.0 --port 8000
```

Expected output:
```
INFO:     Application startup complete
INFO:     Uvicorn running on http://0.0.0.0:8000 (Press CTRL+C to quit)
INFO:     Started server process [1234]
```

### Step 7: Access the Application

**Frontend**: http://localhost:8000/static/index.html
**API Documentation**: http://localhost:8000/docs

### Step 8: Test the Application

1. **Register a user**
   - Go to frontend URL
   - Click "Register here"
   - Fill in details and register

2. **View Dashboard**
   - After login, you'll see dashboard stats
   - View total crimes, reports, users

3. **Explore Crime Data**
   - Click "Crime Data" tab
   - Filter by city, state, or crime type

4. **Create a Report**
   - Click "Dashboard" tab
   - Fill "Report a Crime" form
   - Submit

5. **View Predictions**
   - Click "Predictions" tab
   - See AI predictions (baseline model)

6. **Post Community Message**
   - Click "Community" tab
   - Post a message
   - See other users' messages

## 🔧 Troubleshooting

### Problem: "MySQL connection refused"

**Solution:**
```bash
# Check if MySQL is running
# Windows: Services → MySQL80
# macOS: brew services list
# Linux: sudo systemctl status mysql

# Start MySQL
# Windows: Services → MySQL80 → Start
# macOS: brew services start mysql
# Linux: sudo systemctl start mysql
```

### Problem: "No module named 'fastapi'"

**Solution:**
```bash
# Make sure you activated venv
venv\Scripts\activate  # Windows
source venv/bin/activate  # macOS/Linux

# Then install again
pip install -r requirements.txt
```

### Problem: "Database 'cybercrime_db' doesn't exist"

**Solution:**
```bash
# Create the database
mysql -u root -p -e "CREATE DATABASE cybercrime_db;"

# Then run load_data.py
python load_data.py
```

### Problem: "CORS error in browser"

**Solution:**
CORS is already enabled. Clear browser cache:
- Press F12 (DevTools)
- Network tab → Disable cache
- Reload page

### Problem: "Port 8000 already in use"

**Solution:**
```bash
# Use a different port
python -m uvicorn main:app --reload --port 8001

# Access at http://localhost:8001
```

## 📚 What's Included?

- ✅ User authentication (Register/Login)
- ✅ Crime data dashboard with stats
- ✅ Advanced crime data filtering
- ✅ Crime prediction system
- ✅ Crime reporting system
- ✅ Community messaging
- ✅ Admin panel
- ✅ 1000+ crime records (pre-loaded)
- ✅ Automatic database migrations

## 🎯 Next Steps

1. **Explore the API**
   - Go to http://localhost:8000/docs
   - Try different endpoints

2. **Read the Code**
   - `main.py` - API routes
   - `models.py` - Database models
   - `static/app.js` - Frontend logic

3. **Add Features**
   - Add visualization (Chart.js)
   - Improve predictions (ML)
   - Add email notifications

4. **Deploy**
   - Docker containerization
   - Cloud deployment (Heroku, AWS, etc.)

## 💡 Tips

- **Enable auto-reload**: `--reload` flag reloads on code changes
- **Check logs**: Terminal shows all requests and errors
- **API Docs**: Always available at `/docs` endpoint
- **Database**: Use MySQL client to inspect data directly
- **Frontend**: Browser DevTools (F12) shows network requests

## 📞 Need Help?

1. Check README.md for full documentation
2. Review error messages in terminal
3. Check browser console (F12)
4. Verify .env file configuration
5. Ensure MySQL is running

---

**You're all set! Happy learning! 🚀**
