import csv
import os

#MUST CHANGE THESE
imdbFile = 'C:\\...\\name.basics.tsv'
#full path or sql command (ex: mysql)
sqlLocation = 'C:\\...\\mysql.exe'
sqlUser = 'root'
sqlPass = '.....'

#set up / clean database
sqlPrefix = sqlLocation + ' -u ' + sqlUser + ' -p' + sqlPass + ' -e '
os.system(sqlPrefix + "\"CREATE DATABASE imdb;\"")
os.system(sqlPrefix + "\"DROP TABLE imdb.names_raw;\"")
os.system(sqlPrefix + "\"CREATE TABLE imdb.names_raw (" \
          + "nconst CHAR(9) NOT NULL, " \
          + "primaryName VARCHAR(64) NOT NULL, " \
          + "birthYear INT NOT NULL, " \
          + "deathYear INT NOT NULL, " \
          + "primaryProfession VARCHAR(64) NOT NULL, " \
          + "knownForTitles VARCHAR(39) NOT NULL, " \
          + "PRIMARY KEY (nconst)" \
          + ");\"")

with open(imdbFile, encoding="utf8") as tsvfile:
    reader = csv.reader(tsvfile, delimiter='\t')
    rowCount = 0
    namesAdded = 0
    for row in reader:
        
        if rowCount % 100 == 0:
            print("%s names kept/added out of %s total names --- file progress: %.4f %%" % (namesAdded, rowCount, (rowCount/8388816)*100) )
        
        #ignore 1st line of file
        rowCount += 1
        if rowCount == 1:
            continue

        #set up sql variables, fix invalid chars
        nconst = row[0]
        primaryName = row[1].replace("'", "''")
        birthYear = row[2]
        deathYear = row[3]
        primaryProfession = row[4].replace("'", "''")
        knownForTitles = row[5].replace("'", "''")

        # only process on actors/actresses known for 4 movies and have a birth year
        if (birthYear == '\\N') or (len(knownForTitles) != 39):
            continue
        if ("actor" not in primaryProfession) and ("actress" not in primaryProfession):
            continue
        # convert unknown death year to int
        if (deathYear == '\\N'):
            deathYear = -1
        
        sqlCommand = sqlPrefix + "\"INSERT INTO imdb.names_raw VALUES ('%s', '%s', '%s', '%s', '%s', '%s');\"" % (nconst,
            primaryName, birthYear, deathYear, primaryProfession, knownForTitles)
        os.system(sqlCommand)
        namesAdded += 1
