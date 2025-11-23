"""
Filename: caCommaDelimitedFormat.py
Author: Austin Richardson
Creating date:  3/25/2025
"""
def csvStudentFormat(fFilename):
   try:
      print(fFilename) # Quick test to see what the file path given is
      #  Import libraries
      import mariadb
      import csv
      import numpy as np

      # Import functions
      from caConnect import dbConnection
      #  Connect to the college advising database and create a cursor
      conn = dbConnection()
      cur = conn.cursor()
      #  Create a temporary array
      tmpArray = []
      formatArray = []
   
      #  Read the tmpStudentList file
      with open(fFilename, newline='') as csvfile:
         spamreader = csv.reader(csvfile, delimiter=',') # I think if the user puts a , into any of the fields it will break
         for row in spamreader:
                if spamreader.line_num == 1:
                    continue  # Skip header if present
                tmpArray.append(row)
                
      for i in tmpArray:   # Go through each value in the array, and join them together
         tmp = ",".join(i)
         if "<" in tmp: #  Remove the unneded data from the email field
            first_index = tmp.index("<") + 1
            second_index = tmp.index(">")
            new_test = tmp[first_index:second_index] + tmp[second_index + 2:]
            new_test = new_test.split(',')
         else:
            new_test = tmp.split(',')
         formatArray.append(new_test)
         
      cur.execute("USE college_advising;")   # activate the college_advising database
        
      for x in formatArray:
         sql = f"""INSERT INTO students (student_email, first_name, last_name, star_id)
                     VALUES {tuple(x)}
                     ON DUPLICATE KEY UPDATE
                     student_email = "{x[0]}",
                     first_name = "{x[1]}",
                     last_name = "{x[2]}";"""
         print(sql)
         cur.execute(sql)
   
      #  Close the connection
      cur.close
      conn.close

      msg = "CSV file succesfully imported :)"
      return msg
   
   except mariadb.Error as e:
      msg = f"Error in mariaDB: {e}"
      return msg
      
      
# Entry point if called from shell
if __name__ == "__main__":
    import sys
    if len(sys.argv) < 2:
        print("Usage: python caCommaDelimitedFormat.py <path_to_csv>")
    else:
        filename = sys.argv[1]
        result = csvStudentFormat(filename)
        print(result)