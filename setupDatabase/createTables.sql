SET @MinNumVotes = 10000;
SET @MaxActorsPerMovie = 5;

/*
(1)
Download these archives, extract them, and replace *LOCATION* in this script with the parent directory of the extracted data.
DOWNLOAD LOCATION										                  SUMMARY OF DATA										            SIZE
https://datasets.imdbws.com/title.basics.tsv.gz       show ID -> basic info of show                 84 MB
https://datasets.imdbws.com/title.ratings.tsv.gz		  show ID -> IMDB rating summary                4 MB
https://datasets.imdbws.com/title.principals.tsv.gz		show ID -> list of principal crew person IDs	230 MB
https://datasets.imdbws.com/name.basics.tsv.gz        person ID -> basic info of person             158 MB
More info: http://www.imdb.com/interfaces/
*/


/*
(2)
Create database moviequiz
Create an sql table for each file and load all data into them
*/

# create table from title.basics.tsv/data.tsv
DROP TABLE IF EXISTS titlebasics_raw;
CREATE TABLE titlebasics_raw (
  tconst CHAR(9) NOT NULL PRIMARY KEY,
  titleType VARCHAR(32),
  primaryTitle VARCHAR(512),
  originalTitle VARCHAR(512),
  isAdult INT,
  startYear INT,
  endYear INT,
  runtimeMinutes INT,
  genres VARCHAR(32)
);
LOAD DATA LOCAL INFILE '*LOCATION*/title.basics.tsv/data.tsv' REPLACE INTO TABLE titlebasics_raw IGNORE 1 LINES;

# create table from title.ratings.tsv/data.tsv
DROP TABLE IF EXISTS titleratings_raw;
CREATE TABLE titleratings_raw (
  tconst CHAR(9),
  averageRating FLOAT,
  numVotes INT
);
LOAD DATA LOCAL INFILE '*LOCATION*/title.ratings.tsv/data.tsv' REPLACE INTO TABLE titleratings_raw IGNORE 1 LINES;

# create table from title.principals.tsv/data.tsv
DROP TABLE IF EXISTS titleprincipals_raw;
CREATE TABLE titleprincipals_raw (
  tconst CHAR(9),
  ordering INT,
  nconst CHAR(9),
  category VARCHAR(64),
  job VARCHAR(512),
  characters VARCHAR(512)
);
LOAD DATA LOCAL INFILE '*LOCATION*/title.principals.tsv/data.tsv' REPLACE INTO TABLE titleprincipals_raw IGNORE 1 LINES;

# create table from name.basics.tsv/data.tsv
DROP TABLE IF EXISTS namebasics_raw;
CREATE TABLE namebasics_raw (
  nconst CHAR(9) NOT NULL PRIMARY KEY,
  primaryName VARCHAR(128),
  birthYear INT,
  deathYear INT,
  primaryProfession VARCHAR(128),
  knownForTitles VARCHAR(128)
);
LOAD DATA LOCAL INFILE '*LOCATION*/name.basics.tsv/data.tsv' REPLACE INTO TABLE namebasics_raw IGNORE 1 LINES;


/*
Create 3 new tables from the raw data with trimmed and combined rows
 */

# Determines gender from the profession string
DROP FUNCTION IF EXISTS GETGENDER;
CREATE FUNCTION GETGENDER(primaryProfession VARCHAR(128))
  RETURNS CHAR(1)
  BEGIN
    IF primaryProfession LIKE '%actor%' THEN
      RETURN 'm';
    ELSE
      RETURN 'f';
    END IF;
  END;

/*
Create Actor from namebasics_raw
Convert nconst CHAR(9) to ActorID INT
 */
DROP TABLE IF EXISTS Actor;
CREATE TABLE Actor
    SELECT CONVERT(SUBSTRING(nb.nconst, 3), INT) AS ActorID,
      nb.primaryName, nb.birthYear, nb.deathYear,
           GETGENDER(nb.primaryProfession) AS Gender
    FROM namebasics_raw nb
   WHERE (nb.primaryProfession LIKE '%actor%' OR nb.primaryProfession LIKE '%actress%')
;
ALTER TABLE Actor ADD PRIMARY KEY (ActorID);

/*
Create Movie from titlebasics_raw
Convert tconst to INT MovieID
Keep only highly voted on movies
 */
DROP TABLE IF EXISTS Movie;
CREATE TABLE Movie
    SELECT CONVERT(SUBSTRING(tb.tconst, 3), INT) AS MovieID,
      tb.titleType, tb.primaryTitle, tb.originalTitle, tb.startYear, tb.runtimeMinutes, tb.genres, tr.numVotes, tr.averageRating
    FROM titlebasics_raw tb, titleratings_raw tr
    WHERE tb.tconst = tr.tconst
          AND tr.numVotes >= @MinNumVotes
          AND tb.titleType = 'movie'
          AND NOT tb.genres LIKE '%Animation%'
          AND NOT tb.genres LIKE '%Documentary%'
;
ALTER TABLE Movie ADD PRIMARY KEY (MovieID);

/*
Create MovieActor from titleprincipals_raw
Keep only the top X actors from each movie
 */
DROP TABLE IF EXISTS MovieActor_temp;
CREATE TABLE MovieActor_temp
    SELECT CONVERT(SUBSTRING(tp.tconst, 3), INT) AS MovieID,
           CONVERT(SUBSTRING(tp.nconst, 3), INT) AS ActorID,
           tp.ordering AS Importance, tp.category AS Job, tp.characters
    FROM titleprincipals_raw tp
    WHERE ordering <= @MaxActorsPerMovie
      AND (category LIKE '%actor%' OR category LIKE '%actress%')
;

# Delete duplicate (MovieID, ActorID) in MovieActor
DROP TABLE IF EXISTS MovieActor;
CREATE TABLE MovieActor (
  MovieID INT,
  ActorID INT,
  Importance INT,
  Job VARCHAR(64),
  characters VARCHAR(512),
  PRIMARY KEY (MovieID, ActorID)
);
INSERT IGNORE INTO MovieActor
  SELECT * FROM MovieActor_temp
;
DROP TABLE MovieActor_temp;


/*
(4) Trim the 3 tables against themselves
 */

/*
delete MovieActor relations that aren't in Movie
(Keep only relations with highly-voted on movies)
 */
DELETE ma FROM MovieActor ma
  LEFT JOIN Movie m ON m.MovieID = ma.MovieID
WHERE m.MovieID IS NULL
;

/*
delete movies that aren't in the final MovieActor table
 */
DELETE m FROM Movie m
  LEFT JOIN MovieActor ma ON ma.MovieID = m.MovieID
WHERE ma.MovieID IS NULL
;

/*
delete actors that aren't in the final MovieActor table
 */
DELETE a FROM Actor a
  LEFT JOIN MovieActor ma ON ma.ActorID = a.ActorID
WHERE ma.ActorID IS NULL
;

#Get rid of a couple of fields that are either not being used or are unnormalized.
ALTER TABLE Movie DROP genres;
ALTER TABLE Movie DROP titleType;
ALTER TABLE Movie DROP characters;
