#DROP PROCEDURE getActors IF EXISTS;
CREATE PROCEDURE getActors(IN Id INT)
  BEGIN
    SELECT *
    FROM ACTOR, (SELECT MA.ActorId
                 FROM MOVIE_ACTOR MA
                 WHERE MA.MovieId = Id) MA
    WHERE ACTOR.ActorId = MA.ActorId;
  END;

DELIMITER $
CREATE PROCEDURE getMovies(IN Id INT)
  BEGIN
    SELECT *
    FROM MOVIE M, (SELECT MA.MovieId
                   FROM MOVIE_ACTOR MA
                   WHERE MA.ActorId = Id) MA
    WHERE M.Id = MA.MovieId;
  END $
DELIMITER ;

#DROP PROCEDURE getMoviesByDecade;
DELIMITER $
CREATE PROCEDURE getMoviesByDecade(IN year INT)
  BEGIN
    SELECT * FROM MOVIE M
    WHERE M.StartYear >= year AND M.StartYear < year + 10
    GROUP BY RAND()
    LIMIT 10;
  END $
DELIMITER ;

DROP FUNCTION getBadActor;
DELIMITER $
CREATE FUNCTION getBadActor(Id INT, birth INT, gender CHAR(1))
  RETURNS VARCHAR(512)
  BEGIN
    RETURN(SELECT A.FullName FROM ACTOR A
    WHERE ActorId != Id AND A.Gender = gender
          AND  ABS(A.BirthYear - birth) <= 8
    GROUP BY RAND()
           LIMIT 1);
  END $
DELIMITER ;

DELIMITER $
CREATE FUNCTION getBadMovie(Id INT, year INT)
  RETURNS VARCHAR(512)
  BEGIN
    RETURN(SELECT M.PrimaryTitle FROM MOVIE M
    WHERE M.StartYear = year AND M.Id != Id
    GROUP BY RAND()
           LIMIT 1);
  END $
DELIMITER ;

DELIMITER $
CREATE PROCEDURE getMovie(IN Id INT)
  BEGIN
    SELECT * FROM MOVIE
    WHERE MOVIE.Id = Id;
  END $
DELIMITER ;

DELIMITER $
CREATE PROCEDURE getActor(IN Id INT)
  BEGIN
    SELECT * FROM ACTOR
    WHERE ACTOR.ActorId = Id;
  END $
DELIMITER ;

DROP PROCEDURE  getMovieYearQuestion;
DELIMITER $
CREATE PROCEDURE getMovieYearQuestion(OUT question VARCHAR(255), IN name VARCHAR(128),
                                      IN a1 INT, OUT a2 INT, OUT a3 INT, OUT a4 INT)
  BEGIN
    SET question = CONCAT("What year was ", name, " made?");

    SET a2 = getRandomYear(a1);
    Set a3 = getRandomYear(a1);
    Set a4 = getRandomYear(a1);
  END $
DELIMITER ;

DROP FUNCTION getRandomYear;
DELIMITER $
CREATE FUNCTION getRandomYear(a1 INT)
  RETURNS INTEGER
  BEGIN
    RETURN a1 - 5 + FLOOR(1 + RAND() * 10);
  END $
DELIMITER ;
