'''
Filename: caConnect.py
Author: Austin Richardson
Creating date:  3/30/2025
'''

#   Call this function to connect to the database
def dbConnection(fHost = "localhost", fPort = 3307, fUser = "root", fPassword = ""):
    import mariadb
    import sys
    # Connect to MariaDB Platform
    try:
        conn = mariadb.connect(
            host= fHost,
            port= fPort,
            user= fUser,
            password= fPassword)
        return conn
    except mariadb.Error as e:
        print(f"Error connecting to the database: {e}")
        sys.exit(1)
        return