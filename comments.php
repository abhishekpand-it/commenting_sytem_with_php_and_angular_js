<?php
class db {

	const SERVER = "localhost";
	const USER = "root";
	const PASSWORD = "";
	const DB = "newdb";
	
	private static $instance = NULL;
	private $connection;
	
	private function __construct(){
		$this->connection = mysql_connect(self::SERVER, self::USER, self::PASSWORD);
		if($this->connection){
			mysql_select_db(self::DB, $this->connection);
		}
	}
	
	private function __clone(){
		// to avoid cloning this class
	}
	
	protected static function dbInstance(){
		if(NULL == self::$instance){
			self::$instance = new self;
		}
		return self::$instance;
	}
}

class comments extends db {

	var $con;
	
	public function __construct(){
		parent::dbInstance();
	}
	
	public function getComments(){
		$qry = mysql_query("SELECT comment_id, comments, timestamp FROM comments ORDER BY timestamp DESC");
		$data = array();
		while($rows = mysql_fetch_array($qry)){
			$data[] = array("id" => $rows['comment_id'],
						  "msg" => $rows['comments'],
						  "timestamp" => $rows['timestamp']);
		}
		return json_encode($data);
	}
	
	public function getReplies(){
		$qry = mysql_query("SELECT reply_id, comment_id, replies, timestamp FROM reply ORDER BY timestamp DESC");
		$data = array();
		while($rows = mysql_fetch_array($qry)){
			$data[] = array("id" => $rows['reply_id'],
							"c_id" => $rows['comment_id'],
						  "msg" => $rows['replies'],
						  "timestamp" => $rows['timestamp']);
		}
		return json_encode($data);
	}
	
	public function addComment($post){
		$comments = mysql_escape_string($post['msg']);
		$time = time();
		$id = 0;
		$qry = mysql_query("INSERT INTO comments(comments,timestamp)VALUES('{$comments}','{$time}')")or die(mysql_error());
		$id = mysql_insert_id();
		return json_encode(array("id" => $id,
						  		 "msg" => stripslashes($comments),
						  		 "timestamp" => $time));
	}
	
	public function addReply($post){
		$reply = mysql_escape_string($post['msg']);
		$comment_id = $_POST['c_id'];
		$time = time();
		$id = 0;
		$qry = mysql_query("INSERT INTO reply(comment_id, replies, timestamp)VALUES('{$comment_id}','{$reply}','{$time}')")or die(mysql_error());
		$id = mysql_insert_id();
		return json_encode(array("id" => $id,
						  		 "msg" => stripslashes($reply),
								 "c_id" => $comment_id,
						  		 "timestamp" => $time));
	}
	
	public function deleteComment($id){
		(int)$id = mysql_escape_string($id);
		$del = mysql_query("DELETE FROM comments WHERE comment_id = ".$id);
		if($del)
			return true;
		return false;
	}
	
}
?>