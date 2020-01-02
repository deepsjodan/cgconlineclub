<?php
/*
Plugin Name: CGC Online Club
Description: CGC Online Club
Author: Deepa Jotheeswaran
Version: 0.1
*/

function my_theme_enqueue_styles() {
 
    $parent_style = 'parent-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.
 
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
	
	// all styles
    wp_enqueue_style( 'bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css');
	wp_enqueue_style( 'tagsinput', 'https://cgconline.club/wp-content/themes/twentyseventeen/css/tagsinput.css');
	wp_enqueue_style( 'font-awesome-5', 'https://use.fontawesome.com/releases/v5.6.3/css/all.css', array(), null );
	//if(is_page(14)){
		wp_enqueue_style( 'myBmHgltCSS', 'https://cgconline.club/wp-content/themes/twentyseventeen/css/my_bm_hglt.css');
	//}
    
    // all scripts
	
	wp_enqueue_script( 'jquery','https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js');
	wp_enqueue_script( 'ajax_popper','https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js');
	wp_enqueue_script( 'bootstrap','https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js');
    wp_enqueue_script( 'fontawesome','https://kit.fontawesome.com/a076d05399.js');
	wp_enqueue_script( 'typeahead','https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.10.4/typeahead.bundle.min.js');
	wp_enqueue_script( 'tagsinput','https://cgconline.club/wp-content/themes/twentyseventeen/js/tagsinput.js');
	if(is_page(14)){
		wp_enqueue_script( 'audioPlayer','https://cgconline.club/wp-content/themes/twentyseventeen/js/audio_player.js',array( 'jquery' ),'',true);
	}else{
		wp_enqueue_script( 'srchaudioPlayer','https://cgconline.club/wp-content/themes/twentyseventeen/js/srch_audio_player.js',array( 'jquery' ),'',true);
	}
	wp_enqueue_script( 'myBmHglt','https://cgconline.club/wp-content/themes/twentyseventeen/js/my_bm_hglt.js',array( 'jquery' ),'',true);
	wp_enqueue_script( 'searchpage','https://cgconline.club/wp-content/themes/twentyseventeen/js/search_page.js',array( 'jquery' ),'',true);
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

function add_font_awesome_5_cdn_attributes( $html, $handle ) {
    if ( 'font-awesome-5' === $handle ) {
        return str_replace( "media='all'", "media='all' integrity='sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/' crossorigin='anonymous'", $html );
    }
    return $html;
}
add_filter( 'style_loader_tag', 'add_font_awesome_5_cdn_attributes', 10, 2 );

function register_session(){
    if( !session_id() )
        session_start();
}
add_action('init','register_session');


function fncgc_form_submit(){
	
	global $wpdb;
	
	if(array_key_exists('sess_audio_id',$_POST)){
		$_SESSION['audio_id'] = $_POST['sess_audio_id'];
	}
	
	if(array_key_exists('sess_audio_title',$_POST)){
		$_SESSION['audio_title'] = $_POST['sess_audio_title'];
	}
	
	if(array_key_exists('sess_audio_file',$_POST)){
		$_SESSION['audio_file'] = $_POST['sess_audio_file'];
	}
	
	if(array_key_exists('sess_topic',$_POST)){
		$_SESSION['topic'] = $_POST['sess_topic'];
	}
	
	if(array_key_exists('sess_call_with',$_POST)){	
		$_SESSION['call_with'] = $_POST['sess_call_with'];
	}
	if(array_key_exists('srch_all',$_POST)){	
		$_SESSION['srch_all'] = $_POST['srch_all'];
	}
	if(array_key_exists('sess_lib_srch',$_POST)){	
		$_SESSION['lib_srch'] = $_POST['sess_lib_srch'];
	}
	
		
}

add_action('wp_head','fncgc_form_submit');

add_action( 'wp', 'fncgc_unset_search_all' );
function fncgc_unset_search_all() {
    //Reset sessions on refresh page
    unset( $_SESSION['lib_srch'] );
	log_me("inside unset  ");
}



function fncgc_display_audio(){
	
	global $wpdb;
	
	
	$audios = $wpdb->get_results("SELECT  a.wau_audio_id,
								   CONCAT('Coaching Call',' - ', 
								   DATE_FORMAT(a.wau_audio_date, '%M %D, %Y')) audio_title,
								   a.wau_audio_file
									FROM wpcr_audio a
									ORDER BY a.wau_audio_date desc");
	
	$content = '<div class="container-fluid">
		              <div class="row">
		               <div  class="table-responsive-sm">          
						<table id="tbl_audio" class="table">
						<thead>
						 <th>
						   <h2>Coaching Calls</h2>
						 </th>
						</thead>
						<tbody>';
	$rwCnt = 0;
	$frmId = 'frmAudio';
	if(!empty($audios))                       
	{  
	  foreach($audios as $audio){
		  $rwCnt++;
		  $content .= '<tr><td>';
		  $audio_id = 'wau_audio_id_'.$rwCnt ;
		  $audio_title = 'audio_title_'.$rwCnt ;
		  $audio_file = 'wau_audio_file_'.$rwCnt ;
		  $content .= '<a href="#" class="btn-lg" onclick="openAudio(\''.$frmId.'\','.$rwCnt.');">'.$audio->audio_title.'</a><br>';
		  $content .=  '<input type="hidden" name="'.$audio_id.'" id="'.$audio_id.'" value="'.$audio->wau_audio_id.'">';
		  $content .=  '<input type="hidden" name="'.$audio_title.'" id="'.$audio_title.'" value="'.$audio->audio_title.'">';
		  $content .=  '<input type="hidden" name="'.$audio_file.'" id="'.$audio_file.'" value="'.$audio->wau_audio_file.'">';
		  
	  
	  $topics = $wpdb->get_results("SELECT DISTINCT b.wat_topic
									  FROM wpcr_audio a, wpcr_audio_topics b
									   WHERE a.wau_audio_id = ".$audio->wau_audio_id.
									  " AND a.wau_audio_id = b.wat_audio_id
									   ORDER BY b.wat_topic");  
	  
	  if(!empty($topics))                       
	  {  
		 $content .= '<br>';
		 $content .= '<span>Topics : </span>';
		  foreach($topics as $topic){
			  
			  $content .= '<a href="#" onclick="openTopicSrchRslt(\''.$frmId.'\',\''.$topic->wat_topic.'\')" class="btn btn-info btn-sm" role="button">'.$topic->wat_topic.'</a>';
			  $content .= '&nbsp;'; 
		  }
	  }
	  
	  $call_with = $wpdb->get_results("SELECT DISTINCT b.wpb_call_with
									  FROM wpcr_audio a, wpcr_public_bookmarks b
									   WHERE a.wau_audio_id = ".$audio->wau_audio_id.
									   " AND a.wau_audio_id = b.wpb_audio_id
									    AND b.wpb_call_with IS NOT NULL
									   ORDER BY b.wpb_call_with");  
	  
	  if(!empty($call_with))                       
	  {  
		 $content .= '<br><br>';
		 $content .= '<span>Call With : </span>';
		  foreach($call_with as $mem){
			  if(!empty($mem->wpb_call_with)){
			  $content .= '<a href="#" onclick="openCallwithSrchRslt(\''.$frmId.'\',\''.$mem->wpb_call_with.'\')" class="btn btn-link btn-sm" role="button">'.$mem->wpb_call_with.'</a>';
			  $content .= '&nbsp;'; 
			  }
		  }
	  }
	  
	  
	  $content .= '</td></tr>';
	  }
	}
	
										
	$content .= '</tbody>
    </table></div>
    </div></div>';
	
	 return $content;
}

add_shortcode('cgc_display_audio','fncgc_display_audio');


function fncgc_display_audio_title(){
	
	 return '<h3 id ="audiosrc">'.$_SESSION['audio_title'].'</h3>
	        <input type="hidden" name="audio_id" id="audio_id" value="'.$_SESSION['audio_id'].'">';
}

add_shortcode('cgc_display_audio_title','fncgc_display_audio_title');


function fncgc_all_topics(){
	global $wpdb;
	
	$allTopics = $wpdb->get_results("SELECT DISTINCT a.wat_topic
									  FROM wpcr_audio_topics a
									   ORDER BY a.wat_topic");
	if(!empty($allTopics)){
		
		$audio_topic = array_column($allTopics, 'wat_topic');
		return '<input type="hidden" name="all_topics" id="all_topics" value="'.implode(",",$audio_topic).'">';

	  }
}

add_shortcode('cgc_all_topics','fncgc_all_topics');


function fncgc_display_audio_file(){
	
	 return '<source src="'.$_SESSION['audio_file'].'" type="audio/mp3">';
}

add_shortcode('cgc_display_audio_file','fncgc_display_audio_file');


function fncgc_display_public_bm(){
	global $wpdb;
	
	$publicBms = $wpdb->get_results("SELECT wpb_public_bookmarks_id,wpb_audio_id,wpb_location,
									wpb_location_secs,wpb_title,wpb_call_with,
									 (SELECT b.wpb_location_secs 
                                      FROM wpcr_public_bookmarks b
                                       WHERE b.wpb_location_secs > a.wpb_location_secs
                                        and b.wpb_audio_id = ".$_SESSION['audio_id']."
                                        order by b.wpb_location_secs 
                                        limit 1)  next_bm
									FROM wpcr_public_bookmarks a
									WHERE wpb_audio_id = ".$_SESSION['audio_id']."
									ORDER BY wpb_location_secs");  
									
	if(!empty($publicBms))                       
	{  
	   
       $content = '<div  class="table-responsive-sm">          
					<table id="tbl_pubBm" class="table">
					<tbody>';
	  
       $frmId = 'frmMyBmHglt';
	  foreach($publicBms as $row){
		$content .= '<tr>';

		$content .= '<td><a tabindex="0" onclick="playPubBm('.$row->wpb_location_secs.');">'.$row->wpb_title.'</a><br>';
		if(!empty($row->next_bm)){
		  $length = $row->next_bm - $row->wpb_location_secs;
		  $content .= '<span class="badge badge-light">'.getMinutes($length).'</span><br>';
	   }
		
		if(get_current_user_id() > 0){
			$content .= '<a tabindex="0" onclick="addToMyLib('.$row->wpb_audio_id.',\''.$row->wpb_title.'\','.$row->wpb_location_secs.',\''.$row->wpb_location.'\');"><i class="fas fa-plus"></i> Add to My Library</a> ';
			$content .= '<br>';
		}
		
		$topics = $wpdb->get_results("SELECT DISTINCT a.wat_topic
									  FROM wpcr_audio_topics a
									   WHERE a.wat_audio_id = ".$row->wpb_audio_id.
									   " AND a.wat_public_bookmarks_id = ".$row->wpb_public_bookmarks_id.
									   " ORDER BY a.wat_topic");  
	  
	  if(!empty($topics))                       
	  {  
		 $content .= '<br>';
		 $content .= '<span>Topics : </span>';
		  foreach($topics as $topic){
			  
			  $content .= '<a href="#" onclick="openTopicSrchRslt(\''.$frmId.'\',\''.$topic->wat_topic.'\')" class="btn btn-info btn-sm" role="button">'.$topic->wat_topic.'</a>';
			  $content .= '&nbsp;'; 
		  }
	  }
	  
	  
	  
	  if(!empty($row->wpb_call_with))                       
	  {  
		 $content .= '<br>';
		 $content .= '<span>Call With : </span>';
		 $content .= '<a href="#" onclick="openCallwithSrchRslt(\''.$frmId.'\',\''.$row->wpb_call_with.'\')" class="btn btn-link btn-sm" role="button">'.$row->wpb_call_with.'</a>';
	  }
	  
	  
	  $content .= '</td>';
	  $content .= '<td>'.$row->wpb_location;
	  $content .= '</td>';
	  $content .= '</tr>';

	  }
	  $content .= '</tbody></table></div>';
	}							
	return $content;
}

add_shortcode('cgc_display_public_bm','fncgc_display_public_bm');


function fncgc_display_my_bm(){
	global $wpdb;
	$user_id = get_current_user_id();
	$content = '';
	
	if($user_id > 0){
		$results = $wpdb->get_results("SELECT a.wub_user_bookmarks_id,a.wub_location,a.wub_location_secs,
											   a.wub_title
										FROM wpcr_user_bookmarks a 
										WHERE a.wub_audio_id = ".$_SESSION['audio_id'].
										" AND a.wub_user_id = ".$user_id.
										" ORDER BY a.wub_location_secs "); 
		if(!empty($results))                       
		{  
		  $rwCnt = 1;
		  foreach($results as $row){ 
			
			$bm = 'bm_title_'.$rwCnt ;
			$bmVal = $row->wub_title;
			$bmId = 'user_bm_id_'.$rwCnt;
			$loc = 'bm_loc_'.$rwCnt;
			$tym = 'bm_time_'.$rwCnt;
			$tag = 'bm_tag_'.$rwCnt;
			$rw = 'bm_row_'.$rwCnt;
			$locSecs = $row->wub_location_secs;
			$locHms = $row->wub_location;
			$bmIdVal = $row->wub_user_bookmarks_id;
			
			$content .= '<tr><td>';
			$content .= '<a href="#" onclick="playMyBm('.$rwCnt.');"><span id="'.$bm.'" name="'.$bm.'">'.$bmVal.'</span></a>
						 <span> / </span><span id="'.$loc.'" name="'.$loc.'">'.$locHms.'</span>
			             <input type="hidden" id="'.$rw.'" name="'.$rw.'" value="'.$rwCnt.'">
						 <input type="hidden" id="'.$bmId.'" name="'.$bmId.'" value="'.$bmIdVal.'">
						 <input type="hidden" id="'.$tym.'" name="'.$tym.'" value="'.$locSecs.'">';
						 
			$topics = $wpdb->get_results("SELECT DISTINCT a.wat_topic
										  FROM wpcr_audio_topics a
										   WHERE a.wat_audio_id = ".$_SESSION['audio_id'].
										   " AND a.wat_user_bookmarks_id = ".$bmIdVal.
										   " ORDER BY a.wat_topic");  
			
			$content .= '<br><div id="'.$tag.'">';
			if(!empty($topics))                       
			  {  
				  foreach($topics as $topic){
					  
					  $content .= '<span class="badge badge-info">'.$topic->wat_topic.'</span>';
					  $content .= '&nbsp;'; 
				  }
			  }
			 $content .= '</div>';
			
			$content .= '</td><td>';
			$content .= '<div class="text-right">
							<button type="button" class="btn btn-link  dropdown-toggle-split" data-toggle="dropdown">
							<i class="fa fa-ellipsis-v"></i></button>
							<div class="dropdown-menu">
							<a class="dropdown-item" data-toggle="modal" id="btn_bm_editTitle"  data-target="#editTitle" onclick="fnEditTitle(this,'.$rwCnt.')">Edit Title</a>
							<a class="dropdown-item" data-toggle="modal" data-target="#updateTag" id="btn_bm_updateTag" onclick="fnUpdateTag(this,'.$rwCnt.')">Update Tag</a>
							<a class="dropdown-item" id="btn_bm_delItem" onclick="delItem(this,'.$rwCnt.')">Delete Bookmark</a></div></div>';
			$content .= '</td></tr>';
			$rwCnt++;
		  }
		}
		return $content;
	}
}

add_shortcode('cgc_display_my_bm','fncgc_display_my_bm');


function fncgc_display_my_hglt(){
	global $wpdb;
	$user_id = get_current_user_id();
	$content = '';
	
	if($user_id > 0){
		$results = $wpdb->get_results("SELECT a.wuh_user_highlights_id,a.wuh_start_time_secs,a.wuh_end_time_secs,
											   a.wuh_location,a.wuh_length,a.wuh_title
										FROM wpcr_user_highlights a 
										WHERE a.wuh_audio_id = ".$_SESSION['audio_id'].
										" AND a.wuh_user_id = ".$user_id.
										" ORDER BY a.wuh_start_time_secs "); 
		if(!empty($results))                       
		{  
		  $rwCnt = 1;
		  foreach($results as $row){ 
			
			$hglt = 'hglt_title_'.$rwCnt ;
			$hgltId = 'user_hglt_id_'.$rwCnt;
			$hgltVal = $row->wuh_title;
			$tag = 'hglt_tag_'.$rwCnt ;
			$loc = 'hglt_loc_'.$rwCnt;
			$sTym = 'hglt_stime_'.$rwCnt;
			$eTym = 'hglt_etime_'.$rwCnt;
			$aLen = 'hglt_len_'.$rwCnt;
			$rw = 'hglt_row_'.$rwCnt;
			$lenVal = $row->wuh_length; 
			$sTymVal = $row->wuh_start_time_secs;
			$eTymVal = $row->wuh_end_time_secs;
			$locHms = $row->wuh_location;
			$hgltIdVal = $row->wuh_user_highlights_id;
			
			$content .= '<tr><td>';
			$content .= '<a href="#" onclick="playMyHglt('.$rwCnt.');"><span id="'.$hglt.'" name="'.$hglt.'">'.$hgltVal.'</span></a>
						 <span> / </span><span id="'.$loc.'" name="'.$loc.'">'.$locHms.'</span><span> : </span>
						 <span id="'.$aLen.'" name="'.$aLen.'">'.$lenVal.'</span>
						 <input type="hidden" id="'.$rw.'" name="'.$rw.'" value="'.$rwCnt.'">
						 <input type="hidden" id="'.$hgltId.'" name="'.$hgltId.'" value="'.$hgltIdVal.'">
						 <input type="hidden" id="'.$sTym.'" name="'.$sTym.'" value="'.$sTymVal.'">
						 <input type="hidden" id="'.$eTym.'" name="'.$eTym.'" value="'.$eTymVal.'">';
						 
			$topics = $wpdb->get_results("SELECT DISTINCT a.wat_topic
										  FROM wpcr_audio_topics a
										   WHERE a.wat_audio_id = ".$_SESSION['audio_id'].
										   " AND a.wat_user_highlights_id = ".$hgltIdVal.
										   " ORDER BY a.wat_topic");  
			
			$content .= '<br><div id="'.$tag.'">';
			if(!empty($topics))                       
			  {  
				  foreach($topics as $topic){
					  
					  $content .= '<span class="badge badge-info">'.$topic->wat_topic.'</span>';
					  $content .= '&nbsp;'; 
				  }
			  }
			$content .= '</div>';
			$content .= '</td><td>';
			$content .= '<div class="text-right">
							<button type="button" class="btn btn-link  dropdown-toggle-split" data-toggle="dropdown">
							<i class="fa fa-ellipsis-v"></i></button><div class="dropdown-menu">
							<a class="dropdown-item" data-toggle="modal" id="btn_hglt_editTitle"  data-target="#editTitle" onclick="fnEditTitle(this,'.$rwCnt.')">Edit Title</a>
							<a class="dropdown-item" data-toggle="modal" data-target="#updateTag" id="btn_hglt_updateTag" onclick="fnUpdateTag(this,'.$rwCnt.')">Update Tag</a>
							<a class="dropdown-item" id="btn_hglt_delItem" onclick="delItem(this,'.$rwCnt.')">Delete Highlight</a></div></div>';
			$content .= '</td></tr>';
			$rwCnt++;
		  }
		}
		return $content;
	}
}

add_shortcode('cgc_display_my_hglt','fncgc_display_my_hglt');


function fncgc_display_notes(){
	global $wpdb;
	$user_id = get_current_user_id();
	$notes = '';
	
	if($user_id > 0){
		
		$notes = $wpdb->get_var("SELECT wun_notes
								FROM wpcr_user_notes 
								WHERE wun_audio_id = ".$_SESSION['audio_id'].
								" AND wun_user_id = ".$user_id);
										 
	}
	
	return $notes;
}

add_shortcode('cgc_display_notes','fncgc_display_notes');


function getMinutes( $seconds )
{
	$minutes = floor($seconds/60);
	$secondsleft = $seconds%60;
	if($minutes<10)
		$minutes = "0" . $minutes;
	if($secondsleft<10)
		$secondsleft = "0" . $secondsleft;
	return "$minutes:$secondsleft mins";
}


function fncgc_display_topic_srch_result(){
	global $wpdb;
	
	
	$results = $wpdb->get_results("SELECT a.wpb_audio_id,a.wpb_title,a.wpb_location_secs,
										   a.wpb_location,a.wpb_call_with,
										   c.wau_audio_date,c.wau_audio_file,
										   CONCAT('Coaching Call',' - ', 
								   DATE_FORMAT(c.wau_audio_date, '%M %D, %Y')) audio_title,
								    a.wpb_public_bookmarks_id,
									(SELECT x.wpb_location_secs 
                                      FROM wpcr_public_bookmarks x
                                       WHERE x.wpb_location_secs > a.wpb_location_secs
                                        and x.wpb_audio_id = a.wpb_audio_id
                                        order by x.wpb_location_secs 
                                        limit 1)  next_bm
									FROM wpcr_public_bookmarks a,wpcr_audio_topics b,wpcr_audio c
									WHERE a.wpb_public_bookmarks_id = b.wat_public_bookmarks_id
									AND a.wpb_audio_id = b.wat_audio_id
									AND c.wau_audio_id = a.wpb_audio_id
									AND b.wat_topic = '".$_SESSION['topic']."'");  
	
	 return fncgc_process_search_result($wpdb, $results, $_SESSION['topic'], 'frmTopicSrchRslt');
									
    
	  }

add_shortcode('cgc_display_topic_srch_result','fncgc_display_topic_srch_result');


function fncgc_display_callwith_srch_result(){
	global $wpdb;
	
	$results = $wpdb->get_results("SELECT a.wpb_audio_id,a.wpb_title,a.wpb_location_secs,
										   a.wpb_location,a.wpb_call_with,
										   b.wau_audio_date,b.wau_audio_file,
										   CONCAT('Coaching Call',' - ', 
								   DATE_FORMAT(b.wau_audio_date, '%M %D, %Y')) audio_title,
								    a.wpb_public_bookmarks_id,
									(SELECT x.wpb_location_secs 
                                      FROM wpcr_public_bookmarks x
                                       WHERE x.wpb_location_secs > a.wpb_location_secs
                                        and x.wpb_audio_id = a.wpb_audio_id
                                        order by x.wpb_location_secs 
                                        limit 1)  next_bm
									FROM wpcr_public_bookmarks a,wpcr_audio b
									WHERE b.wau_audio_id = a.wpb_audio_id
									AND a.wpb_call_with = '".$_SESSION['call_with']."'"); 

    return fncgc_process_search_result($wpdb, $results, $_SESSION['call_with'], 'frmCallwithSrchRslt');									
	
    
	  }

add_shortcode('cgc_display_callwith_srch_result','fncgc_display_callwith_srch_result');


function fncgc_display_lib_SrchBox_bm(){
	$content = '<br>
					<div class="row">
						<div class="col-8">
							<div class="input-group mb-3">
								<input type="text" class="form-control input-sm" placeholder="Search My Library" id="libSrch_bm" name="libSrch_bm">
								<div class="input-group-append">
									<button type="submit" onclick="srchLib(\'frmMyLib\');return false;"><i class="fa fa-search fa-sm"></i></button>
								</div>
							</div>
						</div>
						<div class="col">&nbsp;</div>
				</div>';
	return $content;
}

add_shortcode('cgc_display_lib_SrchBox_bm','fncgc_display_lib_SrchBox_bm');

function fncgc_display_lib_SrchBox_hglt(){
	$content = '<br>
					<div class="row">
						<div class="col-8">
							<div class="input-group mb-3">
								<input type="text" class="form-control input-sm" placeholder="Search My Library" id="libSrch_hglt" name="libSrch_hglt">
								<div class="input-group-append">
									<button type="submit" onclick="srchLib(\'frmMyLib\');return false;"><i class="fa fa-search fa-sm"></i></button>
								</div>
							</div>
						</div>
						<div class="col">&nbsp;</div>
				</div>';
	return $content;
}

add_shortcode('cgc_display_lib_SrchBox_hglt','fncgc_display_lib_SrchBox_hglt');

function fncgc_display_lib_SrchBox_notes(){
	$content = '<br>
					<div class="row">
						<div class="col-8">
							<div class="input-group mb-3">
								<input type="text" class="form-control input-sm" placeholder="Search My Library" id="libSrch_notes" name="libSrch_notes">
								<div class="input-group-append">
									<button type="submit" onclick="srchLib(\'frmMyLib\');return false;"><i class="fa fa-search fa-sm"></i></button>
								</div>
							</div>
						</div>
						<div class="col">&nbsp;</div>
				</div>';
	return $content;
}

add_shortcode('cgc_display_lib_SrchBox_notes','fncgc_display_lib_SrchBox_notes');


function fncgc_display_warning_note(){
	
	if(get_current_user_id() == 0){
		return '<div class="alert-warning">
				<strong>Warning!</strong> On Page Refresh, data will be lost.So, Please <a href="/login/">Sign Up / Login</a>.</div>';
	}else{
		return '';
	}
}

add_shortcode('cgc_display_warning_note','fncgc_display_warning_note');


function fncgc_display_search_result(){
	global $wpdb;
	
	
	$results = $wpdb->get_results("SELECT DISTINCT a.wpb_audio_id,a.wpb_title,a.wpb_location_secs,
										   a.wpb_location,a.wpb_call_with,
										   c.wau_audio_date,c.wau_audio_file,
										   CONCAT('Coaching Call',' - ', 
								   DATE_FORMAT(c.wau_audio_date, '%M %D, %Y')) audio_title,
								    a.wpb_public_bookmarks_id,
									(SELECT x.wpb_location_secs 
                                      FROM wpcr_public_bookmarks x
                                       WHERE x.wpb_location_secs > a.wpb_location_secs
                                        and x.wpb_audio_id = a.wpb_audio_id
                                        order by x.wpb_location_secs 
                                        limit 1)  next_bm
									FROM wpcr_public_bookmarks a,wpcr_audio_topics b,wpcr_audio c
									WHERE a.wpb_public_bookmarks_id = b.wat_public_bookmarks_id
									AND a.wpb_audio_id = b.wat_audio_id
									AND c.wau_audio_id = a.wpb_audio_id
                                    AND (UPPER(a.wpb_title) like UPPER('%".$_SESSION['srch_all']."%')
									OR UPPER(a.wpb_call_with) like UPPER('%".$_SESSION['srch_all']."%')
                                    OR EXISTS( SELECT 1 FROM wpcr_audio_topics 
                                                   WHERE wat_audio_id = a.wpb_audio_id
                                                   AND UPPER(wat_topic) like UPPER('%".$_SESSION['srch_all']."%')
                                                   AND wat_public_bookmarks_id = a.wpb_public_bookmarks_id))");  
									
	
	
	  return fncgc_process_search_result($wpdb, $results, $_SESSION['srch_all'], 'frmSrchRslt');
}

add_shortcode('cgc_display_search_result','fncgc_display_search_result');

function fncgc_process_search_result($wpdb, $results, $srchCriteria, $frmId){
	
	$content = '<input type="hidden" name="sess_audio_id" id="sess_audio_id">
				<input type="hidden" name="sess_audio_title" id="sess_audio_title">
				<input type="hidden" name="sess_audio_file" id="sess_audio_file">
				<input type="hidden" name="sess_topic" id="sess_topic">
				<input type="hidden" name="sess_call_with" id="sess_call_with">
					<div class="container-fluid">
		              <div class="row">
		               <div  class="table-responsive-sm">          
						<table id="tbl_audio" class="table">
						<thead>
						 <th>
						   <h3>Coaching Calls with '.$srchCriteria.'</h3>
						 </th>
						</thead>
						<tbody>';
	$rwCnt = 0;
	if(!empty($results))                       
	{  
       foreach($results as $row){
          $rwCnt++;
          $content .= '<tr><td>';
		  $audio_id = 'wau_audio_id_'.$rwCnt ;
		  $audio_title = 'audio_title_'.$rwCnt ;
		  $audio_file = 'wau_audio_file_'.$rwCnt ;
		  $playIcon = 'play_icon_'.$rwCnt ;
		  $audio = 'audio_'.$rwCnt ;
		  $bm_title = 'bm_title_'.$rwCnt ;
		  
		  $content .= '<a tabindex="0" name="'.$bm_title.'" id="'.$bm_title.'" onclick="splayPubBm('.$rwCnt.','.$row->wpb_location_secs.');">'.$row->wpb_title.'</a>';
		  if(!empty($row->next_bm)){
			  $length = $row->next_bm - $row->wpb_location_secs;
			  $content .= '<br><span class="badge badge-light">'.getMinutes($length).'</span><br>';
		   }
		  //$content .= '<a tabindex="0" onclick="splayPubBm('.$rwCnt.','.$row->wpb_location_secs.');"><span id="'.$playIcon.'"><i class="fa fa-play fa-lg"></i></span></a>';
		  //$content .= '<audio preload="auto" id="'.$audio.'"><source src="'.$row->wau_audio_file.'" type="audio/mp3"></audio>';
		  $content .= '<br>';
          $content .= fncgc_get_audio_player($row->wau_audio_file,$rwCnt,$row->wpb_location_secs,$row->next_bm,'srch_');
		  
		  if(get_current_user_id() > 0){
			$content .= '<a tabindex="0" onclick="addToMyLib('.$row->wpb_audio_id.',\''.$row->wpb_title.'\','.$row->wpb_location_secs.',\''.$row->wpb_location.'\');"><i class="fas fa-plus"></i> Add to My Library</a> ';
			$content .= '<br>';
		  }
		  $content .= '<input type="hidden" name="'.$audio_id.'" id="'.$audio_id.'" value="'.$row->wpb_audio_id.'">';
		  $content .= '<input type="hidden" name="'.$audio_title.'" id="'.$audio_title.'" value="'.$row->audio_title.'">';
	      $content .= '<input type="hidden" name="'.$audio_file.'" id="'.$audio_file.'" value="'.$row->wau_audio_file.'">';
		  
	  
	  $topics = $wpdb->get_results("SELECT DISTINCT a.wat_topic
									  FROM wpcr_audio_topics a
									   WHERE a.wat_audio_id = ".$row->wpb_audio_id.
									  " AND a.wat_public_bookmarks_id = ".$row->wpb_public_bookmarks_id.
									  " ORDER BY a.wat_topic");  
	  
	  if(!empty($row->wpb_call_with)){
		    $content .= '<br>';
		    $content .= '<span>Call With : </span>';
		    $content .= '<a href="#" onclick="openCallwithSrchRslt(\''.$frmId.'\',\''.$row->wpb_call_with.'\')" class="btn btn-link btn-sm" role="button">'.$row->wpb_call_with.'</a>';
			$content .= '&nbsp;';
	  }
	  
	  if(!empty($topics))                       
	  {  
		 $content .= '<br>';
		 $content .= '<span>Topics : </span>';
		  foreach($topics as $topic){
			  
			  $content .= '<a href="#" onclick="openTopicSrchRslt(\''.$frmId.'\',\''.$topic->wat_topic.'\')" class="btn btn-info btn-sm" role="button">'.$topic->wat_topic.'</a>';
			  $content .= '&nbsp;'; 
		  }
	  }
	  $content .= '<br><br><a href="#" onclick="openAudio(\''.$frmId.'\','.$rwCnt.');" style="font-size: 150%">From '.$row->audio_title.'</a>';
	  $content .= '</td></tr>';
	 }
	 
      $content .= '</tbody></table>';	
	 }else{
		 $content .= '<tr><td>';
		 $content .= '<span class="text-danger">No results found.</span>';
		 $content .= '</tr></td>';
		 $content .= '</tbody></table>';	
	 }
	  $content .= '<div class="alert alert-success alert-dismissable" id="alert_add_to_mylib">
					 <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						<span id="msg_add_to_mylib">Bookmark added to your library successfully!</span>
					</div></div></div>';
	  return $content;
}


function fncgc_get_audio_player($audio_file,$rwCnt,$audio_strt_tym,$audio_end_tym,$prefix){
	
	$audio = $prefix.'audio_'.$rwCnt ;
	$seekObj = $prefix.'seekObj_'.$rwCnt;
	$isSeeking = $prefix.'isSeeking_'.$rwCnt;
	$strt_tym = $prefix.'start-time_'.$rwCnt;
	$end_tym = $prefix.'end-time_'.$rwCnt;
	$playRateBtn = $prefix.'playRateBtn_'.$rwCnt;
	$play = $prefix.'play_'.$rwCnt;
	$rewind = $prefix.'rewind_'.$rwCnt;
	$forward = $prefix.'forward_'.$rwCnt;
	
	$content = '<div style="width:75%">
				<div id="seekObjContainer" class="slidecontainer">
					<input id="'.$seekObj.'" type="range" name="rng" min="0" step="0.25"
					value="0" onchange="seekAudio(\''.$prefix.'\','.$rwCnt.')" oninput="seekAudio(\''.$prefix.'\','.$rwCnt.')" class="slider">
					<input type="hidden" id="'.$isSeeking.'" name="'.$isSeeking.'" value="N">
				</div>
				<small style="float: left; position: relative; left: 5px;" id="'.$strt_tym.'" class="start-time"></small>
				<small style="float: right; position: relative; right: 5px;" id="'.$end_tym.'" data-toggle="tooltip" 
				data-placement="left" title="Click to show Remining Time" class="end-time" 
				onclick="showhideRemaining(\''.$prefix.'\','.$rwCnt.')"></small>
				<br>
				<div id="player-container">
					<audio  id="'.$audio.'" preload="auto" ondurationchange="setupSeek(\''.$prefix.'\','.$rwCnt.')" 
					ontimeupdate="initProgressBar(\''.$prefix.'\','.$rwCnt.')">
							  <source src="'.$audio_file.'" type="audio/mp3">
							</audio>
					</div>
					   <p>
						<div id="btn-controls" style="text-align:center;">
						   <input type="image" id="'.$rewind.'" src="https://img.icons8.com/ios/50/000000/skip-15-seconds-back.png" 
						   alt="Rewind 15" width="38" height="38" data-toggle="tooltip" data-placement="bottom" 
						   title="Rewind" onclick="rewindAudio(\''.$prefix.'\','.$rwCnt.');return false;">&nbsp;&nbsp;&nbsp;
						   <input type="image" id="'.$play.'" src="https://img.icons8.com/material-outlined/50/000000/circled-play.png" 
						   alt="Play" width="48" height="48" data-toggle="tooltip" data-placement="bottom" 
						   title="Play/Pause" onclick="playAudio(\''.$prefix.'\','.$rwCnt.','.$audio_strt_tym.','.$audio_end_tym.');return false;">&nbsp;&nbsp;&nbsp;
						   <input type="image" id="'.$forward.'" src="https://img.icons8.com/ios/80/000000/skip-ahead-15-seconds.png" 
						   alt="Forward 15" width="38" height="38" data-toggle="tooltip" data-placement="bottom" 
						   title="Forward" onclick="forwardAudio(\''.$prefix.'\','.$rwCnt.');return false;">
						  <div class="dropdown">
					<button type="button" class="btn btn-circle" data-toggle="dropdown" id="'.$playRateBtn.'">
					  1.0x
					</button>
					<div class="dropdown-menu">
					<h6>Play speed</h6>
					<div class="dropdown-divider"></div>
					  <a class="dropdown-item" tabindex="0" onclick="changePlayrate(this,\''.$prefix.'\','.$rwCnt.');return false;">0.5x</a>
					  <a class="dropdown-item" tabindex="0" onclick="changePlayrate(this,\''.$prefix.'\','.$rwCnt.');return false;">0.75x</a>
					  <a class="dropdown-item" tabindex="0" onclick="changePlayrate(this,\''.$prefix.'\','.$rwCnt.');return false;">1.0x</a>
					  <a class="dropdown-item" tabindex="0" onclick="changePlayrate(this,\''.$prefix.'\','.$rwCnt.');return false;">1.25x</a>
					   <a class="dropdown-item" tabindex="0" onclick="changePlayrate(this,\''.$prefix.'\','.$rwCnt.');return false;">1.5x</a>
						<a class="dropdown-item" tabindex="0" onclick="changePlayrate(this,\''.$prefix.'\','.$rwCnt.');return false;">2.0x</a>
					</div>
				  </div>
						</div>
						
						</p></div>';
						
						return $content;
	
}


function fncgc_insert_user_bookmark(){
	
   global $wpdb;
   
   if(get_current_user_id() > 0){
	   
	   $table = "wpcr_user_bookmarks";
	   
	   if(array_key_exists('audio_id',$_POST)){
		   
		   $isAvailable = $wpdb->get_var("SELECT 1
										  FROM wpcr_user_bookmarks a
										   WHERE a.wub_user_id = ".get_current_user_id()."
										   AND a.wub_audio_id = ".$_POST['audio_id']."
										   AND a.wub_location_secs = ".$_POST['bm_locsecs']."
										   AND a.wub_location = '".$_POST['bm_loc']."'
										   AND a.wub_title = '".$_POST['bm_title']."'");  
		  					  
		   if(is_null($isAvailable)){
			   
			   $data = array('wub_user_id' => get_current_user_id(),
							 'wub_audio_id' => $_POST['audio_id'], 
							 'wub_location' => $_POST['bm_loc'],
							 'wub_location_secs' => $_POST['bm_locsecs'],
							 'wub_title' => $_POST['bm_title']);
							 
				
				$wpdb->insert($table,$data);
				echo $wpdb->insert_id;
		   }
		   echo 0;
	   }else{
		   $data = array('wub_user_id' => get_current_user_id(),
						 'wub_audio_id' => $_SESSION['audio_id'], 
						 'wub_location' => $_POST['locHms'],
						 'wub_location_secs' => $_POST['locSecs'],
						 'wub_title' => $_POST['bmTitle']);
						 
		   $wpdb->insert($table,$data);
		   echo $wpdb->insert_id;
	   }
   }else{
	   echo '';
   }
   wp_die(); 
}


add_action( 'wp_ajax_cgc_insert_user_bookmark', 'fncgc_insert_user_bookmark' );
//add_action( 'wp_ajax_nopriv_cgc_insert_user_bookmark', 'fncgc_insert_user_bookmark' );

function fncgc_insert_user_highlight(){
	
   global $wpdb;
   
   if(get_current_user_id() > 0){
	   $table = "wpcr_user_highlights";
	   $data = array('wuh_user_id' => get_current_user_id(),
					 'wuh_audio_id' => $_SESSION['audio_id'], 
					 'wuh_start_time_secs' => $_POST['sTym'],
					 'wuh_end_time_secs' => $_POST['eTym'],
					 'wuh_location' => $_POST['locHms'],
					 'wuh_length' => $_POST['length'],
					 'wuh_title' => $_POST['hgltTitle']);
	   $wpdb->insert($table,$data);
	   echo $wpdb->insert_id;
   }else{
	   echo '';
   }
   wp_die(); 
}


add_action( 'wp_ajax_cgc_insert_user_highlight', 'fncgc_insert_user_highlight' );
//add_action( 'wp_ajax_nopriv_cgc_insert_user_highlight', 'fncgc_insert_user_highlight' );

function fncgc_delete_user_bm_or_hglt_or_nt(){
	
   global $wpdb;
   
   if(get_current_user_id() > 0){
	 if($_POST['ptype'] == 'bm'){
	   $table = 'wpcr_user_bookmarks';
	   $wpdb->delete( $table, array('wub_user_bookmarks_id' => $_POST['user_db_id']));
	   
	   $table = 'wpcr_audio_topics';
	   $wpdb->delete( $table, array('wat_user_bookmarks_id' => $_POST['user_db_id']));
	 }else if($_POST['ptype'] == 'hglt'){
	   $table = 'wpcr_user_highlights';
	   $wpdb->delete( $table, array('wuh_user_highlights_id' => $_POST['user_db_id']));
	   
	   $table = 'wpcr_audio_topics';
	   $wpdb->delete( $table, array('wat_user_highlights_id' => $_POST['user_db_id']));
	 }else{
		$table = 'wpcr_user_notes';
	    $wpdb->delete( $table, array('wun_user_notes_id' => $_POST['user_db_id']));
	 }		 
   }
   echo 'success' ;
   wp_die(); 
}


add_action( 'wp_ajax_cgc_delete_user_bm_or_hglt_or_nt', 'fncgc_delete_user_bm_or_hglt_or_nt' );


function fncgc_update_user_title(){
	
   global $wpdb;
   
   if(get_current_user_id() > 0){
	   if($_POST['ptype'] == 'bm'){
		   $table = "wpcr_user_bookmarks";
		   $data = array('wub_title' => $_POST['title']);
		   $where = array('wub_user_bookmarks_id' => $_POST['user_db_id']);
		   $wpdb->update($table,$data,$where);
	   }else{
		   $table = "wpcr_user_highlights";
		   $data = array('wuh_title' => $_POST['title']);
		   $where = array('wuh_user_highlights_id' => $_POST['user_db_id']);
		   $wpdb->update($table,$data,$where);
	   }
   }
   
   echo 'success' ;
   wp_die(); 
}

add_action( 'wp_ajax_cgc_update_user_title', 'fncgc_update_user_title' );


function fncgc_update_user_topics(){
	
   global $wpdb;
   
   if($_POST['ptype'] == 'bm'){
	   $db_topics = $wpdb->get_results("SELECT wat_audio_topics_id,wat_topic
											FROM wpcr_audio_topics
											WHERE wat_audio_id = ".$_POST['audio_id'].
											" AND wat_user_bookmarks_id = ".$_POST['user_db_id']); 
													
	   
   }else{
	   $db_topics = $wpdb->get_results("SELECT wat_audio_topics_id,wat_topic
											FROM wpcr_audio_topics
											WHERE wat_audio_id = ".$_POST['audio_id'].
											" AND wat_user_highlights_id = ".$_POST['user_db_id']); 
   }
   
   $topics = $_POST['tags'];
   $audio_topic_id = [];
   $audio_topic = [];
   if(!empty($db_topics)){
	  $audio_topic_id = array_column($db_topics, 'wat_audio_topics_id');
	  $audio_topic = array_column($db_topics, 'wat_topic');
	}
   
   if($topics){
					   
	 $user_topic = explode(',', $topics);;
	
	  foreach( $user_topic as $topic ){		
			if(!(in_array($topic, $audio_topic))){
																	
			  $table = "wpcr_audio_topics";
			  if($_POST['ptype'] == 'bm'){
				  $data = array('wat_audio_id' => $_POST['audio_id'], 
								'wat_topic' => $topic,
								'wat_user_bookmarks_id' => $_POST['user_db_id']);
			  }else{
				  $data = array('wat_audio_id' => $_POST['audio_id'], 
								'wat_topic' => $topic,
								'wat_user_highlights_id' => $_POST['user_db_id']);
			  }
			  $wpdb->insert($table,$data);
			  if($wpdb->insert_id){
					array_push($audio_topic, $topic);
					array_push($audio_topic_id, $wpdb->insert_id);
			  }
		   }						   
	   }
	}else{
	  //delete topics in db
	  $table = 'wpcr_audio_topics';
	  if($_POST['ptype'] == 'bm'){
		$wpdb->delete( $table, array('wat_user_bookmarks_id' => $_POST['user_db_id']));
	  }else{
		  $wpdb->delete( $table, array('wat_user_highlights_id' => $_POST['user_db_id']));
	  }
    }
	  
	  if(!empty($db_topics)){
		  for ($x = 0; $x < count($audio_topic_id); $x++) {
			  
			  if(!(in_array($audio_topic[$x], $user_topic))){
				  //delete topics in db
				  $table = 'wpcr_audio_topics';
				  $wpdb->delete( $table, array('wat_audio_topics_id' => $audio_topic_id[$x]));
			  }
		  }
	  }
     echo 'success' ;
     wp_die(); 
}


add_action('wp_ajax_cgc_update_user_topics','fncgc_update_user_topics');


function fncgc_update_user_topic(){
	
   global $wpdb;
   
   if($_POST['ptype'] == 'bm'){
	   $audio_topic_id = $wpdb->get_var("SELECT wat_audio_topics_id
										FROM wpcr_audio_topics
										WHERE wat_audio_id = ".$_POST['audio_id'].
										" AND wat_user_bookmarks_id = ".$_POST['user_db_id'].
										" AND wat_topic = '".$_POST['topic']."'"); 
													
	   
   }else{
	   $audio_topic_id = $wpdb->get_var("SELECT wat_audio_topics_id
										FROM wpcr_audio_topics
										WHERE wat_audio_id = ".$_POST['audio_id'].
										" AND wat_user_highlights_id = ".$_POST['user_db_id'].
										" AND wat_topic = '".$_POST['topic']."'"); 
   }
   
   if($_POST['isTopicAdded'] == 'Y'){
   
	   if(is_null($audio_topic_id)){
					  
		  $table = "wpcr_audio_topics";
		  if($_POST['ptype'] == 'bm'){
			  $data = array('wat_audio_id' => $_POST['audio_id'], 
							'wat_topic' => $_POST['topic'],
							'wat_user_bookmarks_id' => $_POST['user_db_id']);
		  }else{
			  $data = array('wat_audio_id' => $_POST['audio_id'], 
							'wat_topic' => $_POST['topic'],
							'wat_user_highlights_id' => $_POST['user_db_id']);
		  }
		  $wpdb->insert($table,$data);
	   }
   
	}else{
	   if(!empty($audio_topic_id)){
		   $table = 'wpcr_audio_topics';
		   $wpdb->delete( $table, array('wat_audio_topics_id' => $audio_topic_id));
	   }
    }
	    
     echo 'success' ;
     wp_die(); 
}
add_action('wp_ajax_cgc_update_user_topic','fncgc_update_user_topic');


function fncgc_update_user_notes(){
	
   global $wpdb;
   $user_id = get_current_user_id();
   
   if($user_id > 0){
	   $notes = $wpdb->get_results("SELECT wun_notes,wun_user_notes_id
									FROM wpcr_user_notes 
									WHERE wun_audio_id = ".$_POST['audio_id'].
									" AND wun_user_id = ".$user_id);
									
	   if(!empty($notes)){
		  foreach($notes as $note){
			if($_POST['my_notes']){
			   $table = "wpcr_user_notes";
			   $data = array('wun_notes' => $_POST['my_notes']);
			   $where = array('wun_user_notes_id' => $note->wun_user_notes_id);
			   $wpdb->update($table,$data,$where);
			}else{
				$table = 'wpcr_user_notes';
				$wpdb->delete( $table, array('wun_user_notes_id' => $note->wun_user_notes_id));
			}
		  }
	   }else{
		   $table = "wpcr_user_notes";
		   $data = array('wun_user_id' => $user_id,
						 'wun_audio_id' => $_POST['audio_id'], 
						 'wun_notes' => $_POST['my_notes']);
		   $wpdb->insert($table,$data);
	   }
								
	   
	   echo 'Success';
   }else{
	   echo '';
   }
   wp_die(); 
}


add_action( 'wp_ajax_cgc_update_user_notes', 'fncgc_update_user_notes' );
//add_action( 'wp_ajax_nopriv_cgc_update_user_notes', 'fncgc_update_user_notes' );


function fncgc_show_srch_header($lib_srch){
	
	$content = '<div id="srch_mylib" class="row">
					<div class="col-8">
						<span id="srch_msg">Search Results for <strong>\''.$lib_srch.'\'</strong></span>
						<button type="button" class="btn btn-link" id="btn_srchMylib" 
						onclick="clrSrch(\'frmMyLib\');return false;">Clear Search</button>
					</div>
					<div class="col">&nbsp;</div>
				</div>';
	return $content;
}

function fncgc_display_all_my_bm(){
	global $wpdb;
	$user_id = get_current_user_id();
	$content = '';
	if(!isset($_SESSION['lib_srch'])) $_SESSION['lib_srch'] = '';
	
	if($user_id > 0){
		$results = $wpdb->get_results("SELECT a.wub_audio_id,a.wub_title,a.wub_location_secs,
										   a.wub_location,b.wau_audio_date,b.wau_audio_file,
										   CONCAT('Coaching Call',' - ', 
											DATE_FORMAT(b.wau_audio_date, '%M %D, %Y')) audio_title,
											a.wub_user_bookmarks_id
										FROM wpcr_user_bookmarks a,wpcr_audio b
										WHERE b.wau_audio_id = a.wub_audio_id
										AND a.wub_user_id = ".$user_id."
										AND (UPPER(a.wub_title) LIKE UPPER('%".$_SESSION['lib_srch']."%')
										OR EXISTS (SELECT 1 FROM wpcr_audio_topics 
												  WHERE wat_audio_id = a.wub_audio_id
                                                  AND wat_user_bookmarks_id = a.wub_user_bookmarks_id
                                                  AND UPPER(wat_topic) LIKE UPPER('%".$_SESSION['lib_srch']."%'))) 
										ORDER BY a.wub_date_time DESC");
		if(!empty($_SESSION['lib_srch'])){
			
			$content .= fncgc_show_srch_header($_SESSION['lib_srch']);
		}
		$content .= '<h3>My Bookmarks</h3>
					  <div class="table-responsive">
						<table  class="table" id="tbl_bm" >';
		 if(!empty($results))                       
		{  
		  $rwCnt = 1;
		  foreach($results as $row){ 
			
			$bm = 'bm_title_'.$rwCnt ;
			$bmVal = $row->wub_title;
			$bmId = 'user_bm_id_'.$rwCnt;
			$loc = 'bm_loc_'.$rwCnt;
			$tym = 'bm_time_'.$rwCnt;
			$tag = 'bm_tag_'.$rwCnt;
			$rw = 'bm_row_'.$rwCnt;
			$locSecs = $row->wub_location_secs;
			$locHms = $row->wub_location;
			$bmIdVal = $row->wub_user_bookmarks_id;
			
		    $audio_id = 'wau_audio_id_'.$rwCnt ;
		    $audio_title = 'audio_title_'.$rwCnt ;
		    $audio_file = 'wau_audio_file_'.$rwCnt ;
		    $playIcon = 'play_icon_'.$rwCnt ;
		    $audio = 'audio_'.$rwCnt ;
			$frmId = 'frmMyLib';
			
			$content .= '<tr><td>';
			$content .= '<a href="#" onclick="splayPubBm('.$rwCnt.','.$row->wub_location_secs.');"><span id="'.$bm.'" name="'.$bm.'">'.$bmVal.'</span></a>
						 <span> / </span><span id="'.$loc.'" name="'.$loc.'">'.$locHms.'</span>
			             <input type="hidden" id="'.$rw.'" name="'.$rw.'" value="'.$rwCnt.'">
						 <input type="hidden" id="'.$bmId.'" name="'.$bmId.'" value="'.$bmIdVal.'">
						 <input type="hidden" id="'.$tym.'" name="'.$tym.'" value="'.$locSecs.'">';
				
			  $content .= '<br><br>';
              $content .= fncgc_get_audio_player($row->wau_audio_file,$rwCnt,$row->wub_location_secs,0,'bm_');
		  
			  $content .=  '<input type="hidden" name="'.$audio_id.'" id="'.$audio_id.'" value="'.$row->wub_audio_id.'">';
			  $content .=  '<input type="hidden" name="'.$audio_title.'" id="'.$audio_title.'" value="'.$row->audio_title.'">';
			  $content .=  '<input type="hidden" name="'.$audio_file.'" id="'.$audio_file.'" value="'.$row->wau_audio_file.'">';
						 
			$topics = $wpdb->get_results("SELECT DISTINCT a.wat_topic
										  FROM wpcr_audio_topics a
										   WHERE a.wat_audio_id = ".$row->wub_audio_id.
										   " AND a.wat_user_bookmarks_id = ".$bmIdVal.
										   " ORDER BY a.wat_topic");  
			
			$content .= '<br><div id="'.$tag.'">';
			if(!empty($topics))                       
			  {  
				  foreach($topics as $topic){
					  
					  $content .= '<span class="badge badge-info">'.$topic->wat_topic.'</span>';
					  $content .= '&nbsp;'; 
				  }
			  }
			 $content .= '</div>';
			 
	
		    $content .= '<br><a href="#" onclick="openAudio(\''.$frmId.'\','.$rwCnt.');" style="font-size: 110%">From '.$row->audio_title.'</a>';
			$content .= '</td><td>';
			$content .= '<div class="text-right">
							<button type="button" class="btn btn-link  dropdown-toggle-split" data-toggle="dropdown">
							<i class="fa fa-ellipsis-v"></i></button>
							<div class="dropdown-menu">
							<a class="dropdown-item" data-toggle="modal" id="btn_bm_editTitle"  data-target="#editTitle" onclick="fnEditTitle(this,'.$rwCnt.')">Edit Title</a>
							<a class="dropdown-item" data-toggle="modal" data-target="#updateTag" id="btn_bm_updateTag" onclick="fnUpdateTag(this,'.$rwCnt.')">Update Tag</a>
							<a class="dropdown-item" id="btn_bm_delItem" onclick="delItem(this,'.$rwCnt.')">Delete Bookmark</a></div></div>';
			$content .= '</td></tr>';
			$rwCnt++;
		  }
		}
		$content .= '</table>';
		return $content;
	}
									
    
}

add_shortcode('cgc_display_all_my_bm','fncgc_display_all_my_bm');

function fncgc_display_all_my_hglt(){
	global $wpdb;
	$user_id = get_current_user_id();
	$content = '';
	if(!isset($_SESSION['lib_srch'])) $_SESSION['lib_srch'] = '';
	
	if($user_id > 0){
		$results = $wpdb->get_results("SELECT a.wuh_audio_id,a.wuh_user_highlights_id,
												a.wuh_start_time_secs,a.wuh_end_time_secs,
											   a.wuh_location,a.wuh_length,a.wuh_title,
										   b.wau_audio_date,b.wau_audio_file,
										   CONCAT('Coaching Call',' - ', 
								   DATE_FORMAT(b.wau_audio_date, '%M %D, %Y')) audio_title
									FROM wpcr_user_highlights a,wpcr_audio b
									WHERE b.wau_audio_id = a.wuh_audio_id
                                    AND a.wuh_user_id = ".$user_id."
									AND (UPPER(a.wuh_title) LIKE UPPER('%".$_SESSION['lib_srch']."%')
									OR EXISTS (SELECT 1 FROM wpcr_audio_topics 
											  WHERE wat_audio_id = a.wuh_audio_id
											  AND wat_user_highlights_id = a.wuh_user_highlights_id
											  AND UPPER(wat_topic) LIKE UPPER('%".$_SESSION['lib_srch']."%'))) 
                                    ORDER BY a.wuh_date_time DESC"); 
		if(!empty($_SESSION['lib_srch'])){
			
			$content .= fncgc_show_srch_header($_SESSION['lib_srch']);
		}
		$content .= '<h3>My Highlights</h3>
					  <div class="table-responsive">
					 <table  class="table" id="tbl_hglt" >';
		if(!empty($results))                       
		{  
		  $rwCnt = 1;
		  foreach($results as $row){ 
			
			$hglt = 'hglt_title_'.$rwCnt ;
			$hgltId = 'user_hglt_id_'.$rwCnt;
			$hgltVal = $row->wuh_title;
			$tag = 'hglt_tag_'.$rwCnt ;
			$loc = 'hglt_loc_'.$rwCnt;
			$sTym = 'hglt_stime_'.$rwCnt;
			$eTym = 'hglt_etime_'.$rwCnt;
			$aLen = 'hglt_len_'.$rwCnt;
			$rw = 'hglt_row_'.$rwCnt;
			$lenVal = $row->wuh_length; 
			$sTymVal = $row->wuh_start_time_secs;
			$eTymVal = $row->wuh_end_time_secs;
			$locHms = $row->wuh_location;
			$hgltIdVal = $row->wuh_user_highlights_id;
			
			$audio_id = 'hglt_wau_audio_id_'.$rwCnt ;
		    $audio_title = 'hglt_audio_title_'.$rwCnt ;
		    $audio_file = 'hglt_wau_audio_file_'.$rwCnt ;
		    $playIcon = 'hglt_play_icon_'.$rwCnt ;
		    $audio = 'hglt_audio_'.$rwCnt ;
			$frmId = 'frmMyLib';
			
			
			$content .= '<tr><td>';
			$content .= '<a href="#" onclick="playMyLibHglt('.$rwCnt.');"><span id="'.$hglt.'" name="'.$hglt.'">'.$hgltVal.'</span></a>
						 <span> / </span><span id="'.$loc.'" name="'.$loc.'">'.$locHms.'</span><span> : </span>
						 <span id="'.$aLen.'" name="'.$aLen.'">'.$lenVal.'</span>
						 <input type="hidden" id="'.$rw.'" name="'.$rw.'" value="'.$rwCnt.'">
						 <input type="hidden" id="'.$hgltId.'" name="'.$hgltId.'" value="'.$hgltIdVal.'">
						 <input type="hidden" id="'.$sTym.'" name="'.$sTym.'" value="'.$sTymVal.'">
						 <input type="hidden" id="'.$eTym.'" name="'.$eTym.'" value="'.$eTymVal.'"><br>';
			$content .= '<br><br>';
            $content .= fncgc_get_audio_player($row->wau_audio_file,$rwCnt,$row->wuh_start_time_secs,$row->wuh_end_time_secs,'hglt_');
			$content .=  '<input type="hidden" name="'.$audio_id.'" id="'.$audio_id.'" value="'.$row->wuh_audio_id.'">';
			$content .=  '<input type="hidden" name="'.$audio_title.'" id="'.$audio_title.'" value="'.$row->audio_title.'">';
			$content .=  '<input type="hidden" name="'.$audio_file.'" id="'.$audio_file.'" value="'.$row->wau_audio_file.'">';
						 
			$topics = $wpdb->get_results("SELECT DISTINCT a.wat_topic
										  FROM wpcr_audio_topics a
										   WHERE a.wat_audio_id = ".$row->wuh_audio_id.
										   " AND a.wat_user_highlights_id = ".$hgltIdVal.
										   " ORDER BY a.wat_topic");  
			
			$content .= '<br><div id="'.$tag.'">';
			if(!empty($topics))                       
			  {  
				  foreach($topics as $topic){
					  
					  $content .= '<span class="badge badge-info">'.$topic->wat_topic.'</span>';
					  $content .= '&nbsp;'; 
				  }
			  }
			$content .= '</div>';
			$content .= '<br><a href="#" onclick="openAudio(\''.$frmId.'\','.$rwCnt.');" style="font-size: 110%">From '.$row->audio_title.'</a>';
			$content .= '</td><td>';
			$content .= '<div class="text-right">
							<button type="button" class="btn btn-link  dropdown-toggle-split" data-toggle="dropdown">
							<i class="fa fa-ellipsis-v"></i></button><div class="dropdown-menu">
							<a class="dropdown-item" data-toggle="modal" id="btn_hglt_editTitle"  data-target="#editTitle" onclick="fnEditTitle(this,'.$rwCnt.')">Edit Title</a>
							<a class="dropdown-item" data-toggle="modal" data-target="#updateTag" id="btn_hglt_updateTag" onclick="fnUpdateTag(this,'.$rwCnt.')">Update Tag</a>
							<a class="dropdown-item" id="btn_hglt_delItem" onclick="delItem(this,'.$rwCnt.')">Delete Highlight</a></div></div>';
			$content .= '</td></tr>';
			$rwCnt++;
		  }
		}
		$content .= '</table>';
		return $content;
	}
}

add_shortcode('cgc_display_all_my_hglt','fncgc_display_all_my_hglt');



function fncgc_display_all_my_notes(){
	global $wpdb;
	$user_id = get_current_user_id();
	$content = '';
	if(!isset($_SESSION['lib_srch'])) $_SESSION['lib_srch'] = '';
	
	if($user_id > 0){
		$results = $wpdb->get_results("SELECT a.wun_audio_id,a.wun_user_notes_id,b.wau_audio_date,b.wau_audio_file,
										   CONCAT('Coaching Call',' - ', 
										   DATE_FORMAT(b.wau_audio_date, '%M %D, %Y')) audio_title,a.wun_notes
											FROM wpcr_user_notes a,wpcr_audio b
											WHERE b.wau_audio_id = a.wun_audio_id
											AND a.wun_user_id = ".$user_id."
											AND UPPER(a.wun_notes) like UPPER('%".$_SESSION['lib_srch']."%')
											ORDER BY a.wun_date_time DESC"); 
		if(!empty($_SESSION['lib_srch'])){
			
			$content .= fncgc_show_srch_header($_SESSION['lib_srch']);
		}
		
		$content .= '<h3>My Notes</h3>
					  <div class="table-responsive">
					 <table  class="table" id="tbl_notes" >';
		if(!empty($results))                       
		{  
		  $rwCnt = 1;
		  foreach($results as $row){ 
			
			$notes = 'notes_'.$rwCnt ;
			$notesVal = $row->wun_notes;
			$notesId = 'user_notes_'.$rwCnt ;
			$notesIdVal = $row->wun_user_notes_id;
			$rw = 'notes_row_'.$rwCnt;
			
			$audio_id = 'nt_wau_audio_id_'.$rwCnt ;
		    $audio_title = 'nt_audio_title_'.$rwCnt ;
		    $audio_file = 'nt_wau_audio_file_'.$rwCnt ;
			$frmId = 'frmMyLib';
			
			
			$content .= '<tr><td>';
			$content .= '<span id="'.$notes.'" name="'.$notes.'">'.$notesVal.'</span>
						 <input type="hidden" id="'.$rw.'" name="'.$rw.'" value="'.$rwCnt.'">
						 <input type="hidden" id="'.$notesId.'" name="'.$notesId.'" value="'.$notesIdVal.'">';
	
			$content .=  '<input type="hidden" name="'.$audio_id.'" id="'.$audio_id.'" value="'.$row->wun_audio_id.'">';
			$content .=  '<input type="hidden" name="'.$audio_title.'" id="'.$audio_title.'" value="'.$row->audio_title.'">';
			$content .=  '<input type="hidden" name="'.$audio_file.'" id="'.$audio_file.'" value="'.$row->wau_audio_file.'">';
						 
			$content .= '<br><a href="#" onclick="openAudio(\''.$frmId.'\','.$rwCnt.');">From '.$row->audio_title.'</a>';
			$content .= '</td><td>';
			$content .= '<div class="text-right">
							<button type="button" class="btn btn-link  dropdown-toggle-split" data-toggle="dropdown">
							<i class="fa fa-ellipsis-v"></i></button>
							<div class="dropdown-menu">
							<a class="dropdown-item" data-toggle="modal" id="btn_editNote"  data-target="#editNote" onclick="fnEditNote('.$rwCnt.')">Edit Note</a>
							<a class="dropdown-item" id="btn_note_delItem" onclick="delItem(this,'.$rwCnt.')">Delete Note</a></div></div>';
			$content .= '</td></tr>';
			$rwCnt++;
		  }
		}
		$content .= '</table>';
		return $content;
	}
}

add_shortcode('cgc_display_all_my_notes','fncgc_display_all_my_notes');

function log_me($message) {
    if ( WP_DEBUG === true ) {
        if ( is_array($message) || is_object($message) ) {
            error_log( print_r($message, true) );
        } else {
            error_log( $message );
        }
    }
}

function deleteAudio($db_audio_id){
	global $wpdb;
	
	$table = 'wpcr_audio';
	$wpdb->delete( $table, array( 'wau_audio_id' => $db_audio_id ) );
	
	$table = 'wpcr_public_bookmarks';
	$wpdb->delete( $table, array( 'wpb_audio_id' => $db_audio_id ) );
	
	$table = 'wpcr_audio_topics';
	$wpdb->delete( $table, array( 'wat_audio_id' => $db_audio_id ) );
}

function fnUpdateCustomTable($post_id) {
	
	global $wpdb;
	$currPost = get_post($post_id);
	
	$db_audio_ids = $wpdb->get_results("SELECT wau_audio_id 
										  FROM wpcr_audio 
											WHERE wau_post_id = ".$post_id);
											
    if(!empty($db_audio_ids))                       
		{  
		  foreach($db_audio_ids as $row){ 
				$db_audio_id = $row->wau_audio_id;
		  }
		}		  

    if ($currPost->post_status == 'draft') {
		//delete 
		if(!empty($db_audio_id)){
			deleteAudio($db_audio_id);
		}
    
	}else if ($currPost->post_status == 'publish') {
		if(empty($db_audio_id)){

			if(!empty(get_field('audio_date', $post_id)) && !empty(get_field('audio_url', $post_id))) {
				
				$table = "wpcr_audio";
				$data = array('wau_audio_date' => date(get_field('audio_date', $post_id)), 
							  'wau_audio_file' => get_field('audio_url', $post_id),
							  'wau_post_id' => $post_id);
				$wpdb->insert($table,$data);
				$audio_id = $wpdb->insert_id;
				
				
				$rwCnt = 0;
				if(have_rows('bookmarks')):
					while ( have_rows('bookmarks') ) : 
					    the_row();
						if(!empty(get_sub_field('title')) && !empty(get_sub_field('location'))):
						   
						   $acf_fld_name = 'bookmarks_'.$rwCnt;
						   $title = get_sub_field('title');
						   $callwith = get_sub_field('cgc_member');
						   $location = date_parse(get_sub_field('location'));
						   $location_secs = $location['hour'] * 3600 + $location['minute'] * 60 + $location['second'];
						   $call_with = (get_sub_field('cgc_member') == 'Select') ? '' : get_sub_field('cgc_member');
						   
						   $table = "wpcr_public_bookmarks";
						   $data = array('wpb_audio_id' => $audio_id, 
										 'wpb_location' => get_sub_field('location'),
										 'wpb_location_secs' => $location_secs,
										 'wpb_title' => get_sub_field('title'),
										 'wpb_call_with' => $call_with,
										  'wpb_acf_field' => $acf_fld_name);
						   $wpdb->insert($table,$data);
						   $public_bm_id = $wpdb->insert_id;
						    
						   if(empty($public_bm_id)) return;
						   $topics = get_sub_field('topics');
						   if( $topics ):
							foreach( $topics as $topic ):
							  $table = "wpcr_audio_topics";
							  $data = array('wat_audio_id' => $audio_id, 
											'wat_topic' => $topic->name,
											'wat_public_bookmarks_id' => $public_bm_id);
							  $wpdb->insert($table,$data);
							endforeach;
						  endif;
						endif;
						$rwCnt++;
					 endwhile;
				   endif;
				}
				
		}else{
			
			if(get_field('audio_date', $post_id) && get_field('audio_url', $post_id)) {
				
				$table = "wpcr_audio";
				$data = array('wau_audio_date' => date(get_field('audio_date', $post_id)), 
							  'wau_audio_file' => get_field('audio_url', $post_id));
				$where = array('wau_post_id' => $post_id);
				$wpdb->update($table,$data,$where);
				
				$rwCnt = 0;
				if(have_rows('bookmarks')){
				 while ( have_rows('bookmarks') ){
				  the_row();
				  $public_bm_id = ''; 
				  $acf_fld_name = 'bookmarks_'.$rwCnt;
				  
				  if(!empty(get_sub_field('title')) && !empty(get_sub_field('location'))){
					  
				  $public_bm_ids = $wpdb->get_results("SELECT wpb_public_bookmarks_id 
														FROM wpcr_public_bookmarks 
														WHERE wpb_acf_field = '".$acf_fld_name.
														"' AND wpb_audio_id =".$db_audio_id ); 
				   if(!empty($public_bm_ids))                       
					{  
					  foreach($public_bm_ids as $row){ 
							$public_bm_id = $row->wpb_public_bookmarks_id;
					  }
					}
														
														
				   $title = get_sub_field('title');
				   $callwith = get_sub_field('cgc_member');
				   $location = date_parse(get_sub_field('location'));
				   $location_secs = $location['hour'] * 3600 + $location['minute'] * 60 + $location['second'];
				   $call_with = (get_sub_field('cgc_member') == 'Select') ? '' : get_sub_field('cgc_member');
				   
				   
				   if(!empty($public_bm_id)){
					   
					   $table = "wpcr_public_bookmarks";
					   $data = array('wpb_location' => get_sub_field('location'),
									 'wpb_location_secs' => $location_secs,
									 'wpb_title' => get_sub_field('title'),
									 'wpb_call_with' => $call_with);
					   $where = array('wpb_public_bookmarks_id' => $public_bm_id);
					   $wpdb->update($table,$data,$where);
				   }else{
					   
					   $table = "wpcr_public_bookmarks";
					   $data = array('wpb_audio_id' => $db_audio_id, 
									 'wpb_location' => get_sub_field('location'),
									 'wpb_location_secs' => $location_secs,
									 'wpb_title' => get_sub_field('title'),
									 'wpb_call_with' => $call_with,
									  'wpb_acf_field' => $acf_fld_name);
					   $wpdb->insert($table,$data);
					   $public_bm_id = $wpdb->insert_id;
				   }
				   
				   if(empty($public_bm_id)) return;
				   $db_topics = $wpdb->get_results("SELECT wat_audio_topics_id,wat_topic
													FROM wpcr_audio_topics
													WHERE wat_audio_id = ".$db_audio_id.
													" AND wat_public_bookmarks_id = ".$public_bm_id);  
													
				   $topics = get_sub_field('topics');
				  
				  $audio_topic_id = [];
				  $audio_topic = [];
				  if(!empty($db_topics)){
					$audio_topic_id = array_column($db_topics, 'wat_audio_topics_id');
					$audio_topic = array_column($db_topics, 'wat_topic');
				  }
				 
				    if($topics){
					   
					   $acf_topic = array_column($topics, 'name');
					   foreach( $topics as $topic ){
						   
						   if(!(in_array($topic->name, $audio_topic))){
							  									
						      $table = "wpcr_audio_topics";
							  $data = array('wat_audio_id' => $db_audio_id, 
											'wat_topic' => $topic->name,
											'wat_public_bookmarks_id' => $public_bm_id);
							  $wpdb->insert($table,$data);
		
							  if($wpdb->insert_id){
									array_push($audio_topic, $topic->name);
									array_push($audio_topic_id, $wpdb->insert_id);
							  }
						   }						   
				        }
				   }else{
					  //delete topics in db
					  $table = 'wpcr_audio_topics';
					  $wpdb->delete( $table, array('wat_public_bookmarks_id' => $public_bm_id));
				  }
				  
				  if(!empty($db_topics)){
					  for ($x = 0; $x < count($audio_topic_id); $x++) {
						  
						  if(!(in_array($audio_topic[$x], $acf_topic))){
							  //delete topics in db
							  $table = 'wpcr_audio_topics';
							  $wpdb->delete( $table, array('wat_audio_topics_id' => $audio_topic_id[$x]));
						  }
					  }
				  }
				}								  
						  
				 $rwCnt++;
				}
			}
			}
			
		}

    
	}
}

add_action('acf/save_post', 'fnUpdateCustomTable', 15);

function fnDelDbAudio($post_id) {
	
   global $wpdb;   
   
   if ('cgc_calls' != get_post_type( $post_id))
       return;
   
   $db_audio_ids = $wpdb->get_results("SELECT wau_audio_id 
										FROM wpcr_audio 
										 WHERE wau_post_id = ".$post_id);
   
	if(!empty($db_audio_ids))                       
	{  
	  foreach($db_audio_ids as $row){ 
			deleteAudio($row->wau_audio_id);
	  }
	}   
   
}

add_action( 'trashed_post', 'fnDelDbAudio' );


?>
