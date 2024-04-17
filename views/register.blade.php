<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Koulen&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
    <header>
        <h1>Birdfeeder</h1>
    </header>
    <main>
        <form id="registerForm" action="/registerUser" method="post">
            @csrf <!-- Include CSRF token -->
            <label for="username">Username:</label>
            <input type="text" name="username" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" name="password" required>
            <br>
            <button type="submit">Register</button>
        </form>

        <p>already registered? <a href="/login">login now</a></p>
    </main>

    <script src="js/register.js"></script>
</body>
</html>
