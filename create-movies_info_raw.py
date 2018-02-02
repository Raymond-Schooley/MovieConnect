import csv
import os

#MUST CHANGE THESE
imdbFile = 'C:\\...\\title.basics.tsv'
#full path or sql command (ex: mysql)
sqlLocation = 'C:\\...\\mysql.exe'
sqlUser = 'root'
sqlPass = '....'

#set up / clean database
sqlPrefix = sqlLocation + ' -u ' + sqlUser + ' -p' + sqlPass + ' -e '
os.system(sqlPrefix + "\"CREATE DATABASE imdb;\"")
os.system(sqlPrefix + "\"DROP TABLE imdb.movies_info_raw;\"")
os.system(sqlPrefix + "\"CREATE TABLE imdb.movies_info_raw (" \
          + "tconst CHAR(9) NOT NULL, " \
          + "primaryTitle VARCHAR(32) NOT NULL, " \
          + "originalTitle VARCHAR(32) NOT NULL, " \
          + "isAdult INT NOT NULL, " \
          + "startYear INT NOT NULL, " \
          + "endYear INT NOT NULL, " \
          + "runtimeMinutes INT NOT NULL, " \
          + "genres VARCHAR(32) NOT NULL, " \
          + "PRIMARY KEY (tconst)" \
          + ");\"")

with open(imdbFile, encoding="utf8") as tsvfile:
    reader = csv.reader(tsvfile, delimiter='\t')
    rowCount = 0
    rowsAdded = 0
    for row in reader:
        
        if rowCount % 100 == 0:
            print("%s movies kept/added out of %s total movies --- file progress: %.4f %%" % (rowsAdded, rowCount, (rowCount/8388816)*100) )
        
        #ignore 1st line of file
        rowCount += 1
        if rowCount == 1:
            continue

        #set up sql variables, fix invalid chars

        tconst = row[0]
        titleType = row[1].replace("'", "''").replace("\\", "\\\\")
        primaryTitle = row[2].replace("'", "''").replace("\\", "\\\\")
        originalTitle = row[3].replace("'", "''").replace("\\", "\\\\")
        isAdult = row[4]
        startYear = row[5]
        endYear = row[6]
        runtimeMinutes = row[7]
        genres = row[8].replace("'", "''").replace("\\", "\\\\")

        #clean up
        if "movie" not in titleType:
            continue
        if genres == '\\\\N':
            continue
        if startYear == '\\N':
            startYear = -1
        if endYear == '\\N':
            endYear = -1
        if runtimeMinutes == '\\N':
            continue

        sqlCommand = sqlPrefix + "\"INSERT INTO imdb.movies_info_raw VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');\"" % (tconst,
            primaryTitle, originalTitle, isAdult, startYear, endYear, runtimeMinutes, genres)
        os.system(sqlCommand)
        rowsAdded += 1
