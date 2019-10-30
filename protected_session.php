<?php
/*
	File: protected_session.php
	Author: Jaspers
	Created by 2018-07-11
  Goal: view.php, modify.php 세션 - 보호 게시물
	Description: 
	2018-07-13 / Jasper / Protected Session (View, Modify) 구분
*/

session_start();              //세션 열기

// 보호글 일 때
if ( strcmp( $article->getMode(), 'protected' ) == 0 )
{
  $protected_list;              // 세션 임시변수
  
  // 세션 존재 유무
  if(!isset($_SESSION['protected_list'])){
    $_SESSION["protected_list"]= "";
  }else{
    $protected_list = unserialize($_SESSION['protected_list']); // 세션 불러오기
  }
  
  $protected_list_dummy = explode( ";", $protected_list );
  
  $board_cnt_ok = 0; // 보호글 탐색
  for($i = 0; $i < sizeof($protected_list_dummy); $i++)
  {
    $target = $boardName . "_" . $article_id . "_" . $usrdate;
    
    if( strcmp($protected_list_dummy[$i], $target) == 0 ) // 일치하는 세션이 있다면
    {
      $board_cnt_ok++; // 존재:1 / 미존재:0
      break;
    }
  }
  
  if( $board_cnt_ok == 0 ) // 보호 섹션으로 이동
  {
    if ( empty ($boardName) &&
         empty ($article_id) ){
         header("Location: about:blank;");
    }
    if ( !empty ($page_id) ){    
      header("Location: protected.php?name=$boardName&page=$page_id&id=$article_id");
    }
    
    if ( empty ($page_id) ){    
      header("Location: protected.php?name=$boardName&id=$article_id");
    }
  }
  
  // 사용 후 일회성 세션 폐기
  if( $board_cnt_ok == 1 )
  {
  
    echo strpos($_SERVER["PHP_SELF"], "modfiy");
    if ( strpos($_SERVER["PHP_SELF"], "view") != false ){
      if ( !empty ($page_id) && !empty($refresh_id) ){
        unset( $_SESSION["protected_list"] );
      }
    }
    
    if ( strpos($_SERVER["PHP_SELF"], "modify")  != false ){
      if ( !empty ($page_id) ){
        unset( $_SESSION["protected_list"] );
      }
    }

  }
  
}

?>