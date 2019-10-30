<?php
/*
	File: comment_ok.php
	Author: Jaspers
	Created by 2018-07-11
	Description: view.php에서 사용됨.
	2018-07-12 / Jaspers / 비밀번호 정규식 적용
*/


$comment;
$pattern = $boardFn->passwordPattern();

$count = -1;
$SUCCESS = 8;

$memoErr = $authorErr = $passwdErr = $passwdChkErr = "";
$memo = $author = $passwd = $passwd2 = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   if (empty($_POST["memo"])) {
      $memoErr = "Required Memo";
      $count++;
   }else {
      $memo = $boardFn->test_input($_POST["memo"]);
   }
    
   if (empty($_POST["author"])) {
       $authorErr = "Author is required";
       $count++;
   }else {
       $author = $boardFn->test_input($_POST["author"]);
   }
    
   if (empty($_POST["passwd"])) {
       $passwdErr = "Password is required";
       $count++;
   }else {
   
       $passwd = $boardFn->test_input($_POST["passwd"]);
       
       if(!preg_match($pattern , $passwd)){
          $passwd = "";
          $passwdErr = "8~15,대소문자,특수문자조합<br>(8-15, uppercase and lowercase letters,<br> special characters combined)";
       }
   }
    
   if (empty($_POST["passwd2"])) {
      $passwdChkErr = "Password Check Error";
      $count++;
      
   }else {
      $passwd2 = $boardFn->test_input($_POST["passwd2"]);
      
      if ( $passwd != $passwd2 ){
         $passwdChkErr = "Password Check Error";
      }
      
      if(!preg_match($pattern , $passwd2)){
        $passwd2 = "";
        $passwdChkErr = "8~15,대소문자,특수문자조합<br>(8-15, uppercase and lowercase letters,<br> special characters combined)";
      }
   }
         
}

// 내용 비어있는지 확인
if ( !empty($memo) && 
     !empty($author) &&
     !empty($passwd) ){
  $count = $SUCCESS;
}

// 댓글 담기
if ( $count == $SUCCESS )
{ 
  $comment = new Comment();
  $comment->setArticle_ID($article_id);
  $comment->setMemo($memo);
  $comment->setAuthor($author);
  $comment->setPassword($passwd);
  $comment->setIP($_SERVER["REMOTE_ADDR"]);
  $comment->setRegidate(date("Y-m-d H:i:s"));
  
  $result = $board->writeComment($boardName, $comment);
  $result = $board->updateReplyCount($boardName, $article_id);
  
  if ( $result == 1) {
    header("Location: view.php?name=$boardName&page=$page_id&id=$article_id"); 
  }
  
}
  
?>