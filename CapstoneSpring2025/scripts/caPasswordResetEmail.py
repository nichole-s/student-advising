#Import libraries
import mariadb
import smtplib
from email.message import EmailMessage
from caConnect import dbConnection
import os

def send_email(textfile, me, you):
    # Textfile = file path for email message
    # me = sender email
    # you = reciver email
    try:
        #Connect to the database
        conn = dbConnection()
        curr = conn.cursor()
        curr.execute("USE college_advising;")
        
        # Fetch email addresses from the database
        curr.execute("SELECT email FROM users where email = 'AustinRichardson2000@hotmail.com'")
        
        # Open a plain text file whose name is in the textfile for reading
        with open(textfile) as fp:
            #   Create a text message
            msg = EmailMessage()
            msg.set_content(fp.read())

        msg['Subject'] = f'The contents of {textfile}'
        msg['From'] = me
        msg['To'] = you
        
        #   Send the message via our own SMTP server
        s = smtplib.SMTP('localhost')
        s.send_message(msg)
        s.quit()
        
    except mariadb.Error as e:
        print(f"Database error: {e}")

dirname = os.path.dirname(__file__)
filename = os.path.join(dirname, "emailTest.txt")

send_email(filename, "atcCollegeAdvising@gmail.com", "AustinRichardson2000@hotmail.com")