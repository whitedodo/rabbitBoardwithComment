<?php
/*
	File: view_session.php
	Author: Jaspers
	Created by 2018-07-10
  Goal: view.php 세션 - 조회수 증가(중복조회 방지)
	Description: 
	
*/

$cnt_list;                    // 세션 임시변수

// 세션 존재 유무
if(!isset($_SESSION['cnt_list'])){
  $_SESSION["cnt_list"]= "";
}else{
  $cnt_list = unserialize($_SESSION['cnt_list']); 
}

$cnt_list_dummy = explode( ";", $cnt_list );

$view_cnt_ok = 0; //조회수를 올려도 되는지 저장하는 변수를 초기화

for($i = 0; $i < sizeof($cnt_list_dummy); $i++)
{ 
  $target = $boardName . "_" . $article_id . "_" . $ip . "_" . $usrdate;
  
  if( strcmp($cnt_list_dummy[$i], $target) == 0 ) // 일치하는 세션이 있다면
  {
    $view_cnt_ok = 1; // 존재:1 / 미존재:0
    break;
  }
}

if( $view_cnt_ok == 0 ) // 조회 여부 판단
{
  $article = new Article();
  $article->setID( $article_id );
  $board->updateCount($boardName, $article);  // 조회수 증가
  
  //echo "증가" . $cnt_list . "<br>";
  $cnt_list.= ";" . $boardName . "_" . $article_id . "_" . $ip . "_" . $usrdate;
  $_SESSION["cnt_list"] = serialize($cnt_list);
}
else{
 
}

?>