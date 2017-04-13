<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">

		<title>
			Etsy Site
		</title>

	</head>
	<body>

		<header>
			<h1>A Very Bad Etsy Site</h1>
		</header>

		<main>
			<h2>User Story:</h2><p>As a user, I want to favorite a product.</p>
			<br><br>

			<h3>Entities and Attributes</h3>

			<p><strong>Profile</strong></p>
			<ul>
				<li>profileId</li>
				<li>profileName</li>
				<li>profileHash</li>
				<li>profileSalt</li>
			</ul>

			<p><strong>Favorite</strong></p>
			<ul>
				<li>favoriteProfileId</li>
				<li>favoriteId</li>
				<li>favoriteDate</li>
			</ul>

			<h3>Conceptual Model</h3>

			<ul>
				<li>one consumer can favorite many products</li>
				<li>many consumers can favorite many products</li>
				<li>consumer can unfavorite a product</li>
				<li>consumers can see how many consumers have favorited a product</li>
			</ul>

		</main>
	</body>
</html>