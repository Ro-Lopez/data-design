<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
	<head>
		<meta charset="utf-8">

		<title>Conceptual Model</title>

	</head>
	<body>

		<header>
			<h1>A Very Bad Etsy Site</h1>
		</header>

		<main>
			<h2>User Story:</h2><p>As a user, I want to favorite a product.</p>


			<p><h2>Conceptual Model</h2></p>

			<h3>Entities and Attributes</h3>

			<p><strong>Profile</strong></p>
			<ul>
				<li>profileId</li>
				<li>profileName</li>
				<li>profileHash</li>
				<li>profileSalt</li>
				<li>profileEmail</li>
				<li>profileAtHandle</li>
				<li>profilePhone</li>
				<li>profileActivationToken</li>
			</ul>

			<p><strong>Product</strong></p>
			<ul>
				<li>productId</li>
				<li>productProfileId</li>
				<li>productContent</li>
				<li>productDate</li>
			</ul>

			<p><strong>Favorite</strong></p>
			<ul>
				<li>favoriteProfileId</li>
				<li>favoriteProductId</li>
				<li>favoriteId</li>
				<li>favoriteDate</li>
				<li>favoriteContent</li>
			</ul>

			<p><strong>Relations</strong></p>
			<ul>
				<li>one user can favorite many products</li>
				<li>many users can favorite many products</li>
				<li>users can see how many consumers have favorited a product</li>
			</ul>

		</main>
	</body>
</html>