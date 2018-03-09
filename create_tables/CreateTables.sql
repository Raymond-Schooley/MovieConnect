SET @MinNumVotes = 10000;
SET @MaxImportance = 5;

DROP DATABASE IF EXISTS moviequiz;
CREATE DATABASE moviequiz;
USE moviequiz;


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
LOAD DATA INFILE '*LOCATION*/name.basics.tsv/data.tsv' REPLACE INTO TABLE namebasics_raw IGNORE 1 LINES;

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
LOAD DATA INFILE '*LOCATION*/title.basics.tsv/data.tsv' REPLACE INTO TABLE titlebasics_raw IGNORE 1 LINES;

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
LOAD DATA INFILE '*LOCATION*/title.principals.tsv/data.tsv' REPLACE INTO TABLE titleprincipals_raw IGNORE 1 LINES;

# create table from title.ratings.tsv/data.tsv
DROP TABLE IF EXISTS titleratings_raw;
CREATE TABLE titleratings_raw (
  tconst CHAR(9),
  averageRating FLOAT,
  numVotes INT
);
LOAD DATA INFILE '*LOCATION*/title.ratings.tsv/data.tsv' REPLACE INTO TABLE titleratings_raw IGNORE 1 LINES;


/*
Create 3 new tables from the raw data with trimmed and combined rows
 */

/*
Create Actor from namebasics_raw
Convert nconst CHAR(9) to ActorID INT
 */
DROP TABLE IF EXISTS Actor;
CREATE TABLE Actor
    SELECT CONVERT(SUBSTRING(nb.nconst, 3), INT) AS ActorID,
      nb.primaryName, nb.birthYear, nb.deathYear
    FROM namebasics_raw nb
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
          AND tb.titleType = 'movie';
;
ALTER TABLE Movie ADD PRIMARY KEY (MovieID);

/*
Create MovieActor from titleprincipals_raw
Keep only the top 5 actors from each movie
 */
DROP TABLE IF EXISTS MovieActor;
CREATE TABLE MovieActor
    SELECT CONVERT(SUBSTRING(tp.tconst, 3), INT) AS MovieID,
           CONVERT(SUBSTRING(tp.nconst, 3), INT) AS ActorID,
           tp.ordering AS Importance, tp.category AS Job, tp.characters
    FROM titleprincipals_raw tp
    WHERE ordering <= @MaxImportance
          AND (category LIKE '%actor%' OR category LIKE '%actress%')
;
/*
Drop all duplicate (Same MovieID and ActorID) rows from MovieActor
There is at least 1 instance of duplicate rows (MovieID 995411, ActorID 1075459).
 */
ALTER IGNORE TABLE MovieActor
  ADD UNIQUE INDEX movie_actor_index (MovieID, ActorID);
/*
Make MovieID, ActorID primary key after duplicates removed
 */
ALTER TABLE MovieActor ADD PRIMARY KEY (MovieID, ActorID);
/*


/*
(4) Trim the 3 tables against themselves
 */

/*
delete MovieActor relations that aren't in Movie
(Keep only relations with highly-voted on movies)
 */
DELETE FROM MovieActor
WHERE NOT EXISTS(SELECT NULL
                 FROM Movie m
                 WHERE m.MovieID = MovieID)
;
/*
delete movies that aren't in the final MovieActor table
 */
DELETE FROM Movie
WHERE NOT EXISTS(SELECT NULL
                 FROM MovieActor ma
                 WHERE ma.MovieID = MovieID)
;
/*
delete actors that aren't in the final MovieActor table
 */
DELETE FROM Actor
WHERE NOT EXISTS(SELECT NULL
                 FROM MovieActor ma
                 WHERE ma.ActorID = ActorID)
;
