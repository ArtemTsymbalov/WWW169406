/**
 * Time and Date Display Module
 * 
 * Provides functionality for:
 * - Real-time clock display
 * - Date formatting and display
 * - Timer control
 */

/**
 * Updates and displays current date
 */
function gettheDate() {
    Todays = new Date();
    TheDate = "" + (Todays.getMonth()+1) + " /" + Todays.getDate() + " /" + (Todays.getYear()-100);
    document.getElementById("data").innerHTML = TheDate;
}

var timerID = null;
var timerRunning = false;

/**
 * Stops the clock timer
 */
function stoplock() {
    if(timerRunning) {
        clearTimeout(timerID);
    }
    timeRunning = false;
}

/**
 * Starts the clock display
 */
function startclock() {
    stoplock();
    gettheDate();
    showtime();
}

/**
 * Displays current time in 12-hour format
 */
function showtime() {
    var now = new Date();
    var hours = now.getHours();
    var minutes = now.getMinutes();
    var seconds = now.getSeconds();
    
    // Format time string
    var timeValue = "" + ((hours > 12) ? hours - 12 : hours);
    timeValue += ((minutes < 10) ? ":0" : ":") + minutes;
    timeValue += ((seconds < 10) ? ":0" : ":") + seconds;
    timeValue += (hours >= 12) ? " P.M." : " A.M.";
    
    document.getElementById("zegarek").innerHTML = timeValue;
    timerID = setTimeout("showtime()", 1000);
    timerRunning = true;
}