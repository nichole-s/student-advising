import csv
import os

dirname = os.path.dirname(__file__)
filename = os.path.join(dirname, 'classListControl.csv')

reader = csv.reader(filename, delimiter=',')
with open(filename, 'r',newline='') as csvfile:
    spamreader = csv.reader(csvfile, quotechar='|')
    rowArray = []
    newArray = []
    for row in spamreader:
        rowArray.append(row)
    for i in rowArray:
        test = ", ".join(i)
        if "<" in test:
            first_index = test.index("<") + 1
            second_index = test.index(">")
            new_test = test[first_index:second_index] + test[second_index + 3:]
            new_test = new_test.split(',')
            newArray.append(new_test)
    print(newArray)
    
with open(filename, 'w', newline='') as csvfile:
    writer = csv.writer(csvfile, quoting=csv.QUOTE_ALL)
    writer.writerows(newArray)