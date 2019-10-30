<?php
/*
	File: comment_ud.php
	Author: Jaspers
	Created by 2018-07-11
	Description: view.php에서 사용됨.
  2018-07-12 / Jaspers / 비밀번호 정규식 적용
*/

?>
<?php
  header('Content-Type: text/html; charset=UTF-8');
	require('jasper.php');
?>

<?php

$util = new BoardFn();
$comment;
$pattern = $util->passwordPattern();

$count = -1;
$SUCCESS = 8;   // 성공

$board = new JasperBoard();
$memoErr = $authorErr = $passwdErr = $passwdChkErr = "";
$memo = $author = $passwd = $choose = "";
$boardName = $pageID = $articleID = $commentID = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

   if (empty($_POST["memo"])) {
      $memoErr = "Required Memo";
      $count++;
   }else {
      $memo = $util->test_input($_POST["memo"]);
   }
    
   if (empty($_POST["passwd"])) {
       $passwdErr = "Password is required";
       $count++;
   }else {
       $passwd = $util->test_input($_POST["passwd"]);
       
       if(!preg_match($pattern , $passwd)){
          $passwd = "";
          $passwdErr = "8~15,대소문자,특수문자조합<br>(8-15, uppercase and lowercase letters,<br> special characters combined)";
       }
       
   }
   
   // 게시판 이름
   if (empty($_POST["boardName"])) {   }
   else {
       $boardName = $util->test_input($_POST["boardName"]);
   }
   
   // 페이지 ID
   if (empty($_POST["pageID"])) {}
   else {
       $pageID = $util->test_input($_POST["pageID"]);
   }

   // 게시글 ID
   if (empty($_POST["articleID"])) {}
   else {
       $articleID = $util->test_input($_POST["articleID"]);
   }
   
   // 댓글 ID
   if (empty($_POST["commentID"])) {}
   else {
       $commentID = $util->test_input($_POST["commentID"]);
   }
   
   // 옵션(수정, 삭제): m, d
   if (empty($_POST["choose"])) {}
   else {
       $choose = $util->test_input($_POST["choose"]);
   }
}

$comment = new Comment();

// 내용 비어있는지 확인
if ( !empty($memo) &&
     !empty($passwd) &&
     !empty($boardName) &&
     !empty($pageID) &&
     !empty($articleID) &&
     !empty($commentID) &&
     !empty($choose) ){
     
  $comment->setID($commentID);
  $comment->setMemo($memo);
  $comment->setPassword($passwd);
     
  // 비밀번호 일치 여부
  if ( $board->isPasswordComment($boardName, $comment) != false ){
    $count = $SUCCESS;  
  }
  else
  {
    echo "<script>alert('비밀번호가 일치하지 않습니다.\\n(Passwords do not match.)');";
    echo "location.href(\"view.php?name=$boardName&page=$pageID&id=$articleID\");";
    echo "</script>";
  } 
}

if ( !empty($choose) ){
  
  if ( empty($memo) ||
     empty($passwd) ||
     empty($boardName) ||
     empty($pageID) ||
     empty($articleID) ||
     empty($commentID) ){
  
    switch ( $choose ){
      case 'm':
        echo "<script>alert('빈칸을 채워주세요.\\n(Fill in the blank.)');";
        echo "location.href(\"view.php?name=$boardName&page=$pageID&id=$articleID\");";
        echo "</script>";
  
        break;
        
      case 'd':
        echo "<script>alert('비밀번호를 채워주세요.\\n(Fill in the Password.)');";
        echo "location.href(\"view.php?name=$boardName&page=$pageID&id=$articleID\");";
        echo "</script>";
            
        break;  
    
      default:
      
        break;
    }
  }

}

// 댓글 담기
if ( $count == $SUCCESS )
{ 
  
  // 수정 모드
  if ( $choose == 'm' ){
    $board->updateComment($boardName, $comment);  
    $comment->setArticle_ID($articleID);    // 수정에서만 게시글 ID 사용
     
    echo "<script>alert('성공적으로 수정되었습니다.\\n(It has been successfully modified.)');";
    echo "location.href(\"view.php?name=$boardName&page=$pageID&id=$articleID\");";
    echo "</script>";
  }
  
  // 삭제 모드
  if ( $choose == 'd' ){
    $board->removeComment($boardName, $comment);
    $result = $board->updateReplyCount($boardName, $articleID);
    
    echo "<script>alert('성공적으로 삭제되었습니다.\\n(Deleted successfully.)');";
    echo "location.href(\"view.php?name=$boardName&page=$pageID&id=$articleID\");";
    echo "</script>";
  
  }
}

?>