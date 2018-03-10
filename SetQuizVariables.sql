/*
Variables for quiz session
Must set all of these (from php or wherever) before running the quiz question procedures
 */
 
/*
Constant defaults for quiz settings.
Shouldn't change.
*/
SET @MinNumVotes = 10000; #Needs to be the same as the value used when trimming the tables
SET @DefaultDifficultyPercent = 60;
SET @DefaultMinMovieYear = 0;
SET @DefaultMaxMovieYear = 9999;
SET @DefaultMinRelatedness = 2; #Ex: Min. movies 2 actors must star in to be related

/*
Dynamic quiz settings, optionally set to non-default values by user
Set these right before loading the settings page
 */
SET @DifficultyPercent = @DefaultDifficultyPercent;
SET @MinMovieYear = @DefaultMinMovieYear;
SET @MaxMovieYear = @DefaultMaxMovieYear;
SET @MinRelatedness = @DefaultMinRelatedness;
