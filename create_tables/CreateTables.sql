/*
(1)
Download these archives, extract them, and replace *LOCATION* in this script with the location of the extracted data.

DOWNLOAD LOCATION										SUMMARY OF DATA										SIZE
https://datasets.imdbws.com/title.basics.tsv.gz			show ID -> basic info of show						84 MB
https://datasets.imdbws.com/title.ratings.tsv.gz		show ID -> IMDB rating summary						4 MB
https://datasets.imdbws.com/title.principals.tsv.gz		show ID -> list of principal crew person IDs		230 MB
https://datasets.imdbws.com/name.basics.tsv.gz			person ID -> basic info of person					158 MB

More info: http://www.imdb.com/interfaces/
*/


/*
(2)
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
LOAD DATA LOCAL INFILE '*LOCATION*/name.basics.tsv/data.tsv' REPLACE INTO TABLE namebasics_raw CHARACTER SET latin1 IGNORE 1 LINES;

# create table from title.basics.tsv/data.tsv
DROP TABLE IF EXISTS titlebasics_raw
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


# create table from title.principals.tsv/data.tsv
DROP TABLE IF EXISTS titleprincipals_raw
CREATE TABLE titleprincipals_raw (
  tconst CHAR(9) NOT NULL PRIMARY KEY,
  principalCast VARCHAR(128)
);
LOAD DATA LOCAL INFILE '*LOCATION*/title.principals.tsv/data.tsv' REPLACE INTO TABLE titleprincipals_raw IGNORE 1 LINES;

# create table from title.ratings.tsv/data.tsv
DROP TABLE IF EXISTS titleratings_raw
CREATE TABLE titleratings_raw (
  tconst CHAR(9),
  averageRating FLOAT,
  numVotes INT
);
LOAD DATA LOCAL INFILE '*LOCATION*/title.ratings.tsv/data.tsv' REPLACE INTO TABLE titleratings_raw IGNORE 1 LINES;


/*
(3)
Trim and combine show tables?
Trim person table?
*/
# Create a table of movies with a significant number of votes by combining titlebasics_raw and titleratings_raw.
DROP TABLE IF EXISTS MOVIE;
CREATE TABLE MOVIE SELECT tb.tconst Id, tb.primaryTitle PrimaryTitle, tb.originalTitle OriginalTitle, tb.endYear EndYear,
	tb.runtimeMinutes RuntimeMinutes, tb.genres Genres, tr.averageRating AverageRating, tr.numVotes Votes
	FROM titleratings_raw tr, titlebasics_raw tb
	WHERE tr.tconst = tb.tconst
		AND tr.numVotes > 10000
		AND tb.titleType = 'movie';

#add the primary key to movie
ALTER TABLE MOVIE ADD PRIMARY KEY (Id);

#Trim the prinipal people from movies that didn't make the cut for Movie1
DROP TABLE IF EXISTS PRINCIPAL;
CREATE TABLE PRINCIPAL SELECT tp.tconst Id, tp.principalCast PrincipalCast
                       FROM MOVIE M, titleprincipals_raw tp
                       WHERE M.Id = tp.tconst;


#Here we had to slow our roll.  The principal table contained reference to the movies
#that we care about as well as the main people involved in making it.  The problem
#was that the PrincipleCast column was a comma separated list of name ids.
#It was not feasible to use this csv field to trim our big namebasics table, getting
#rid of all the people not reference in the PrincipleCast of a movie we care about.
# So we exported our Principal table as a tsv file and wrote a small python script
#that created a csv. The new csv just took the movie Id and printed it and the
#first person in PrincipalCast on one line.  Then the same movie id and the second
#person on the second line.  Repeat this for all the movie person pairs and there
#is the bridge table expressed as a csv file we can create now.

DROP TABLE IF EXISTS IS_RELATED_TO;
CREATE TABLE IS_RELATED_TO (
  MovieId CHAR(10),
  PersonId CHAR(10),
  PRIMARY KEY (MovieId, PersonId)
);
LOAD DATA LOCAL INFILE '/home/ravschoo/Documents/TCSS445/Movies/MovieConnect/EdgeList.tsv' REPLACE INTO TABLE IS_RELATED_TO;

#Now MOVIE_PERSON only contains ids of person that we care about and we can use
#this to trim the namebasics into Person table containing only vips.

DROP TABLE IF EXISTS PERSON;
CREATE TABLE PERSON (
  PersonId CHAR(9) NOT NULL PRIMARY KEY,
  PrimaryName VARCHAR(128),
  BirthYear INT,
  DeathYear INT,
  PrimaryProfession VARCHAR(128),
  KnownForTitles VARCHAR(128)
);

INSERT IGNORE INTO PERSON
  SELECT nb.nconst PersonId, nb.primaryName PrimaryName, nb.birthYear BirthYear,
          nb.deathYear DeathYear, nb.primaryProfession PrimaryProfession,
          nb.knownForTitles KnownForTitles
    FROM namebasics_raw nb, IS_RELATED_TO R
    WHERE nb.nconst = R.PersonId;

#Here we decided we wanted to only deal with actors and actresses.  So we essential
#wanted to use the primary profession field (which is csv) to find the people
#who have one of these roles create a new table with only them and a new column
#for gender depending on whether they are actor or actress.  After that we get
#rid of the primaryProfession fiels as well as the other csv knowForTitles field.
#Since we are deal with csv another python script is used to create our data.
DROP TABLE IF EXISTS ACTOR;
CREATE TABLE ACTOR (
  ActorId CHAR(9) NOT NULL PRIMARY KEY,
  FullName VARCHAR(128),
  BirthYear INT,
  DeathYear INT DEFAULT NULL,
  Gender CHAR(1)
);
LOAD DATA LOCAL INFILE '/home/ravschoo/Documents/TCSS445/Movies/MovieConnect/Actor.tsv' REPLACE INTO TABLE ACTOR;


