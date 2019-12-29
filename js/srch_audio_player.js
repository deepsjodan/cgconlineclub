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

function setupSeek(prefix,rwNo) {

  document.getElementById(prefix+"seekObj_"+rwNo).max = document.getElementById(prefix+"audio_"+rwNo).duration;
}

function seekAudio(prefix,rwNo) {
  
  document.getElementById(prefix+"isSeeking_"+rwNo).value = 'Y';
  document.getElementById(prefix+"audio_"+rwNo).currentTime = document.getElementById(prefix+"seekObj_"+rwNo).value;
  document.getElementById(prefix+"isSeeking_"+rwNo).value = 'N';
}

//var prevcurrentime = 0;
function initProgressBar(prefix,rwNo) {
	
  var seek = document.getElementById(prefix+"seekObj_"+rwNo);
  var player = document.getElementById(prefix+"audio_"+rwNo);
  if (document.getElementById(prefix+"isSeeking_"+rwNo).value == 'N') {
    seek.value = player.currentTime;
  }
  var length = player.duration;
  var current_time = player.currentTime;

  // calculate total length of value
  var totalLength = calculateCurrentValue(length);

  // calculate current value time
  var currentTime = calculateCurrentValue(current_time);

  if (player.readyState === 4) {
	  if(jQuery("#"+prefix+"end-time_"+rwNo).hasClass("end-time")){
		jQuery("#"+prefix+"end-time_"+rwNo).html(totalLength);
	  }
      jQuery("#"+prefix+"start-time_"+rwNo).html(currentTime);
  }
  //checking if the current time is bigger than the previous or else there will be sync different between remaining and current
  //if (currentTime > prevcurrentime) {
	 if(jQuery("#"+prefix+"end-time_"+rwNo).hasClass("rem-time")){
    //calculate the remaining time
    var rem_time = length - current_time;
    jQuery("#"+prefix+"end-time_"+rwNo).html('Remaining : '+calculateCurrentValue(rem_time));
	 }
  //}
  //setting the previouscurrent time to this current time
  //prevcurrentime = currentTime;
}


//seek js
setInterval(function() {
  SetSeekColor();
}, 1);

function SetSeekColor() {
  
    var prefix = (document.getElementById('tbl_audio') == null) ? 'bm_' : 'srch_';
	var totRw = (prefix == 'srch_') ? document.getElementById('tbl_audio').rows.length : 
					document.getElementById('tbl_bm').rows.length;
	
	setColor(totRw,prefix);
	if(prefix != 'srch_'){
		setColor((document.getElementById('tbl_hglt').rows.length),'hglt_');
	}
}

function setColor(totRw,prefix) {
  try {
    
	for(var i = 0 ; i <= totRw ; i++){
    var val =
      (jQuery("#"+prefix+"seekObj_"+i).val() - jQuery("#"+prefix+"seekObj_"+i).attr("min")) /
      (jQuery("#"+prefix+"seekObj_"+i).attr("max") - jQuery("#"+prefix+"seekObj_"+i).attr("min"));
    var percent = val * 100;
    jQuery("#"+prefix+"seekObj_"+i).css(
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

    jQuery("#"+prefix+"seekObj_"+i).css(
      "background-image",
      "-moz-linear-gradient(left center, #0E0E0E 0%, #0E0E0E " +
        percent +
        "%, #d3d3d3 " +
        percent +
        "%, #d3d3d3 100%)"
    );
	}
  } catch (e) {}
}

function showhideRemaining(prefix,rwNo) {
  
  jQuery("#"+prefix+"end-time_"+rwNo).toggleClass("end-time rem-time");
}

//Play and pause function 
function playAudio(prefix,rwNo,strtTym,endTym) {
	try {
		
		prefix = (prefix == 'search') ? '' : prefix ;
		//return objects we need to work with 
		var oAudio = document.getElementById(prefix+'audio_'+rwNo); 
		if(oAudio.currentTime < strtTym){
			oAudio.load();
			oAudio.currentTime = strtTym;
		}
		if(endTym > 0){
			var delaySec = endTym - strtTym;
			var delayMillis = delaySec * 1000;
		}
		
		var btn = document.getElementById(prefix+'play_'+rwNo);
		//Tests the paused attribute and set state. 
		if (oAudio.paused) {
			oAudio.play();
			btn.src = "https://img.icons8.com/material-outlined/48/000000/circled-pause.png";
			if(endTym > 0 && oAudio.currentTime <= endTym && oAudio.currentTime >= strtTym){
				setTimeout(function(){
					  oAudio.pause();
					  btn.src = "https://img.icons8.com/material-outlined/50/000000/circled-play.png";

				}, delayMillis);
			}
			/* var interval = setInterval(function(){
			 if(oAudio.currentTime > endTym){
					  oAudio.pause();
					  clearInterval(interval);
					  btn.src = "https://img.icons8.com/material-outlined/50/000000/circled-play.png";
			 }}, 1000); */
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
function rewindAudio(prefix,rwNo) {
	try {
		prefix = (prefix == 'search') ? '' : prefix ;
		var oAudio = document.getElementById(prefix+'audio_'+rwNo);
		oAudio.currentTime -= 15.0;
	}
	catch (e) {
		// Fail silently but show in F12 developer tools console
		if (window.console && console.error("Error:" + e));
	}
}

//Fast forwards the audio file by 15 seconds.
function forwardAudio(prefix,rwNo) {
	try {
		prefix = (prefix == 'search') ? '' : prefix ;
		var oAudio = document.getElementById(prefix+'audio_'+rwNo);
		oAudio.currentTime += 15.0;
	}
	catch (e) {
		// Fail silently but show in F12 developer tools console
		if (window.console && console.error("Error:" + e));
	}
}

function changePlayrate(obj,prefix,rwNo){
 
  prefix = (prefix == 'search') ? '' : prefix ;
  document.getElementById(prefix+'playRateBtn_'+rwNo).innerHTML = obj.innerHTML;
  var aid = document.getElementById(prefix+"audio_"+rwNo);
  aid.playbackRate = parseFloat(obj.innerHTML);
}


