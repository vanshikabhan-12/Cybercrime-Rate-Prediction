from pydantic import BaseModel, EmailStr
from datetime import datetime
from typing import Optional, List

class UserRegister(BaseModel):
    email: EmailStr
    username: str
    password: str
    full_name: str

class UserLogin(BaseModel):
    email: str
    password: str

class UserResponse(BaseModel):
    id: int
    email: str
    username: str
    full_name: str
    is_admin: bool
    created_at: datetime

    class Config:
        from_attributes = True

class Token(BaseModel):
    access_token: str
    token_type: str
    user: UserResponse

class CrimeResponse(BaseModel):
    id: int
    city: str
    state: str
    year: int
    crime_type: str
    reported_cases: int
    solved_cases: int
    unsolved_cases: int
    monetary_loss: float
    victim_age: int
    victim_gender: str
    victim_profession: str
    population: int
    literacy_rate: float
    internet_penetration: float
    unemployment_rate: float

    class Config:
        from_attributes = True

class CrimeReportCreate(BaseModel):
    city: str
    crime_type: str
    description: str
    severity: str

class CrimeReportResponse(BaseModel):
    id: int
    user_id: int
    city: str
    crime_type: str
    description: str
    severity: str
    created_at: datetime

    class Config:
        from_attributes = True

class MessageCreate(BaseModel):
    content: str

class MessageResponse(BaseModel):
    id: int
    user_id: int
    content: str
    created_at: datetime

    class Config:
        from_attributes = True

class PredictionResponse(BaseModel):
    id: int
    city: str
    state: str
    crime_type: str
    predicted_cases: int
    confidence: float
    created_at: datetime

    class Config:
        from_attributes = True

class DashboardStats(BaseModel):
    total_crimes: int
    total_reports: int
    total_users: int
    top_crime_type: str
    total_monetary_loss: float
