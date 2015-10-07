<?php
	require_once("comments.php");
	$comment = new comments();
	
	if(isset($_GET['action']) and $_GET['action'] == "getComments"){
		echo $comment->getComments();
		exit;
	}
	
	if(isset($_GET['action']) and $_GET['action'] == "getReplies"){
		echo $comment->getReplies();
		exit;
	}
	
	if(isset($_GET['action']) and $_GET['action'] == "delete"){
		$comment->deleteComment($_GET['id']);
		exit;
	}
	
	if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST"){
		//add reply
		if(isset($_POST['c_id'])){
			echo $comment->addReply($_POST);
		}
		//add comment
		else{
			echo $comment->addComment($_POST);
		}
		exit;
	}
	
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Angular Demo</title>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular.min.js"></script>
<script type="text/javascript">
var app = angular.module('myApp', []);
 app.controller('commentsController', function ($scope, $http, $interval){
	
	$http.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
	var promise;
	load_comments();
	load_replies();
	
	$scope.start= function(){
	promise = $interval(function(){
		load_comments();
		load_replies();
	},2000);
	};
	function load_comments(){
	$http.get("index.php?action=getComments")
	     .success(function(data){ $scope.comments = data;  });
	};
	function load_replies(){
	$http.get("index.php?action=getReplies")
	     .success(function(data){ $scope.reply = data;  });
	};
	
	$scope.stopit = function(){
      $interval.cancel(promise);
	};
	
	$scope.start();
	
	$scope.addComment = function(comment){
		if("undefined" != comment.msg){
			$http({
			  	method : "POST",
			  	url : "index.php", 
				data : "action=add&msg="+comment.msg
			  }).success(function(data){
				  $scope.comments.unshift(data); 
			  });
			$scope.comment = "";
		}
	}
	
	$scope.deleteComment = function(index){
		$http({
			  method : "GET",
			  url : "index.php?action=delete&id="+$scope.comments[index].id,
		}).success(function(data){
			$scope.comments.splice(index,1);
		});
	}
	
	$scope.addReply = function(comment_index, reply_msg){
		if("undefined" != comment_index){
			$http({
			  	method : "POST",
			  	url : "index.php", 
				data : "action=add_reply&msg="+reply_msg+"&c_id="+comment_index
			  }).success(function(data){
				  load_replies();
				  $scope.start();
			  });
			$scope.reply = "";
		}
	}
 });
</script>
<style type="text/css">
* { padding:0px; margin:0px; }
body{font-family:arial}
textarea{border:solid 1px #333;width:520px;height:30px;font-family:arial;padding:5px}
.main{margin:0 auto;width:600px; text-align:left:}
.updates
{
padding:20px 10px 20px 10px ;
border-bottom:dashed 1px #999;
background-color:#f2f2f2;
}
.button
{
padding:10px;
float:right;
background-color:#006699;
color:#fff;
font-weight:bold;
text-decoration:none;

}
.updates a
{
color:#cc0000;
font-size:12px;

}
</style>
</head>
<body>
	<div ng-app="myApp" id="ng-app" class="main">
		<br/>
	<h1>WritingMinds Assignment - Abhishek Pandit</h1><br />
	<h2><a href="http://re.vu/cyberlord92">Click here to Visit My Profile</a></h2><br /><br />
	<div ng-controller="commentsController">



	<div>
	<textarea name="submitComment" ng-model="comment.msg" placeholder="Post the comment"></textarea>
	<a href="javascript:void(0);" class="button" ng-click="addComment(comment)">POST</a>
	</div>

	  <!-- Comments -->
	  <div ng-repeat="comment in comments">
	    <div class="updates">
	    <a href="javascript:void(0);" ng-click="deleteComment($index);" style='float:right'>Delete</a>
	    {{comment.msg}}
		<br /><br />
			<div style="margin-left:70px">
			<div ng-repeat = "replies in reply">
				<div ng-if="comment.id == replies.c_id">
				<br />
				{{replies.msg}}
				<br />
				</div>
			</div>	
			<br>
			<textarea style=" width:380px;height:30px;font-family:arial;padding:5px" name="submitReply" ng-model="replies.msg" ng-click="stopit();" placeholder="Submit your Reply"></textarea>
			<a href="javascript:void(0);" class="button" style="color:#fff;" ng-click="addReply(comment.id, replies.msg)">REPLY</a>
			</div>
	    </div>
	  </div>

	</div>
	</div>


</body>
</html>
