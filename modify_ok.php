<?php
/*
	File: modify_ok.php
	Author: Jaspers
	Created by 2018-07-10
	Description: modify.php에서 사용됨.
	2018-07-12 / Jaspers / 비밀번호 정규식 적용
	2019-10-07 / Jasper / isPassword(,) 버그 수정
*/

$util = new BoardFn();

// 게시글 존재여부
$result = $board->isArticle($boardName, $article);

if ( !$result ){
  echo "<script>alert(\"게시글이 존재하지 않습니다.\n";
  echo "(This post does not exist.)\");";
  echo "location.href(\"list.php?name=$boardName\");";
  echo "</script>";
} 

$count = -1;
$SUCCESS = 8;
$pattern = $util->passwordPattern();

$subjectErr = $authorErr = $passwdErr = $memoErr = $modeErr = "";
$subject = $article->getSubject();
$author = $article->getAuthor();
$passwd = "";
$memo = $article->getMemo();
$mode = $article->getMode();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   if (empty($_POST["subject"])) {
       $subjectErr = "Subject is required";
       $count++;
   }else {
       $subject = $util->test_input($_POST["subject"]);
   }
    
   if (empty($_POST["author"])) {
       $authorErr = "Author is required";
       $count++;
   }else {
       $author = $util->test_input($_POST["author"]);
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
    
   if (empty($_POST["memo"])) {
      $memoErr = "Required Memo(내용 입력)";
      $count++;
   }else {
      $memo = $util->test_input($_POST["memo"]);
   }
   
   if (empty($_POST["mode"])) {
      $modeErr = "Required Mode(양식 필수)";
      $count++;
   }else {
      $mode = $util->test_input($_POST["mode"]);
   }
}

// 아티클 생성
$article = new Article();
$article->setID($article_id);
$article->setSubject($subject);
$article->setAuthor($author);
$article->setPassword($passwd);
$article->setMemo($memo);
$article->setMode($mode);

// 내용 비어있는지 확인
if ( !empty($subject) && 
     !empty($author) &&
     !empty($passwd) &&
     !empty($memo) &&
     !empty($mode) ) {
    
    // 비밀번호 일치 여부
    if ( $board->isPassword($boardName, $article) != false ){
       $count = $SUCCESS;
    }
    else{
       $passwdErr = "Password is mismatch.(비밀번호는 불일치하다.)";
    }
    
}


// 게시글 담기
if ( $count == $SUCCESS )
{
  $result = $board->modify($boardName, $article);
  
  if ( $result == 1 )
  {
    header("Location: list.php?name=$boardName"); 
  }
  
}

?>
