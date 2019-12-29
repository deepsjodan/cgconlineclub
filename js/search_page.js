function openAudio(frmid,rwCnt){
   var frm = document.getElementById(frmid);
   document.getElementById('sess_audio_id').value = document.getElementById('wau_audio_id_'+rwCnt).value;
   document.getElementById('sess_audio_title').value = document.getElementById('audio_title_'+rwCnt).value;
document.getElementById('sess_audio_file').value = document.getElementById('wau_audio_file_'+rwCnt).value;
   frm.action = '/coachingcall/';
   frm.submit();
 }

function openTopicSrchRslt(frmid,topic){
    var frm = document.getElementById(frmid);
   document.getElementById('sess_topic').value = topic;
   
   frm.action = '/topic-search-result/';
   frm.submit();
  }

function openCallwithSrchRslt(frmid,callwith){
   
   var frm = document.getElementById(frmid);
   document.getElementById('sess_call_with').value = callwith;
   
   frm.action = '/callwith-search-result/';
   frm.submit();
  }

function splayPubBm(rwCnt,locationSecs){
   
   //pauseOtherBm();
   var aid = document.getElementById('audio_'+rwCnt);
   var icon = document.getElementById('play_icon_'+rwCnt);
   
   if(aid.paused){
	   aid.load();
       aid.currentTime=locationSecs;
       aid.play();
       icon.innerHTML = '<i class="fa fa-pause fa-lg"></i>';
   }else{
       aid.pause();
       icon.innerHTML = '<i class="fa fa-play fa-lg"></i>';
   }
}

function pauseOtherBm(){
	
	var tblLen = document.getElementById('tbl_audio').rows.length;
	for(var i = 1 ; i <= tblLen ; i++){
		
		var aid = document.getElementById('audio_'+i);
		var icon = document.getElementById('play_icon_'+i);
		if(!aid.paused){
			aid.pause();
			icon.innerHTML = '<i class="fa fa-play fa-lg"></i>';
		}
	}
}

function srchLib(frmid){
	
   var frm = document.getElementById(frmid);
   document.getElementById('sess_lib_srch').value = document.getElementById('libSrch').value;
   
   frm.action = '/my-library/';
   frm.submit();
}


function clrSrch(frmid){
	
	var frm = document.getElementById(frmid);
	document.getElementById('sess_lib_srch').value = "";
	
	frm.action = '/my-library/';
    frm.submit();
}

function addToMyLib(audioId,bmTitle,bmLocSec,bmLoc){
	
	var data = {
		'action': 'cgc_insert_user_bookmark',
		'audio_id': audioId,
		'bm_title': bmTitle,
		'bm_locsecs': bmLocSec,
		'bm_loc': bmLoc
	    };
  
  jQuery.post(ajaxurl, data, function(userBmId) {
    
	console.log( userBmId );
	if(userBmId > 0){
		jQuery('#msg_add_to_mylib').text('Bookmark added to your library successfully!');
	}else{
		jQuery('#msg_add_to_mylib').text('Bookmark already added to your library!');
	}
	jQuery('#alert_add_to_mylib').show();
    setTimeout(function(){
    	jQuery('#alert_add_to_mylib').hide();
    }, 3000);
  });
	
}
