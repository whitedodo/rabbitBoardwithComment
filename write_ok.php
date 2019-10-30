<?php
/*
	File: write_ok.php
	Author: Jaspers
	Created by 2018-07-10
	Description: write.php에서 사용됨.
	2018-07-12 / Jaspers / 비밀번호 정규식 적용
*/

$article;
$pattern = $boardFn->passwordPattern();

$count = -1;
$SUCCESS = 8;

$subjectErr = $authorErr = $passwdErr = $passwdChkErr = $memoErr = "";
$subject = $author = $passwd = $passwd2 = $memo = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   if (empty($_POST["subject"])) {
       $subjectErr = "Subject is required";
       $count++;
   }else {
       $subject = $boardFn->test_input($_POST["subject"]);
   }
    
   if (empty($_POST["author"])) {
       $authorErr = "Author is required(작성자는 필수이다)";
       $count++;
   }else {
       $author = $boardFn->test_input($_POST["author"]);
   }
    
   if (empty($_POST["passwd"])) {
       $passwdErr = "Password is required(비밀번호는 필수이다)";
       $count++;
   }else {
       $passwd = $boardFn->test_input($_POST["passwd"]);
       
       if(!preg_match($pattern , $passwd)){
          $passwd = "";
          $passwdErr = "8~15,대소문자,특수문자조합<br>(8-15, uppercase and lowercase letters,<br> special characters combined)";
       }
       
   }
    
   if (empty($_POST["passwd2"])) {
      $passwdChkErr = "Password Check Error(비밀번호 확인 오류)";
      $count++;
   }else {
      $passwd2 = $boardFn->test_input($_POST["passwd2"]);
      
      if ( $passwd != $passwd2 ){
         $passwdChkErr = "Password Check Error(비밀번호 확인 오류)";
      }
      
      if(!preg_match($pattern , $passwd2)){
          $passwd2 = "";
          $passwdChkErr = "8~15,대소문자,특수문자조합<br>(8-15, uppercase and lowercase letters,<br> special characters combined)";
      }
      
   }
   
   if (empty($_POST["mode"])) {
      $modeErr = "Required Mode(양식 필수)";
      $count++;
   }else {
      $mode = $boardFn->test_input($_POST["mode"]);
   }
    
   if (empty($_POST["memo"])) {
      $memoErr = "Required Memo(내용 입력)";
      $count++;
   }else {
      $memo = $boardFn->test_input($_POST["memo"]);
   }
}

// 내용 비어있는지 확인
if ( !empty($subject) && 
     !empty($author) &&
     !empty($passwd) &&
     !empty($memo) && 
     !empty($mode) ){
  $count = $SUCCESS;
}

// 게시글 담기
if ( $count == $SUCCESS )
{ 
  $article = new Article();
  $article->setSubject( $subject );
  $article->setAuthor( $author );
  $article->setPassword( $passwd );
  $article->setMemo( $memo );
  $article->setMode( $mode );
  $article->setIP($_SERVER["REMOTE_ADDR"]);
  $article->setRegidate(date("Y-m-d H:i:s"));
  $article->setCount(0);
  
  // 글 등록(Article - Register)
  $result = $board->write($boardName, $article);

  if ( $result == 1) {
    header("Location: list.php?name=$boardName"); 
  }
}

?>