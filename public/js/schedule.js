document.addEventListener('DOMContentLoaded', function () {
    const newAlarmBtn = document.getElementById('newAlarmBtn');
    const alarmsContainer = document.getElementById('alarmsContainer');
    const alarmTemplate = document.getElementById('alarmTemplate');

    getAlarms();

    newAlarmBtn.addEventListener('click', createAlarm);


    function createAlarm() {
        const baseUrl = window.location.origin;
        const url = `${baseUrl}/createTimer`;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({}),
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                else {
                    getAlarms();
                }
                return response.json();
            })
            .catch(error => {
                console.error('There was a problem with your fetch operation:', error);
            });
    }


    function getAlarms() {
        const baseUrl = window.location.origin;
        const url = `${baseUrl}/getTimers`;

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log(data);
                updateAlarm(data['message']);
            })
            .catch(error => {
                console.error('There was a problem with your fetch operation:', error);
            });
    }

    function updateAlarm(timers) {
        alarmsContainer.innerHTML = '';
    
        timers.forEach(timer => {
            const clone = document.importNode(alarmTemplate.content, true);
            const toggleAlarm = clone.querySelector('.switch input');
            const alarmTime = clone.querySelector('.alarmTime');
            const daysOfWeek = clone.querySelector('.daysOfWeek');
            const deleteButton = clone.querySelector('.deleteAlarm');
            const saveButton = clone.querySelector('.saveAlarm');
    
            toggleAlarm.checked = timer.feed_time_is_active === 1;
            alarmTime.value = timer.time;
            
            const days = timer.days_of_week.split(';');
            days.forEach(day => {
                const checkbox = daysOfWeek.querySelector(`[data-day="${day}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                    const dayElement = clone.querySelector(`#${day}`);
                    if (dayElement) {
                        dayElement.classList.add('highlight');
                    }
                }
            });

            const outputTime = clone.querySelector('#output');
            if (outputTime) {
                const timeWithoutSeconds = timer.time.split(':').slice(0, 2).join(':');
                outputTime.textContent = timeWithoutSeconds;
            }
    

            toggleAlarm.addEventListener('click', function () {
                const isActive = this.checked ? 1 : 0;
                updateTimerActive(timer.timer_id, isActive);
            });
    
            alarmTime.addEventListener('change', function () {
                timer.time = this.value;
            });
    
            deleteButton.addEventListener('click', function () {
                deleteAlarm(timer.timer_id);
            });
    
            saveButton.addEventListener('click', function () {
                const selectedDays = [];
                daysOfWeek.querySelectorAll('.checkbox-custom').forEach(checkbox => {
                    if (checkbox.checked) {
                        selectedDays.push(checkbox.getAttribute('data-day'));
                    }
                });
    
                const data = {
                    timer_id: timer.timer_id,
                    time: alarmTime.value,
                    days_of_week: selectedDays.join(';')
                };
    
                saveTimer(data);
            });
    
            alarmsContainer.appendChild(clone);
        });
    }

    function updateTimerActive(timerId, isActive) {
        const queryString = `timer_id=${timerId}&feed_time_is_active=${isActive}`;
        
        fetch(`/updateTimerActive?${queryString}`, {
            method: 'GET'
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to update timer status');
                }
                return response.json();
            })
            .then(data => {
                getAlarms();
            })
            .catch(error => {
                console.error('Error updating timer status:', error);
            });
    }

    function saveTimer(data) {
        const { timer_id, time, days_of_week } = data;

        const daysOfWeekQueryString = days_of_week ? `&days_of_week=${days_of_week}` : 'NAN';
        const queryString = `timer_id=${timer_id}&time=${time}&days_of_week=${daysOfWeekQueryString}`;

        fetch(`/updateTimer?${queryString}`, {
            method: 'GET'
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to save timer');
                }
                return response.json();
            })
            .then(data => {
                getAlarms();
            })
            .catch(error => {
                console.error('Error saving timer:', error);
            });
    }

    function deleteAlarm(timerId) {
        fetch("/deleteTimer?timer_id=" + timerId, {
            method: 'GET'
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to delete timer');
                }
                return response.json();

            })
            .then(data => {
                getAlarms();
            })
            .catch(error => {
                console.error('Error deleting timer:', error);
            });
    }

    alarmsContainer.addEventListener('click', function (event) {
        if (event.target.closest('.alarm') && !event.target.matches('button') && !event.target.matches('input') && !event.target.matches('span') && !event.target.matches('label')) {
            const alarm = event.target.closest('.alarm');
            const daysOfWeek = alarm.querySelector('.daysOfWeek');
            daysOfWeek.classList.toggle('hidden');
        }
    });

});
