#  Import libraries
import mariadb
import csv
import numpy as np
import datetime

# Import functions
from caConnect import dbConnection
#  Connect to the college advising database and create a cursor
conn = dbConnection()
cur = conn.cursor()

cur.execute("use college_advising")

cur.execute("select first_name, last_name, student_email, star_id from students;")

for (first_name, last_name, student_email, star_id) in cur:
    print(f"First Name:\t{first_name}\nLast Name:\t{last_name}\nemail:\t\t{student_email}\nStar ID:\t{star_id}\n")
    
"""x = datetime.datetime.now()

print(x.strftime("%A"))"""
'''
thoughts for password reset

Discuss with teamates format for dates
''' 
# INSERT INTO Users (first_name, last_name, email, password_hash)