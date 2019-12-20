var isSeeking = false;
var seek = document.getElementById("seekObj");
var player =document.getElementById("player");
SetSeekColor();

function calculateTotalValue(length) {
  var minutes = Math.floor(length / 60),
    seconds_int = length - minutes * 60,
    seconds_str = seconds_int.toString(),
    seconds = seconds_str.split(".")[0],
    temp_min = minutes.toString().length === 1 ? "0" + minutes : minutes,
    temp_sec = seconds.toString().length === 1 ? "0" + seconds : seconds;
  return temp_min + ":" + temp_sec;
}

function calculateCurrentValue(_seconds) {
  function padTime(t) {
    return t < 10 ? "0" + t : t;
  }

  if (typeof _seconds !== "number") return "";
  if (_seconds < 0) {
    _seconds = Math.abs(_seconds);
  }
  var hours = Math.floor(_seconds / 3600),
    minutes = Math.floor((_seconds % 3600) / 60),
    seconds = Math.floor(_seconds % 60);
  var hour = hours > 0 ? padTime(hours) + ":" : "";
  return hour + padTime(minutes) + ":" +  padTime(seconds);
}

function setupSeek() {
  seek.max = player.duration;
}

function seekAudio() {
  isSeeking = true;
  player.currentTime = seek.value;
  isSeeking = false;
}

var prevcurrentime = 0;
function initProgressBar() {
  if (!isSeeking) {
    seek.value = player.currentTime;
  }
  var length = player.duration;
  var current_time = player.currentTime;

  // calculate total length of value
  var totalLength = calculateCurrentValue(length);


  // calculate current value time
  var currentTime = calculateCurrentValue(current_time);
  if (player.readyState === 4) {
      jQuery(".end-time").html(totalLength);
      jQuery(".start-time").html(currentTime);
  }
  //checking if the current time is bigger than the previous or else there will be sync different between remaining and current
  if (currentTime > prevcurrentime) {
    //calculate the remaining time
    var rem_time = length - current_time;
    jQuery(".rem-time").html('Remaining : '+calculateCurrentValue(rem_time));
  }
  //setting the previouscurrent time to this current time
  prevcurrentime = currentTime;
}


//seek js
setInterval(function() {
  SetSeekColor();
}, 1);

function SetSeekColor() {
  try {
    var val =
      (jQuery("#seekObj").val() - jQuery("#seekObj").attr("min")) /
      (jQuery("#seekObj").attr("max") - jQuery("#seekObj").attr("min"));
    var percent = val * 100;
    jQuery("#seekObj").css(
      "background-image",
      "-webkit-gradient(linear, left top, right top, " +
        "color-stop(" +
        percent +
        "%, #0E0E0E), " +
        "color-stop(" +
        percent +
        "%, #d3d3d3)" +
        ")"
    );

    jQuery("#seekObj").css(
      "background-image",
      "-moz-linear-gradient(left center, #0E0E0E 0%, #0E0E0E " +
        percent +
        "%, #d3d3d3 " +
        percent +
        "%, #d3d3d3 100%)"
    );
  } catch (e) {}
}

function showhideRemaining(e) {
  jQuery(e).toggleClass("end-time rem-time");
}

//Play and pause function 
function playAudio() {
	try {
		//return objects we need to work with 
		var oAudio = document.getElementById('player'); 
		var btn = document.getElementById('play');
        clearHglt();
		//Tests the paused attribute and set state. 
		if (oAudio.paused) {
			oAudio.play();
			btn.src = "https://img.icons8.com/material-outlined/48/000000/circled-pause.png";
		}
		else {
			oAudio.pause();
			btn.src = "https://img.icons8.com/material-outlined/50/000000/circled-play.png";
		}
	}
	catch (e) {
		// Fail silently but show in F12 developer tools console
		if (window.console && console.error("Error:" + e));
	}
}

//Rewinds the audio file by 15 seconds.
function rewindAudio() {
	try {
		var oAudio = document.getElementById('player');
		oAudio.currentTime -= 15.0;
	}
	catch (e) {
		// Fail silently but show in F12 developer tools console
		if (window.console && console.error("Error:" + e));
	}
}

//Fast forwards the audio file by 15 seconds.
function forwardAudio() {
	try {
		var oAudio = document.getElementById('player');
		oAudio.currentTime += 15.0;
	}
	catch (e) {
		// Fail silently but show in F12 developer tools console
		if (window.console && console.error("Error:" + e));
	}
}

function iconToPause(){
	document.getElementById('play').src = "https://img.icons8.com/material-outlined/48/000000/circled-pause.png";
}

function iconToPlay(){
	document.getElementById('play').src = "https://img.icons8.com/material-outlined/50/000000/circled-play.png";
}

function changePlayrate(obj){
  document.getElementById('playRateBtn').innerHTML = obj.innerHTML;
  var aid = document.getElementById("player");
   aid.playbackRate = parseFloat(obj.innerHTML);
}


