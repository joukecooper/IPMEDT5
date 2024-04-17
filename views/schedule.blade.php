<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Alarm App</title>
  <link rel="stylesheet" href="css/schedule.css">
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
  <div class="container">
    <button id="newAlarmBtn">New Alarm</button>
    <div id="alarmsContainer"></div>
  </div>

  <template id="alarmTemplate">
    <div class="alarm" id="timerIdTemplate">
      <div class="alarmHeader">
        <p id="output">10:00</p>
        <p id="monday">M</p>
        <p id="tuesday">T</p>
        <p id="wednesday">W</p>
        <p id="thursday">T</p>
        <p id="friday">F</p>
        <p id="saturday">S</p>
        <p id="sunday">S</p>
        <label class="switch">
          <input type="checkbox">
          <span class="slider"></span>
      </label>
      </div>
      <div class="daysOfWeek">
        <div>
          <input type="time" value="10:00" class="alarmTime" id="alarmTime">
        </div>
        <div>
          <label><input type="checkbox" class="checkbox-custom" id="checkboxA" data-day="monday">M</label>
          <label><input type="checkbox" class="checkbox-custom" id="checkboxB" data-day="tuesday">T</label>
          <label><input type="checkbox" class="checkbox-custom" id="checkboxC" data-day="wednesday">W</label>
          <label><input type="checkbox" class="checkbox-custom" id="checkboxD" data-day="thursday">T</label>
          <label><input type="checkbox" class="checkbox-custom" id="checkboxE" data-day="friday">F</label>
          <label><input type="checkbox" class="checkbox-custom" id="checkboxF" data-day="saturday">S</label>
          <label><input type="checkbox" class="checkbox-custom" id="checkboxG" data-day="sunday">S</label>
        </div>
        <div>
          <button class="deleteAlarm">Delete</button>
          <button class="saveAlarm">Save</button>
        </div>
      </div>
    </div>
</template>
  </main>


  <nav>
        <ul>
            <li>
                <a onclick="window.location.href='/schedules'"><img src="svg/schedule.svg" alt="schedule"></a>
                <a onclick="window.location.href='/home'"><img src="svg/home.svg" alt="home"></a>
                <img class="percentage" src="svg/battery1.svg" id="svg-container" alt="percentage">
            </li>
        </ul>
    </nav>

</body>
  <script src="js/schedule.js"></script>
  <script src="js/storage.js"></script>
</html>
