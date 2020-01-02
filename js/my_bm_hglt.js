var aid = document.getElementById("player");
var strtHglt = '';
var timer;



function playPubBm(locationSecs){
 if(document.getElementById('show_msg').innerHTML != ''){
       clearHglt();
     }
   aid.currentTime=locationSecs;
   aid.play();
   iconToPause();
   jQuery("#publicBm").modal("hide");
}

function getRowNo(tblId){

   var tbl = document.getElementById(tblId);
   var tblLen = tbl.rows.length;
   if(tblLen > 1){
   	var lastRw = tbl.rows[tblLen-2].cells[0];
   	var inputs = lastRw.getElementsByTagName('input');
   	return  parseInt(inputs[0].value) + 1;
   }else{
   	   return 1 ;
   }

}

function bookmarkAudio() {

     if(document.getElementById('show_msg').innerHTML != ''){
       clearHglt();
     }
     
	 
     var tbl = document.getElementById('tbl_bm');

      var row = tbl.insertRow(tbl.rows.length);
      var cell1 = row.insertCell(0);
      var cell2 = row.insertCell(1);

      var rwCnt = getRowNo('tbl_bm');
      var bm = 'bm_title_' + rwCnt ;
      var bmVal = 'BookMark '+ rwCnt;
	  var bmId = 'user_bm_id_'+ rwCnt;
      var loc = 'bm_loc_'+rwCnt;
      var tym = 'bm_time_'+rwCnt;
      var tag = 'bm_tag_' + rwCnt ;
      var row = 'bm_row_'+ rwCnt;
	  var locSecs = Math.floor(aid.currentTime);
	  var locHms = secondsToHms(aid.currentTime);

      cell1.innerHTML = '<a href="#" onclick="playMyBm('+rwCnt+');"><span id="'+bm+'" name="'+bm+'">'+bmVal+'</span></a><span> / </span><span id="'+loc+'" name="'+loc+'">'+locHms+'</span><input type="hidden" id="'+row+'" name="'+row+'" value="'+rwCnt+'"><input type="hidden" id="'+bmId+'" name="'+bmId+'"><input type="hidden" id="'+tym+'" name="'+tym+'" value="'+locSecs+'"><br><div id="'+tag+'"></div>';

      cell2.innerHTML = '<div class="text-right"><button type="button" class="btn btn-link  dropdown-toggle-split" data-toggle="dropdown"><i class="fa fa-ellipsis-v"></i></button><div class="dropdown-menu"><a class="dropdown-item" data-toggle="modal" id="btn_bm_editTitle"  data-target="#editTitle" onclick="fnEditTitle(this,'+rwCnt+')">Edit Title</a><a class="dropdown-item" data-toggle="modal" data-target="#updateTag" id="btn_bm_updateTag" onclick="fnUpdateTag(this,'+rwCnt+')">Update Tag</a><a class="dropdown-item" id="btn_bm_delItem" onclick="delItem(this,'+rwCnt+')">Delete Bookmark</a></div></div>';
      
	  insertUserBm(bmId, bmVal, locSecs, locHms);
	  
    jQuery('.nav-tabs a[href="#myBm"]').tab('show');
	  jQuery('#alert_bm').show();
    setTimeout(function(){
    	jQuery('#alert_bm').hide();
    }, 3000);
}

function insertUserBm(bmId, bmVal, locSecs, locHms){
	var data = {
    'action': 'cgc_insert_user_bookmark',
    'bmTitle': bmVal,
    'locHms': locHms,
	'locSecs' : locSecs
  };
  
  
  jQuery.post(ajaxurl, data, function(userbmId) {
	document.getElementById(bmId).value = userbmId;
  });


}


function secondsToHms(d) {
    d = Number(d);
    var h = Math.floor(d / 3600);
    var m = Math.floor(d % 3600 / 60);
    var s = Math.floor(d % 3600 % 60);

    var hDisplay = (h > 0 ? (h > 9 ? h : '0'+h) : '00');
    var mDisplay = (m > 0 ? (m > 9 ? m : '0'+m) : '00');
    var sDisplay = (s > 0 ? (s > 9 ? s : '0'+s) : '00');
    return hDisplay + ':' + mDisplay + ':' + sDisplay;
}

function secondsToMins(d) {
    d = Number(d);

    var m = Math.floor(d % 3600 / 60);
    var s = Math.floor(d % 3600 % 60);

    var tym = (m > 0) ? ((m == 1) ? m + 'min' : m + ' mins') :
                        ((s == 1) ? s + 'sec' : s + ' secs');

    return tym;
}

function playMyBm(rwCnt){
   var tym = document.getElementById('bm_time_'+rwCnt).value;
   aid.currentTime=tym;
   aid.play();
   iconToPause();
}

function delItem(btn,rwCnt){

  if (confirm("Are you sure to delete?")) {
   
	  if(btn.id == 'btn_bm_delItem'){
		  var user_bm_id = document.getElementById('user_bm_id_'+rwCnt).value; 
		  if(user_bm_id) delUserBmOHgltONt('bm', user_bm_id);  
	  }else if(btn.id == 'btn_hglt_delItem'){
		  var user_hglt_id = document.getElementById('user_hglt_id_'+rwCnt).value; 
		  if(user_hglt_id) delUserBmOHgltONt('hglt', user_hglt_id);  
	  }else{
		  var user_notes_id = document.getElementById('user_notes_'+rwCnt).value; 
		  if(user_notes_id) delUserBmOHgltONt('notes', user_notes_id);  
	  }
	  var row = btn.parentNode.parentNode.parentNode.parentNode;
	  row.parentNode.removeChild(row);
  }
  
}

function delUserBmOHgltONt(pType, user_db_id){
	var data = {
    'action': 'cgc_delete_user_bm_or_hglt_or_nt',
    'user_db_id': user_db_id,
	'ptype': pType
	};
  
  
  jQuery.post(ajaxurl, data, function(response) {
    console.log( response );
  });

}


function fnEditTitle(obj,rwCnt){
   if(obj.id == 'btn_bm_editTitle'){
     document.getElementById('mod_title').value = document.getElementById('bm_title_'+rwCnt).innerHTML;
     document.getElementById('rowNo').value = rwCnt;
     document.getElementById('pType').value = "bm";
  }else{
     document.getElementById('mod_title').value = document.getElementById('hglt_title_'+rwCnt).innerHTML;
     document.getElementById('rowNo').value = rwCnt;
     document.getElementById('pType').value = "hglt";
  }
}

function saveTitle(){
  var pType =  document.getElementById('pType').value;
  var rowNo = document.getElementById('rowNo').value;
  var modTitle = document.getElementById('mod_title').value;
  if(pType == "bm"){
    document.getElementById('bm_title_'+rowNo).innerHTML = modTitle;
	var user_bm_id = document.getElementById('user_bm_id_'+rowNo).value;
	if(user_bm_id) updateDbTitle(pType, user_bm_id, modTitle);
  }else{
  	document.getElementById('hglt_title_'+rowNo).innerHTML = modTitle;
	var user_hglt_id = document.getElementById('user_hglt_id_'+rowNo).value;
	if(user_hglt_id) updateDbTitle(pType, user_hglt_id, modTitle);
  }
   jQuery("#editTitle").modal('hide');
}

function updateDbTitle(pType, user_db_id, modTitle){
	var data = {
    'action': 'cgc_update_user_title',
    'title': modTitle,
    'user_db_id': user_db_id,
	'ptype': pType
   };
  
  
  jQuery.post(ajaxurl, data, function(response) {
	console.log( response );
  });

}

function fnUpdateTag(obj,rwCnt){

    jQuery("#mod_tag").tagsinput('removeAll');

    if(obj.id == 'btn_bm_updateTag'){

     var spans = document.getElementById('bm_tag_'+rwCnt).getElementsByTagName('span');

     for (var i=0; i<spans.length; i++){
          jQuery('#mod_tag').tagsinput('add', spans[i].innerHTML);
     }

      document.getElementById('rowNo').value = rwCnt;
      document.getElementById('pType').value = "bm";

    }else{

    	var spans = document.getElementById('hglt_tag_'+rwCnt).getElementsByTagName('span');

     for (var i=0; i<spans.length; i++){
          jQuery('#mod_tag').tagsinput('add', spans[i].innerHTML);
     }

      document.getElementById('rowNo').value = rwCnt;
      document.getElementById('pType').value = "hglt";

    }
}


jQuery('input').on('itemAdded', function(event) {
	
   if(document.getElementById('pType').value == '') return;
   updateTopic(event.item, 'Y');
});


jQuery('input').on('itemRemoved', function(event) {
  // event.item: contains the item
  
  if(document.getElementById('pType').value == '') return;
  updateTopic(event.item, 'N');
});

function updateTopic(topic, isTopicAdded){
   
   var pType = document.getElementById('pType').value;
   var rowNo = document.getElementById('rowNo').value;
   
   
   var audio_id = (document.getElementById('frmMyLib') == null) ?
					document.getElementById('audio_id').value : 
					((pType == "bm") ? document.getElementById('wau_audio_id_'+rowNo).value :
					document.getElementById('hglt_wau_audio_id_'+rowNo).value);
   
   if(pType == 'bm'){
	
	   var user_bm_id = document.getElementById('user_bm_id_'+rowNo).value;
	   if(user_bm_id) updateDbTopic(pType, audio_id, user_bm_id, topic, isTopicAdded);
   }else{
	   
	   var user_hglt_id = document.getElementById('user_hglt_id_'+rowNo).value;
	   if(user_hglt_id) updateDbTopic(pType, audio_id, user_hglt_id, topic, isTopicAdded);
   }
}

function updateDbTopic(pType, audio_id, user_db_id, topic, isTopicAdded){
	
  var data = {
		'action': 'cgc_update_user_topic',
		'topic': topic,
		'user_db_id': user_db_id,
		'audio_id': audio_id,
		'ptype': pType,
		'isTopicAdded': isTopicAdded
	    };
		
  jQuery.post(ajaxurl, data, function(response) {
    console.log( response );
  });

}

jQuery('#updateTag').on('hidden.bs.modal', function () {
   
   var pType = document.getElementById('pType').value;
   var rowNo = document.getElementById('rowNo').value;
   var tags =  document.getElementById('mod_tag').value;
   
   
   var splTags = tags.split(",");
   if(pType == 'bm'){
	   var tagDiv = document.getElementById('bm_tag_'+rowNo);
   }else{
	   var tagDiv = document.getElementById('hglt_tag_'+rowNo);
   }
   
   tagDiv.innerHTML = '';

   for (var i=0; i<splTags.length; i++){
        var newSpan = document.createElement('span');
        newSpan.innerHTML = splTags[i];
        newSpan.setAttribute('class', 'badge badge-info');
		tagDiv.appendChild(newSpan);
		space = document.createTextNode('  ');
		tagDiv.appendChild(space);
   }
   
   document.getElementById('rowNo').value = '';
   document.getElementById('pType').value = '';
  
});

/*function saveTags(){

   var pType = document.getElementById('pType').value;
   var rowNo = document.getElementById('rowNo').value;
   var tags =  document.getElementById('mod_tag').value;
   
   var audio_id = ( typeof(document.getElementById('audio_id')) != "undefined") ?
					document.getElementById('audio_id').value : 
					document.getElementById('wau_audio_id_'+rowNo).value ;
   var splTags = tags.split(",");
   if(pType == 'bm'){
	   var tagDiv = document.getElementById('bm_tag_'+rowNo);
	   var user_bm_id = document.getElementById('user_bm_id_'+rowNo).value;
	   if(user_bm_id) updateDbTags(pType, audio_id, user_bm_id, tags);
   }else{
	   var tagDiv = document.getElementById('hglt_tag_'+rowNo);
	   var user_hglt_id = document.getElementById('user_hglt_id_'+rowNo).value;
	   if(user_hglt_id) updateDbTags(pType, audio_id, user_hglt_id, tags);
   }
   
   tagDiv.innerHTML = '';

   for (var i=0; i<splTags.length; i++){
        var newSpan = document.createElement('span');
        newSpan.innerHTML = splTags[i];
        newSpan.setAttribute('class', 'badge badge-info');
		tagDiv.appendChild(newSpan);
		space = document.createTextNode('  ');
		tagDiv.appendChild(space);
   }

   jQuery("#updateTag").modal('hide');
}


function updateDbTags(pType, audio_id, user_db_id, tags){
	
  var data = {
		'action': 'cgc_update_user_topics',
		'tags': tags,
		'user_db_id': user_db_id,
		'audio_id': audio_id,
		'ptype': pType
	    };
		
  jQuery.post(ajaxurl, data, function(response) {
    console.log( response );
  });

}*/



function highlightAudio(){
   if(strtHglt != '') {
     clearInterval(timer);
	 document.getElementById('show_msg').innerHTML = '';
     document.getElementById("show_timer").innerHTML = '';
     //document.getElementById('btn_hglt').innerHTML = '<img src="https://img.icons8.com/color/48/000000/border-color.png"><br>Start Highlight';
     endHighlight();
   }else{
      if (aid.paused) {
         aid.play();
         iconToPause();
      }
   		strtHglt = aid.currentTime;
   		//document.getElementById('btn_hglt').innerHTML = '<img src="https://img.icons8.com/color/48/000000/border-color.png"><br>End Highlight';
      document.getElementById('show_msg').innerHTML = 'Highlight Started at '+secondsToHms(strtHglt)+', Click "End Highlight" to finish.';
       var seconds = 0;
       document.getElementById('show_timer').innerHTML =  secondsToHms(seconds);
      timer = setInterval(function(){
           seconds++;
          document.getElementById("show_timer").innerHTML = secondsToHms(seconds);
		  if ( jQuery('#minmax').hasClass("min") ){  
			document.getElementById("endHgltTitle").innerHTML = secondsToHms(seconds);
		  }
      },1000);
       jQuery("#endHglt").modal('show');
   }
}


function endHighlight(){

     clearInterval(timer);
	 document.getElementById('show_msg').innerHTML = '';
     document.getElementById("show_timer").innerHTML = '';
     //document.getElementById('btn_hglt').innerHTML = '<img src="https://img.icons8.com/color/48/000000/border-color.png"><br>Start Highlight';

      var tbl = document.getElementById('tbl_hglt'); // table reference

      var row = tbl.insertRow(tbl.rows.length);
      var cell1 = row.insertCell(0);
      var cell2 = row.insertCell(1);


      var rwCnt = getRowNo('tbl_hglt');
      var hglt = 'hglt_title_' + rwCnt ;
	  var hgltId = 'user_hglt_id_'+ rwCnt;
      var hgltVal = 'Highlight '+ rwCnt;
      var tag = 'hglt_tag_' + rwCnt ;
      var loc = 'hglt_loc_'+rwCnt;
      var sTym = 'hglt_stime_'+rwCnt;
      var eTym = 'hglt_etime_'+rwCnt;
      var aLen = 'hglt_len_'+rwCnt;
      var row = 'hglt_row_'+ rwCnt;
      var lenVal = secondsToMins(Math.floor(aid.currentTime - strtHglt)) ;
      var sTymVal = Math.floor(strtHglt);
      var eTymVal = Math.floor(aid.currentTime);
	  var locHms = secondsToHms(strtHglt);
	  
      cell1.innerHTML = '<a href="#" onclick="playMyHglt('+rwCnt+');"><span id="'+hglt+'" name="'+hglt+'">'+hgltVal+'</span></a><span> / </span><span id="'+loc+'" name="'+loc+'">'+locHms+'</span><span> : </span><span id="'+aLen+'" name="'+aLen+'">'+lenVal+'</span><input type="hidden" id="'+row+'" name="'+row+'" value="'+rwCnt+'"><input type="hidden" id="'+hgltId+'" name="'+hgltId+'"><input type="hidden" id="'+sTym+'" name="'+sTym+'" value="'+sTymVal+'"><input type="hidden" id="'+eTym+'" name="'+eTym+'" value="'+eTymVal+'"><br><div id="'+tag+'"></div>';

      cell2.innerHTML = '<div class="text-right"><button type="button" class="btn btn-link  dropdown-toggle-split" data-toggle="dropdown"><i class="fa fa-ellipsis-v"></i></button><div class="dropdown-menu"><a class="dropdown-item" href="#" data-toggle="modal" id="btn_hglt_editTitle"  data-target="#editTitle" onclick="fnEditTitle(this,'+rwCnt+')">Edit Title</a><a class="dropdown-item" href="#" data-toggle="modal" data-target="#updateTag" id="btn_hglt_updateTag" onclick="fnUpdateTag(this,'+rwCnt+')">Update Tag</a><a class="dropdown-item" href="#" id="btn_hglt_delItem" onclick="delItem(this,'+rwCnt+')">Delete Highlight</a></div></div>';

	  insertUserHglt(hgltId, sTymVal, eTymVal, locHms, lenVal, hgltVal);
	  
	  jQuery('.nav-tabs a[href="#myHglt"]').tab('show');
      jQuery('#alert_hglt').show();
    setTimeout(function(){
    	jQuery('#alert_hglt').hide();
    }, 3000);

      strtHglt = '';
      jQuery("#endHglt").modal('hide');
}

function insertUserHglt(hgltId, sTymVal, eTymVal, locHms, lenVal, hgltVal){
	var data = {
    'action': 'cgc_insert_user_highlight',
    'hgltTitle': hgltVal,
	'sTym' : sTymVal,
	'eTym' : eTymVal,
    'locHms': locHms,
	'length' : lenVal
  };
  
  
  jQuery.post(ajaxurl, data, function(userhgltId) {
	document.getElementById(hgltId).value = userhgltId;
  });


}

function clearHglt(){
   clearInterval(timer);
   strtHglt = '';
   document.getElementById('show_msg').innerHTML = '';
   document.getElementById("show_timer").innerHTML = '';
   //document.getElementById('btn_hglt').innerHTML = '<img src="https://img.icons8.com/color/48/000000/border-color.png"><br>Start Highlight';

}

function playMyHglt(rwCnt){
   var sTym = document.getElementById('hglt_stime_'+rwCnt).value;
   var eTym = document.getElementById('hglt_etime_'+rwCnt).value;
   aid.currentTime=sTym;
   aid.play();
   iconToPause();
   const interval = setInterval(function(){
     if(aid.currentTime > eTym){
      		  aid.pause();
			  iconToPlay();
              clearInterval(interval);
     }}, 1000);
     jQuery("#myHglt").modal("hide");
}


function minimizeEndHglt(obj){

   var $modal, $apnData;
   $apnData = jQuery(obj).closest(".endHgltModal");
   $modal = "#minmax";
  
	if ( jQuery(obj).find("i").hasClass( 'fa-minus') ){ 
		jQuery('#minmax').toggleClass("min");
		jQuery(".minmaxCon").append($apnData);  
		var sTimer = document.getElementById('show_timer').innerHTML;
		document.getElementById('endHgltTitle').innerHTML = '<h3>'+ sTimer +'</h3>';
		//document.getElementById('endHgltTitle').innerHTML = '<h3>'+ sTimer +'</h3><div><button type="button" class="btn btn-primary" id="btn_endHglt" onclick="endHighlight()">End Highlight</button>&nbsp;<button type="button" class="btn btn-link btn-sm" data-dismiss="modal" id="closeEndHglt">Cancel</button></div>';
		
		//jQuery("#endHgltTitle").html('<h3>'+ sTimer +'</h3><div><button type="button" class="btn btn-primary" id="btn_endHglt" onclick="endHighlight()">End Highlight</button>&nbsp;<button type="button" class="btn btn-link btn-sm" data-dismiss="modal" id="closeEndHglt">Cancel</button></div>');
		jQuery(obj).find("i").toggleClass( 'fa-minus').toggleClass( 'fa-clone');
	  } 
	  else { 
		//document.getElementById('endHgltTitle').innerHTML  = 'End Highlight'; 
		document.getElementById('endHgltTitle').innerHTML  = '<h3>End Highlight</h3>'; 
		jQuery(obj).find("i").toggleClass( 'fa-clone').toggleClass( 'fa-minus');
		jQuery('#minmax').removeClass("min");
	  }

}

jQuery('#endHglt').on('hidden.bs.modal', function () {
  jQuery("#minmax").removeClass("min");
  document.getElementById('endHgltTitle').innerHTML  = 'End Highlight';     
  jQuery('.modalMinimize').find("i").removeClass('fa fa-clone').addClass( 'fa fa-minus');
  clearHglt();
  document.getElementById('rowNo').value = '';
  document.getElementById('pType').value = '';
});


function fnEditNote(rwCnt){
   
     document.getElementById('mod_note').value = document.getElementById('notes_'+rwCnt).innerHTML;
     document.getElementById('rowNo').value = rwCnt;
     document.getElementById('pType').value = "nt";
}


function saveNote(){
	
	var rowNo = document.getElementById('rowNo').value;
	var audio_id = document.getElementById('nt_wau_audio_id_'+rowNo).value;
	var mod_note = document.getElementById('mod_note').value 
    document.getElementById('notes_'+rowNo).innerHTML = document.getElementById('mod_note').value;
	var user_nt_id = document.getElementById('user_notes_'+rowNo).value;
	if(user_nt_id) saveDbNote(audio_id, mod_note);
 
    jQuery("#editNote").modal('hide');
}
  
function saveDbNote(audio_id, noteVal){
	
	var data = {
		'action': 'cgc_update_user_notes',
		'my_notes': noteVal,
		'audio_id': audio_id
	};
		
  jQuery.post(ajaxurl, data, function(response) {
    console.log( response );
  });
}

function updateNote(noteVal){
	
	var audio_id = document.getElementById('audio_id').value;
	saveDbNote(audio_id, noteVal);
}


function playMyLibHglt(rwCnt){
	
   var aid = document.getElementById('hglt_audio_'+rwCnt);
   var icon = document.getElementById('hglt_play_icon_'+rwCnt);
   //var interval;
   if(aid.paused){
	   aid.load();
	   var sTym = document.getElementById('hglt_stime_'+rwCnt).value;
	   var eTym = document.getElementById('hglt_etime_'+rwCnt).value;
	   //aid.currentTime=(aid.currentTime == 0 || aid.currentTime == eTym) ? sTym : aid.currentTime;
	   aid.currentTime = sTym;
	   aid.play();
	   icon.innerHTML = '<i class="fa fa-pause fa-lg"></i>';
	   var interval = setInterval(function(){
		 if(aid.currentTime > eTym){
				  aid.pause();
				  clearInterval(interval);
				  icon.innerHTML = '<i class="fa fa-play fa-lg"></i>';
		 }}, 1000);
   }else{
	   //if(interval) clearInterval(interval);
       aid.pause();
       icon.innerHTML = '<i class="fa fa-play fa-lg"></i>';
   }
}





