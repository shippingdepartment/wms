<?php
//Notes Class

class Notes {
	public $note_title;
	public $note_detail;
	public $readstatus;
	
	function set_note($note_id) { 
		global $db;
		$query = 'SELECT * from notes WHERE note_id="'.$note_id.'" AND warehouse_id="'.$_SESSION['warehouse_id'].'"';
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		$this->note_title = $row['note_title'];
		$this->note_detail = $row['note_detail'];
		$this->readstatus = $row['readstatus'];
	}//level set ends here.
	
	function update_note($note_id, $note_title, $note_detail) { 
		global $db;
		global $language;
		$query = 'UPDATE notes SET
				  note_title = "'.$note_title.'",
				  note_detail = "'.$note_detail.'"
				   WHERE note_id="'.$note_id.'" AND warehouse_id="'.$_SESSION['warehouse_id'].'"';
		$result = $db->query($query) or die($db->error);
		return $language["note_update_success"];
	}//update user level ends here.
	
	function list_notes() {
		global $db;
		global $language;
		$query = 'SELECT * from notes WHERE user_id="'.$_SESSION['user_id'].'" ORDER by note_id DESC';
		$result = $db->query($query) or die($db->error);
		
		$content = '';
		while($row = $result->fetch_array()) { 
		 	extract($row);
			
			$content .= '<div class="my_note col-md-3">';
			$content .= '<div class="note_title">';
			$content .= '<h2 class="pull-left">'.$note_title.'</h2>';
			$content .= '<div class="pull-right" style="width:105px;padding-top:15px;">';
			$content .= '<form method="post" name="edit" action="manage_notes.php">';
			$content .= '<input type="hidden" name="edit_note" value="'.$note_id.'">';
			$content .= '<input type="submit" class="btn btn-default btn-sm pull-left" value="'.$language['edit'].'">';
			$content .= '</form>';
			$content .= '<form method="post" name="delete" onsubmit="return confirm_delete();" action="">';
			$content .= '<input type="hidden" name="delete_note" value="'.$note_id.'">';
			$content .= '<input type="submit" class="btn btn-default btn-sm pull-right" value="'.$language['delete'].'">';
			$content .= '</form>';
            $content .= '</div><div class="clearfix"></div>';
            $content .= '</div>';
			$content .= '<p><strong>'.$language["date"].': </strong>'.$note_date.'</p>';
            $content .= '<div class="note_detail"><p>'.$note_detail.'</p></div>';
            $content .= '</div><!--note ends here.-->';
		 }//while loop ends here.
		 echo $content;
	}//list_notes ends here.
	
	function add_note($note_title, $note_detail) { 
		global $db;
		global $language;
		$query = 'INSERT into notes VALUES(NULL, "'.date("Y-m-d").'", "'.$note_title.'", "'.$note_detail.'", "'.$_SESSION['user_id'].'", "'.$_SESSION['warehouse_id'].'", "0")';
		$result = $db->query($query) or die($db->error);
		return $language["note_added_success"];
	}//add notes ends here.

	function delete_note($note_id) {
		global $db;
		global $language;
			$query = 'DELETE from notes WHERE user_id="'.$_SESSION['user_id'].'" AND note_id="'.$note_id.'"';
			$result = $db->query($query) or die($db->error);
			$message = $language["note_delete_success"];	
		return $message;
	}//delete level ends here.

function notes_widget() {
		global $db;
		$query1 = 'SELECT * from notes WHERE readstatus="0" ORDER by note_id  ';
		$result1 = $db->query($query1) or die($db->error);
		$count = $result1->num_rows;
		
		$query = 'SELECT * from notes WHERE readstatus="0" ORDER by note_id  DESC LIMIT 10';
		$result = $db->query($query) or die($db->error);
		$content = '';
		
		while($row = $result->fetch_array()) { 
			extract($row);
			$content .= '<li><a href="includes/processnotes.php?noteid='.$note_id.'"><span class="notification-badge bg-primary"><i class="fa fa-photo"></i></span>';
			$content .= '<span class="notification-info text-danger"><strong>'.strip_tags($note_title).'</strong></span>';
			$note_detail = (strlen($note_detail) > 103) ? substr($note_detail,0,60).'...' : $note_detail;;
			$note_detail = strip_tags($note_detail);
			$content .= '<span class="notification-info" style="font-size:12px">'.strip_tags($note_detail).'</span>';
			$content .= '<small class="notification-date text-info" style="font-size:10px">';
			$content .= $note_date;
			$content .= '</small></a></li>';
			
		}//loop ends here.
			if ($count> 2) {
				$content .= '<li style="color:0000FF;text-align:right"><a href="#" ><small class="notification-date text-primary" ><b> <i class="fa fa-bell"></i> All Notifications >> </b></small></a></li>';
			}
	echo $content;
	}//list_notes ends here.
	
	function notes_count() { 
		global $db;
		$warh = $_SESSION['warehouse_id'];
		$query = "SELECT * from notes WHERE warehouse_id='".$warh."' AND readstatus='0'";
		$result = $db->query($query) or die($db->error);
		echo $result->num_rows;
	}//unread count ends here.
	
	function noteread($note_id) { 
		global $db;
		//global $language;
		$query = 'UPDATE notes SET
				  readstatus = "1"
				   WHERE note_id="'.$note_id.'"';
		$result = $db->query($query) or die($db->error);
		//return $language["note_update_success"];
	}//update user level ends here.

}//class ends here.