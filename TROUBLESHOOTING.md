# Troubleshooting Guide

Common issues and solutions when running the Cybercrime Rate Prediction system.

---

## 🔧 Installation Issues

### Issue: "Python not found" or "pip not found"

**Problem**: Python or pip is not installed or not in PATH

**Solutions**:
```bash
# Check if Python is installed
python --version

# If not found, download from python.org
# Make sure to check "Add Python to PATH" during installation

# After installing, restart terminal/command prompt
```

---

### Issue: "ModuleNotFoundError: No module named 'fastapi'"

**Problem**: Dependencies not installed or virtual environment not activated

**Solutions**:
```bash
# 1. Check if virtual environment is activated
# You should see (venv) at the start of your prompt

# 2. If not activated:
venv\Scripts\activate  # Windows
source venv/bin/activate  # macOS/Linux

# 3. Install dependencies
pip install -r requirements.txt

# 4. Verify installation
pip list
# Should show: fastapi, uvicorn, sqlalchemy, etc.
```

---

### Issue: "Permission denied" when creating venv

**Problem**: No write permissions in directory

**Solutions**:
```bash
# 1. Run terminal as Administrator (Windows)
# Right-click → Run as administrator

# 2. Or use a different directory
# Move project to a writable location
```

---

## 💾 Database Issues

### Issue: "Can't connect to MySQL server"

**Problem**: MySQL is not running or wrong credentials

**Solutions**:

#### Windows
```bash
# 1. Check if MySQL is running
# Services → Search for "MySQL80" → Should be "Running"
# If not: Right-click → Start

# 2. Or use command line:
net start MySQL80  # Start MySQL
net stop MySQL80   # Stop MySQL
```

#### macOS
```bash
# 1. Check status
brew services list

# 2. Start MySQL
brew services start mysql

# 3. Stop MySQL
brew services stop mysql
```

#### Linux
```bash
# 1. Check status
sudo systemctl status mysql

# 2. Start MySQL
sudo systemctl start mysql

# 3. Stop MySQL
sudo systemctl stop mysql
```

---

### Issue: "Access denied for user 'root'@'localhost'"

**Problem**: Wrong MySQL password in .env file

**Solutions**:
```bash
# 1. Check your .env file
# It should look like:
# DATABASE_URL=mysql+mysql-connector-python://root:YOUR_PASSWORD@localhost/cybercrime_db

# 2. Test MySQL connection
mysql -u root -p
# Enter your password

# 3. If you forgot password, reset it:
# Windows: Look for MySQL documentation on password reset
# macOS/Linux: sudo mysqld_safe --skip-grant-tables
```

---

### Issue: "Database 'cybercrime_db' doesn't exist"

**Problem**: Database not created before running application

**Solutions**:
```bash
# 1. Create database using command line
mysql -u root -p -e "CREATE DATABASE cybercrime_db;"

# 2. Or manually create:
mysql -u root -p
# Then in MySQL shell:
CREATE DATABASE cybercrime_db;
USE cybercrime_db;
EXIT;

# 3. The application should auto-create tables on first run
# But if not, run:
python load_data.py
```

---

### Issue: "No module named 'mysql.connector'"

**Problem**: MySQL connector not installed

**Solutions**:
```bash
# Reinstall all requirements
pip install --upgrade -r requirements.txt

# Or just install connector
pip install mysql-connector-python==8.2.0
```

---

## 🌐 Server Issues

### Issue: "Uvicorn not found"

**Problem**: Uvicorn not installed

**Solutions**:
```bash
# Make sure virtual environment is activated
venv\Scripts\activate  # Windows

# Install uvicorn
pip install uvicorn==0.24.0

# Or reinstall all requirements
pip install -r requirements.txt
```

---

### Issue: "Port 8000 already in use"

**Problem**: Another application is using port 8000

**Solutions**:
```bash
# 1. Use a different port
python -m uvicorn main:app --reload --port 8001

# 2. Or find and kill the process using port 8000
# Windows:
netstat -ano | findstr :8000  # Find process ID
taskkill /PID <PID> /F  # Kill the process

# 3. Or stop the other application
```

---

### Issue: "ModuleNotFoundError" when running load_data.py

**Problem**: Virtual environment not activated or dependencies missing

**Solutions**:
```bash
# 1. Activate virtual environment first
venv\Scripts\activate  # Windows
source venv/bin/activate  # macOS/Linux

# 2. Then run the script
python load_data.py

# 3. You should see:
# "Loaded 100 records..."
# "Successfully loaded 1000 crime records!"
```

---

### Issue: Application starts but won't accept requests

**Problem**: Application running but server not responding

**Solutions**:
```bash
# 1. Check terminal output for errors
# You should see:
# "Uvicorn running on http://0.0.0.0:8000"
# "Application startup complete"

# 2. Try accessing health endpoint
# http://localhost:8000/health

# 3. Check API documentation
# http://localhost:8000/docs

# 4. If still not working, restart server
# Press CTRL+C to stop
# Run again: python -m uvicorn main:app --reload
```

---

## 🌐 Frontend Issues

### Issue: "Cannot GET /static/index.html"

**Problem**: Static files not found

**Solutions**:
```bash
# 1. Check that static folder exists
# You should have: static/
#                 ├── index.html
#                 ├── style.css
#                 └── app.js

# 2. Verify file paths are correct
# From project root

# 3. Restart server and try again
# CTRL+C then: python -m uvicorn main:app --reload
```

---

### Issue: Styling looks broken or buttons don't work

**Problem**: CSS or JavaScript not loading

**Solutions**:
```bash
# 1. Open Browser DevTools (F12)
# Check Network tab for 404 errors on CSS/JS files

# 2. Clear browser cache
# Ctrl+Shift+Delete → Clear cache

# 3. Hard refresh
# Ctrl+Shift+R (or Cmd+Shift+R on Mac)

# 4. Try a different browser
# Firefox, Chrome, Edge, Safari
```

---

### Issue: "Login fails" or "Email already registered"

**Problem**: User already exists or database issue

**Solutions**:
```bash
# 1. Try registering with different email
# test@example.com → test2@example.com

# 2. Check if database has data
mysql -u root -p cybercrime_db
SELECT * FROM users;  # See registered users

# 3. Clear users if needed (for testing)
mysql -u root -p cybercrime_db
DELETE FROM users;  # Start fresh

# 4. Reload data
python load_data.py
```

---

### Issue: Filtering doesn't work in Crime Data tab

**Problem**: API call failing or bad filter parameters

**Solutions**:
```bash
# 1. Check browser console (F12 → Console)
# Look for error messages

# 2. Check if data was loaded
# Go to API docs: http://localhost:8000/docs
# Try GET /crimes endpoint manually

# 3. Verify database has crime data
mysql -u root -p cybercrime_db
SELECT COUNT(*) FROM crimes;  # Should show > 0

# 4. Check network requests
# Browser DevTools → Network tab
# Look for failed requests
```

---

## 🔐 Authentication Issues

### Issue: "Invalid token" error

**Problem**: JWT token expired or invalid

**Solutions**:
```bash
# 1. Clear browser storage and login again
# F12 → Application → Storage → Local Storage → Clear

# 2. Or manually remove token
# Browser Console:
localStorage.removeItem('token');

# 3. Login again to get new token
# Token expires after 30 minutes

# 4. Check token in DevTools
# Application → Local Storage → token value
```

---

### Issue: "Admin access required"

**Problem**: User is not an admin

**Solutions**:
```bash
# 1. Create an admin user:
mysql -u root -p cybercrime_db
UPDATE users SET is_admin = 1 WHERE id = 1;

# 2. Or use API (if already admin):
# POST /admin/users/{user_id}/promote

# 3. Logout and login again to refresh permissions
```

---

### Issue: "Unauthorized" when making requests

**Problem**: Missing or invalid JWT token

**Solutions**:
```bash
# 1. Make sure you're logged in
# You should see the Dashboard tab, not login form

# 2. If page loaded but requests fail:
# F12 → Console → Check for errors

# 3. Check token in localStorage:
# F12 → Application → Storage → Local Storage
# Should have 'token' key with a value

# 4. Try logging in again
# Some browsers clear localStorage unexpectedly
```

---

## 📊 Data Issues

### Issue: CSV data not loading

**Problem**: CSV file not found or malformed

**Solutions**:
```bash
# 1. Check file exists
# Project root should have: cybercrime_db.csv

# 2. Run data loader
python load_data.py

# 3. Check output for errors
# Should show "Successfully loaded X records"

# 4. Verify data in database
mysql -u root -p cybercrime_db
SELECT COUNT(*) FROM crimes;  # Should show > 1000

# 5. If file corrupt, check original
# Make sure cybercrime_db.csv hasn't been modified
```

---

### Issue: No data appears in filters

**Problem**: Data not loaded or filters not working

**Solutions**:
```bash
# 1. Verify data loaded
python load_data.py

# 2. Check database directly
mysql -u root -p cybercrime_db
SELECT DISTINCT city FROM crimes LIMIT 5;

# 3. Test API endpoint directly
# Go to http://localhost:8000/docs
# Try GET /crimes endpoint

# 4. If API returns data but frontend doesn't:
# Check browser console for JavaScript errors
# F12 → Console tab
```

---

## 🔍 Debug Tips

### Enable Detailed Error Messages

```bash
# 1. Check terminal output
# FastAPI server logs all requests and errors

# 2. Open browser DevTools
# F12 → Console tab → See JavaScript errors
# F12 → Network tab → See API requests/responses

# 3. Check MySQL logs
# Usually in MySQL installation directory
```

### Test API Endpoint Directly

```bash
# Using curl in PowerShell:
curl -Method POST -Uri "http://localhost:8000/api/auth/login" `
  -Headers @{"Content-Type"="application/json"} `
  -Body '{"email":"test@example.com","password":"test123"}'

# Or use the interactive API docs at /docs
```

### Verify Environment Setup

```bash
# Check Python version
python --version  # Should be 3.8+

# Check MySQL version
mysql --version  # Should be 8.0+

# Check pip packages
pip list  # Should show all required packages

# Check virtual environment
which python  # Should show path in venv folder
```

---

## 🆘 When All Else Fails

### Complete Fresh Start

```bash
# 1. Stop all services
# Close terminal/server (CTRL+C)
# Stop MySQL service

# 2. Delete database
mysql -u root -p -e "DROP DATABASE cybercrime_db;"

# 3. Delete virtual environment
rmdir /s venv  # Windows
rm -rf venv    # macOS/Linux

# 4. Restart from beginning
# Follow QUICKSTART.md instructions step-by-step

# 5. Don't skip any steps!
```

---

### Getting Help

If you're stuck:

1. **Read Error Message Carefully**
   - Most errors tell you what's wrong
   - Note the file and line number

2. **Check Documentation**
   - README.md - Full documentation
   - QUICKSTART.md - Step-by-step setup
   - API_ENDPOINTS.md - API reference

3. **Search Online**
   - Copy error message to Google
   - Usually someone else had same issue

4. **Check Code**
   - Error often points to problematic file
   - Read the relevant section of code

5. **Test Incrementally**
   - Test each component separately
   - API health check: GET /health
   - Database: mysql -u root -p cybercrime_db

---

## 📋 Common Solutions Checklist

- [ ] Virtual environment activated? (see `(venv)` in prompt)
- [ ] .env file created and configured?
- [ ] MySQL running and accepting connections?
- [ ] Database created: `cybercrime_db`?
- [ ] Data loaded: `python load_data.py` completed?
- [ ] Port 8000 available (no other apps using it)?
- [ ] All dependencies installed: `pip install -r requirements.txt`?
- [ ] Browser cache cleared (Ctrl+Shift+Delete)?
- [ ] Tried accessing /docs endpoint?
- [ ] Checked browser DevTools console (F12)?

---

## 📞 Still Having Issues?

If the above doesn't help:

1. **Document the problem**
   - What were you doing?
   - What error appeared?
   - Which file/line?

2. **Check logs**
   - Terminal output from FastAPI server
   - Browser console (F12)
   - MySQL error logs

3. **Try the nuclear option**
   - Complete fresh start (see above)
   - Fresh database
   - Fresh virtual environment

4. **Read the documentation again**
   - Sometimes the answer is there
   - Don't skip the details

---

**Last Updated**: 2024-05-30
**Version**: 1.0.0
