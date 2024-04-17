<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>home</title>
    <script src="js/home.js" defer></script>
    <script src="js/storage.js" defer></script>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/default.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Koulen&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <a onclick="window.location.href='/login'"><img class="logout" src="svg/logout.svg" alt="logout icon"></a>
        <H1>Birdfeeder</H1>
    </header>
    <main>
        <button id="feed_bird" class="bird-feed-button" type="button">Feed Bird</button>
        <section>
            <button id="decrease_food" class="button-click" type="button">-</button>    
            <p id="amount_of_food">0 gram</p>
            <button id="increase_food" class="button-click" type="button">+</button>
        </section>
    </main>
    <nav>
        <ul>
            <li>
                <a onclick="window.location.href='/schedules'"><img src="svg/schedule.svg" alt="schedule"></a>
                <a onclick="window.location.href='/home'"><img src="svg/home.svg" alt="home"></a>
                <img class="percentage" src="" id="svg-container" alt="percentage">
            </li>
        </ul>
    </nav>
</body>
</html>