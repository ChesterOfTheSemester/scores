
# Scores

Score list can be seen in @Route("/scores", name="scores")

Submitting new scores is possible in @Route("/api/submitScore/{name}/{difficulty}/{score}", name="submitScore")

Logging in as admin is possible in @Route("/login", name="login")

Logging out is possible in @Route("/logout", name="logout")

# API

There's a small API endpoint for getting scores in @Route("/api/getScores", name="getScores")

Parameters: order, orderBy (direction), filter

# DB dump

INSERT INTO `scores` (`id`, `name`, `difficulty`, `score`, `verified`) VALUES
	(1, 'user1', 1, 5, 1),
	(2, 'user2', 2, 1, 1),
	(3, 'user3', 99, 100, 1);
  
INSERT INTO `admins` (`id`, `username`, `password`) VALUES
	(1, 'chester', 'test'),
	(2, 'admin', 'admin');
