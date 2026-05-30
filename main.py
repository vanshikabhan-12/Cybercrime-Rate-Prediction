from fastapi import FastAPI, Depends, HTTPException, status
from fastapi.middleware.cors import CORSMiddleware
from fastapi.staticfiles import StaticFiles
from sqlalchemy.orm import Session
from datetime import timedelta
from database import engine, get_db, Base
from models import User, Crime, CrimeReport, Message, Prediction
from schemas import (
    UserRegister, UserLogin, Token, UserResponse,
    CrimeResponse, CrimeReportCreate, CrimeReportResponse,
    MessageCreate, MessageResponse, PredictionResponse, DashboardStats
)
from auth import hash_password, verify_password, create_access_token, get_current_user, get_admin_user
from config import get_settings
from sqlalchemy import func

# Create tables
Base.metadata.create_all(bind=engine)

app = FastAPI(title="Cybercrime Rate Prediction", version="1.0.0")
settings = get_settings()

# CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Mount static files
try:
    app.mount("/static", StaticFiles(directory="static"), name="static")
except Exception:
    pass

# ==================== AUTH ENDPOINTS ====================
@app.post("/api/auth/register", response_model=Token)
def register(user: UserRegister, db: Session = Depends(get_db)):
    existing_user = db.query(User).filter(User.email == user.email).first()
    if existing_user:
        raise HTTPException(status_code=400, detail="Email already registered")

    existing_username = db.query(User).filter(User.username == user.username).first()
    if existing_username:
        raise HTTPException(status_code=400, detail="Username already taken")

    db_user = User(
        email=user.email,
        username=user.username,
        hashed_password=hash_password(user.password),
        full_name=user.full_name
    )
    db.add(db_user)
    db.commit()
    db.refresh(db_user)

    access_token = create_access_token(data={"sub": db_user.id})
    return {
        "access_token": access_token,
        "token_type": "bearer",
        "user": db_user
    }

@app.post("/api/auth/login", response_model=Token)
def login(user: UserLogin, db: Session = Depends(get_db)):
    db_user = db.query(User).filter(User.email == user.email).first()
    if not db_user or not verify_password(user.password, db_user.hashed_password):
        raise HTTPException(status_code=401, detail="Invalid credentials")

    access_token = create_access_token(data={"sub": db_user.id})
    return {
        "access_token": access_token,
        "token_type": "bearer",
        "user": db_user
    }

@app.get("/api/auth/me", response_model=UserResponse)
def get_me(current_user: User = Depends(get_current_user)):
    return current_user

# ==================== CRIME DATA ENDPOINTS ====================
@app.get("/api/crimes", response_model=list[CrimeResponse])
def get_crimes(
    city: str = None,
    state: str = None,
    crime_type: str = None,
    year: int = None,
    skip: int = 0,
    limit: int = 100,
    db: Session = Depends(get_db)
):
    query = db.query(Crime)

    if city:
        query = query.filter(Crime.city.ilike(f"%{city}%"))
    if state:
        query = query.filter(Crime.state.ilike(f"%{state}%"))
    if crime_type:
        query = query.filter(Crime.crime_type.ilike(f"%{crime_type}%"))
    if year:
        query = query.filter(Crime.year == year)

    crimes = query.offset(skip).limit(limit).all()
    return crimes

@app.get("/api/crimes/cities")
def get_cities(db: Session = Depends(get_db)):
    cities = db.query(func.distinct(Crime.city)).all()
    return [{"city": c[0]} for c in cities if c[0]]

@app.get("/api/crimes/states")
def get_states(db: Session = Depends(get_db)):
    states = db.query(func.distinct(Crime.state)).all()
    return [{"state": s[0]} for s in states if s[0]]

@app.get("/api/crimes/types")
def get_crime_types(db: Session = Depends(get_db)):
    types = db.query(func.distinct(Crime.crime_type)).all()
    return [{"type": t[0]} for t in types if t[0]]

@app.get("/api/crimes/stats", response_model=DashboardStats)
def get_stats(db: Session = Depends(get_db)):
    total_crimes = db.query(func.count(Crime.id)).scalar() or 0
    total_reports = db.query(func.count(CrimeReport.id)).scalar() or 0
    total_users = db.query(func.count(User.id)).scalar() or 0
    total_loss = db.query(func.sum(Crime.monetary_loss)).scalar() or 0.0

    top_crime = db.query(Crime.crime_type, func.count(Crime.id)).group_by(
        Crime.crime_type
    ).order_by(func.count(Crime.id).desc()).first()

    return {
        "total_crimes": total_crimes,
        "total_reports": total_reports,
        "total_users": total_users,
        "top_crime_type": top_crime[0] if top_crime else "N/A",
        "total_monetary_loss": total_loss
    }

# ==================== CRIME REPORT ENDPOINTS ====================
@app.post("/api/reports", response_model=CrimeReportResponse)
def create_report(
    report: CrimeReportCreate,
    current_user: User = Depends(get_current_user),
    db: Session = Depends(get_db)
):
    db_report = CrimeReport(
        user_id=current_user.id,
        city=report.city,
        crime_type=report.crime_type,
        description=report.description,
        severity=report.severity
    )
    db.add(db_report)
    db.commit()
    db.refresh(db_report)
    return db_report

@app.get("/api/reports", response_model=list[CrimeReportResponse])
def get_reports(
    current_user: User = Depends(get_current_user),
    db: Session = Depends(get_db)
):
    if current_user.is_admin:
        reports = db.query(CrimeReport).all()
    else:
        reports = db.query(CrimeReport).filter(CrimeReport.user_id == current_user.id).all()
    return reports

@app.delete("/api/reports/{report_id}")
def delete_report(
    report_id: int,
    current_user: User = Depends(get_current_user),
    db: Session = Depends(get_db)
):
    report = db.query(CrimeReport).filter(CrimeReport.id == report_id).first()
    if not report:
        raise HTTPException(status_code=404, detail="Report not found")

    if report.user_id != current_user.id and not current_user.is_admin:
        raise HTTPException(status_code=403, detail="Not authorized")

    db.delete(report)
    db.commit()
    return {"message": "Report deleted"}

# ==================== SOCIAL ENDPOINTS ====================
@app.post("/api/messages", response_model=MessageResponse)
def create_message(
    message: MessageCreate,
    current_user: User = Depends(get_current_user),
    db: Session = Depends(get_db)
):
    db_message = Message(
        user_id=current_user.id,
        content=message.content
    )
    db.add(db_message)
    db.commit()
    db.refresh(db_message)
    return db_message

@app.get("/api/messages", response_model=list[MessageResponse])
def get_messages(skip: int = 0, limit: int = 50, db: Session = Depends(get_db)):
    messages = db.query(Message).order_by(Message.created_at.desc()).offset(skip).limit(limit).all()
    return messages

# ==================== PREDICTION ENDPOINTS ====================
@app.post("/api/predictions", response_model=PredictionResponse)
def create_prediction(
    city: str,
    state: str,
    crime_type: str,
    current_user: User = Depends(get_admin_user),
    db: Session = Depends(get_db)
):
    # Simple prediction logic - in production, use ML model
    historical_crimes = db.query(Crime).filter(
        Crime.city == city,
        Crime.crime_type == crime_type
    ).all()

    avg_cases = sum([c.reported_cases for c in historical_crimes]) / len(historical_crimes) if historical_crimes else 0

    db_prediction = Prediction(
        city=city,
        state=state,
        crime_type=crime_type,
        predicted_cases=int(avg_cases * 1.1),  # Simple 10% increase prediction
        confidence=0.75
    )
    db.add(db_prediction)
    db.commit()
    db.refresh(db_prediction)
    return db_prediction

@app.get("/api/predictions", response_model=list[PredictionResponse])
def get_predictions(db: Session = Depends(get_db)):
    predictions = db.query(Prediction).order_by(Prediction.created_at.desc()).all()
    return predictions

# ==================== ADMIN ENDPOINTS ====================
@app.get("/api/admin/users", response_model=list[UserResponse])
def get_all_users(current_user: User = Depends(get_admin_user), db: Session = Depends(get_db)):
    users = db.query(User).all()
    return users

@app.post("/api/admin/users/{user_id}/promote")
def promote_to_admin(
    user_id: int,
    current_user: User = Depends(get_admin_user),
    db: Session = Depends(get_db)
):
    user = db.query(User).filter(User.id == user_id).first()
    if not user:
        raise HTTPException(status_code=404, detail="User not found")

    user.is_admin = True
    db.commit()
    return {"message": "User promoted to admin"}

@app.delete("/api/admin/crimes/{crime_id}")
def delete_crime(
    crime_id: int,
    current_user: User = Depends(get_admin_user),
    db: Session = Depends(get_db)
):
    crime = db.query(Crime).filter(Crime.id == crime_id).first()
    if not crime:
        raise HTTPException(status_code=404, detail="Crime record not found")

    db.delete(crime)
    db.commit()
    return {"message": "Crime record deleted"}

# ==================== HEALTH CHECK ====================
@app.get("/")
def root():
    return {"message": "Cybercrime Rate Prediction API", "version": "1.0.0"}

@app.get("/health")
def health_check():
    return {"status": "ok"}

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)
