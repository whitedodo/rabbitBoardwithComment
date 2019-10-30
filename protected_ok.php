<?php
/*
	File: protected_ok.php
	Author: Jaspers
	Created by 2018-07-10
	Description: protect.php에서 사용됨.
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
$passwd = $page = "";

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
   
   if (empty($_POST["page"])) {   }
   else {
       $page = $util->test_input($_POST["page"]);
   }
}

// 내용 비어있는지 확인
if ( !empty($passwd)){
  
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
  session_start();              //세션 열기
  $usrdate = date("Y-m-d");
  
  if ( !empty($page) ){
    
    $protected_list.= ";" . $boardName . "_" . $article_id . "_" . $usrdate;
    $_SESSION["protected_list"] = serialize($protected_list); // 저장
    header("Location: view.php?name=$boardName&page=$page&id=$article_id");
  }
  else{  
    $protected_list.= ";" . $boardName . "_" . $article_id . "_" . $usrdate;
    $_SESSION["protected_list"] = serialize($protected_list); // 저장
    
    header("Location: modify.php?name=$boardName&id=$article_id");
  }
  
}

?>