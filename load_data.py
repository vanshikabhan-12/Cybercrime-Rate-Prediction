import csv
from database import SessionLocal, engine, Base
from models import Crime
import os

def load_crime_data():
    Base.metadata.create_all(bind=engine)
    db = SessionLocal()

    csv_path = "cybercrime_db.csv"

    if not os.path.exists(csv_path):
        print(f"Error: {csv_path} not found")
        return

    try:
        with open(csv_path, 'r', encoding='utf-8') as f:
            reader = csv.DictReader(f)
            count = 0

            for row in reader:
                crime = Crime(
                    city=row['city'],
                    state=row['state'],
                    year=int(row['year']),
                    crime_type=row['crime_type'],
                    reported_cases=int(row['reported_cases']),
                    solved_cases=int(row['solved_cases']),
                    unsolved_cases=int(row['unsolved_cases']),
                    monetary_loss=float(row['Monetary Loss (INR)']),
                    victim_age=int(row['Victim Age']),
                    victim_gender=row['Victim Gender'],
                    victim_profession=row['Victim Profession'],
                    population=int(row['population']),
                    literacy_rate=float(row['literacy_rate']),
                    internet_penetration=float(row['internet_penetration']),
                    unemployment_rate=float(row['unemployment_rate'])
                )
                db.add(crime)
                count += 1

                if count % 100 == 0:
                    db.commit()
                    print(f"Loaded {count} records...")

            db.commit()
            print(f"Successfully loaded {count} crime records!")

    except Exception as e:
        print(f"Error loading data: {e}")
        db.rollback()
    finally:
        db.close()

if __name__ == "__main__":
    load_crime_data()
