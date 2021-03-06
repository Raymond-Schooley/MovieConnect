/*
Quiz question procedures:
 */

# asks who was the lead star of a random movie
DROP PROCEDURE IF EXISTS makeQuestionWhoWasLeadInRandMovie;
CREATE PROCEDURE makeQuestionWhoWasLeadInRandMovie (OUT question VARCHAR(255),
                                                    OUT a1 VARCHAR(255),
                                                    OUT a2 VARCHAR(255),
                                                    OUT a3 VARCHAR(255),
                                                    OUT a4 VARCHAR(255))
  BEGIN
    SET @Min = getCustomMinNumVotes();
    SET @randomMovie = getRandomMovieId();
    SET question = CONCAT("Who was the lead actor in ", getMovieName(@randomMovie), "?");

    SET @answerActorID =
    (SELECT a.ActorID
     FROM MovieActor ma
       LEFT JOIN Movie m
         ON ma.MovieID = m.MovieID
       LEFT JOIN Actor a
         ON ma.ActorID = a.ActorID
     WHERE
       m.MovieID = @randomMovie
       AND m.numVotes > @Min
       AND (m.startYear >= @MinMovieYear AND m.startYear <= @MaxMovieYear)
     ORDER BY Importance
     LIMIT 0,1
    );

    SET a1 = getActorName(@answerActorID);
    SET a2 = getActorName(getRandomActorId());
    SET a3 = getActorName(getRandomActorId());
    SET a4 = getActorName(getRandomActorId());
  END;


# asks what movie a random actor starred in
DROP PROCEDURE IF EXISTS makeQuestionWhatMovieRandActorLeadIn;
CREATE PROCEDURE makeQuestionWhatMovieRandActorLeadIn (OUT question VARCHAR(255),
                                                       OUT a1 VARCHAR(255),
                                                       OUT a2 VARCHAR(255),
                                                       OUT a3 VARCHAR(255),
                                                       OUT a4 VARCHAR(255))
  BEGIN
    SET @Min = getCustomMinNumVotes();
    SET @movieAnswer = getRandomMovieId();

    SET @TheActorID =
    (SELECT a.ActorID
     FROM MovieActor ma
       LEFT JOIN Movie m
         ON ma.MovieID = m.MovieID
       LEFT JOIN Actor a
         ON ma.ActorID = a.ActorID
     WHERE
       m.MovieID = @movieAnswer
       AND m.numVotes > @Min
       AND (m.startYear >= @MinMovieYear AND m.startYear <= @MaxMovieYear)
     ORDER BY Importance
     LIMIT 0,1
    );

    SET question = CONCAT("What movie did ", getActorName(@TheActorID), " star in?");
    SET a1 = getMovieName(@movieAnswer);
    SET a2 = getMovieName(getRandomMovieId());
    SET a3 = getMovieName(getRandomMovieId());
    SET a4 = getMovieName(getRandomMovieId());
  END;


# Creates question asking what random movie 2 or more random actors starred in
DROP PROCEDURE IF EXISTS makeQuestionWhatMovieStarsTheseActors;
CREATE PROCEDURE makeQuestionWhatMovieStarsTheseActors(OUT question VARCHAR(255),
                                                       OUT a1 VARCHAR(255),
                                                       OUT a2 VARCHAR(255),
                                                       OUT a3 VARCHAR(255),
                                                       OUT a4 VARCHAR(255))
  BEGIN
    SET @Min = getCustomMinNumVotes();

    SELECT m.MovieID, GROUP_CONCAT(a.primaryName SEPARATOR ', and ') INTO @TheMovie, @TheActors
    FROM Movie m
      LEFT JOIN MovieActor ON m.MovieID = MovieActor.MovieID
      LEFT JOIN Actor a ON a.ActorID = MovieActor.ActorID
    WHERE m.numVotes > @Min
          AND (m.startYear >= @MinMovieYear AND m.startYear <= @MaxMovieYear)
    GROUP BY m.MovieID
    having count(distinct a.ActorID) >= @MinRelatedness
    ORDER BY RAND()
    LIMIT 0,1
    ;

    SET question = CONCAT("What movie did the actors ", @TheActors, " star in?");
    SET a1 = getMovieName(@TheMovie);
    SET a2 = getMovieName(getRandomMovieId());
    SET a3 = getMovieName(getRandomMovieId());
    SET a4 = getMovieName(getRandomMovieId());
  END;



# Creates question asking what random actor starred in 2 or more random movies
DROP PROCEDURE IF EXISTS makeQuestionWhatActorStarredInTheseMovies;
CREATE PROCEDURE makeQuestionWhatActorStarredInTheseMovies(OUT question VARCHAR(255),
                                                           OUT a1 VARCHAR(255),
                                                           OUT a2 VARCHAR(255),
                                                           OUT a3 VARCHAR(255),
                                                           OUT a4 VARCHAR(255))
  BEGIN
    SET @Min = getCustomMinNumVotes();

    SELECT a.ActorID, GROUP_CONCAT(m.primaryTitle SEPARATOR ', and ') INTO @TheActor, @TheMovies
    FROM Actor a
      LEFT JOIN MovieActor ON a.ActorID = MovieActor.ActorID
      LEFT JOIN Movie m ON m.MovieID = MovieActor.MovieID
    WHERE m.numVotes > @Min
          AND (m.startYear >= @MinMovieYear AND m.startYear <= @MaxMovieYear)
    GROUP BY a.ActorID
    having count(distinct m.MovieID) >= @MinRelatedness
    ORDER BY RAND()
    LIMIT 0,1
    ;

    SET question = CONCAT("What actor starred in the movies ", @TheMovies, "?");
    SET a1 = getActorName(@TheActor);
    SET a2 = getActorName(getRandomActorId());
    SET a3 = getActorName(getRandomActorId());
    SET a4 = getActorName(getRandomActorId());
  END;


# Create question asking what year a random movie was made in
DROP PROCEDURE IF EXISTS createQuestionWhatYearWasRandMovie;
CREATE PROCEDURE createQuestionWhatYearWasRandMovie(OUT question VARCHAR(255),
                                                    OUT a1 VARCHAR(255),
                                                    OUT a2 VARCHAR(255),
                                                    OUT a3 VARCHAR(255),
                                                    OUT a4 VARCHAR(255))
  BEGIN
    SET @randMovie = getRandomMovieId();
    SET question = CONCAT("What year was ", getMovieName(@randMovie), " made?");
    SET a1 = getMovieYear(@randMovie);
    SET a2 = getRandomYear(a1);
    Set a3 = getRandomYear(a1);
    Set a4 = getRandomYear(a1);
  END;
CALL createQuestionWhatYearWasRandMovie(@q, @a, @w1, @w2, @w3);
SELECT @q, @a, @w1, @w2, @w3;



/*
Utility procedures:
 */

# Pick random movie id
DROP FUNCTION IF EXISTS getRandomMovieId;
CREATE FUNCTION getRandomMovieId()
  RETURNS INT
  BEGIN
    SET @Min = getCustomMinNumVotes();
    SET @RandomMovie =
    (SELECT MovieID
     FROM Movie
     WHERE
       numVotes > @Min
       AND (startYear >= @MinMovieYear AND startYear <= @MaxMovieYear)
     # AND NOT MovieID = notID
     ORDER BY RAND()
     LIMIT 1);
    RETURN @RandomMovie;
  END;


# Pick random actor id
DROP FUNCTION IF EXISTS getRandomActorId;
CREATE FUNCTION getRandomActorId()
  RETURNS INT
  BEGIN
    SET @RandomMovie =
    (SELECT ActorID
     FROM Actor
     #WHERE NOT ActorID = notID
     ORDER BY RAND()
     LIMIT 1);
    RETURN @RandomMovie;
  END;


# Gets a random year
DROP FUNCTION getRandomYear;
DELIMITER $
CREATE FUNCTION getRandomYear(a1 INT)
  RETURNS INTEGER
  BEGIN
    RETURN a1 - 5 + FLOOR(1 + RAND() * 10);
  END $
DELIMITER ;


/*
gets the minimum number a of votes a movie can have when finding a random movie
based off the setting @DifficultyPercent
 */
DROP FUNCTION IF EXISTS getCustomMinNumVotes;
CREATE FUNCTION getCustomMinNumVotes()
  RETURNS FLOAT
  BEGIN
    SET @highestNumVotes = (SELECT MAX(numVotes) FROM Movie) - @MinNumVotes;
    SET @easynessPercent = 100.00 - @DifficultyPercent;
    SET @customHighestNumVotes = @highestNumVotes * @easynessPercent / 100.00;
    RETURN @customHighestNumVotes + @MinNumVotes;
  END;



# Gets the primary name an actor based on its ID
DROP FUNCTION IF EXISTS getActorName;
CREATE FUNCTION getActorName(AID INT)
  RETURNS VARCHAR(128)
  BEGIN
    RETURN (SELECT A.primaryName FROM Actor A
    WHERE A.ActorId = AID);
  END;


# Gets the year of a movie based on its ID
DROP FUNCTION IF EXISTS getMovieYear;
CREATE FUNCTION getMovieYear(MID INT)
  RETURNS INT
  BEGIN
    RETURN (SELECT M.startYear
            FROM Movie M
    WHERE M.MovieId = MID);
  END;


# Gets the primary name a movie based on its ID
DROP FUNCTION IF EXISTS getMovieName;
CREATE FUNCTION getMovieName(MID INT)
  RETURNS VARCHAR(512)
  BEGIN
    RETURN (SELECT M.primaryTitle FROM Movie M
    WHERE M.MovieId = MID);
  END;
