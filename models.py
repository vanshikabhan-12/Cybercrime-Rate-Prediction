from sqlalchemy import Column, Integer, String, Float, DateTime, Boolean, ForeignKey, Text
from sqlalchemy.orm import relationship
from datetime import datetime
from database import Base

class User(Base):
    __tablename__ = "users"

    id = Column(Integer, primary_key=True, index=True)
    email = Column(String(255), unique=True, index=True)
    username = Column(String(255), unique=True, index=True)
    hashed_password = Column(String(255))
    full_name = Column(String(255))
    is_admin = Column(Boolean, default=False)
    created_at = Column(DateTime, default=datetime.utcnow)

    crime_reports = relationship("CrimeReport", back_populates="user")
    messages = relationship("Message", back_populates="user")

class Crime(Base):
    __tablename__ = "crimes"

    id = Column(Integer, primary_key=True, index=True)
    city = Column(String(255), index=True)
    state = Column(String(255), index=True)
    year = Column(Integer, index=True)
    crime_type = Column(String(255), index=True)
    reported_cases = Column(Integer)
    solved_cases = Column(Integer)
    unsolved_cases = Column(Integer)
    monetary_loss = Column(Float)
    victim_age = Column(Integer)
    victim_gender = Column(String(50))
    victim_profession = Column(String(255))
    population = Column(Integer)
    literacy_rate = Column(Float)
    internet_penetration = Column(Float)
    unemployment_rate = Column(Float)

class CrimeReport(Base):
    __tablename__ = "crime_reports"

    id = Column(Integer, primary_key=True, index=True)
    user_id = Column(Integer, ForeignKey("users.id"))
    city = Column(String(255))
    crime_type = Column(String(255))
    description = Column(Text)
    severity = Column(String(50))  # LOW, MEDIUM, HIGH
    created_at = Column(DateTime, default=datetime.utcnow)

    user = relationship("User", back_populates="crime_reports")

class Message(Base):
    __tablename__ = "messages"

    id = Column(Integer, primary_key=True, index=True)
    user_id = Column(Integer, ForeignKey("users.id"))
    content = Column(Text)
    created_at = Column(DateTime, default=datetime.utcnow)

    user = relationship("User", back_populates="messages")

class Prediction(Base):
    __tablename__ = "predictions"

    id = Column(Integer, primary_key=True, index=True)
    city = Column(String(255))
    state = Column(String(255))
    crime_type = Column(String(255))
    predicted_cases = Column(Integer)
    confidence = Column(Float)
    created_at = Column(DateTime, default=datetime.utcnow)
