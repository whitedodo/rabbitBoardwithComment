<?php
/*
	File: remove_ok.php
	Author: Jaspers
	Created by 2018-07-10
	Description: remove.php에서 사용됨.
*/

$util = new BoardFn();
$article = new Article();
$article->setID( $article_id );
$comment;

// 게시글 존재여부
$result = $board->isArticle($boardName, $article);

if ( !$result ){
  echo "<script>alert(\"게시글이 존재하지 않습니다.\");";
  echo "location.href(\"list.php?name=$boardName\");";
  echo "</script>";
} 


$count = -1;
$SUCCESS = 3;

$passwdErr = "";
$passwd = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
   if (empty($_POST["passwd"])) {
       $passwdErr = "Password is required";
       $count++;
   }else {
       $passwd = $util->test_input($_POST["passwd"]);
       
       /*
       // check if e-mail address is well-formed
       if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $emailErr = "Invalid email format"; 
       }
       */
   }
}

// 내용 비어있는지 확인
if ( !empty($passwd) ){
  
  $article->setPassword($passwd); // 비밀번호 넣기
  
  if ( $board->isPassword($boardName, $article) != false ){
    $count = $SUCCESS;
  }
  else{
    $passwdErr = "비밀번호 불일치(Password mismatch)";
  }
}

// 게시글 담기
if ( $count == $SUCCESS )
{ 
  $article->setPassword( $passwd );
  $comment = new Comment();
  $comment->setArticle_ID( $article_id );
  
  // 글 삭제(Article - Remove)
  $result = $board->removeComment($boardName, $comment);  // 댓글 삭제
  $result = $board->remove($boardName, $article);         // 게시글 삭제
  
//  echo $result;
  
  if ( $result == true )
  {
//    echo $boardName;
    echo "<script>alert('성공적으로 삭제하였습니다.\\n(Successfully deleted.)');";
    echo "location.href(\"list.php?name=$boardName\");";
    echo "</script>";
  }
  
}

?>