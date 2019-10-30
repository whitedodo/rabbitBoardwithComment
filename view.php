<?php	
/*
	File: view.php
	Author: Jaspers
	Created by 2018-07-08
	Goal: 게시판 글 조회 화면(View)
	Description:
	2018-07-10 / Jasper / 웹 표준 확인
	2018-07-10 / Jasper / 세션 - 조회수 구현
	2018-07-12 / Jasper / 보안 절차 확인 (protected_session.php) 설계 및 구현
	2018-07-12 / Jasper / 수행시간 측정
	2018-07-13 / Jasper / IP주소 일부만 출력 (글 보기 영역 수정)
*/
?>
<?php
	require('jasper.php');
?>

<?php
	$board = new JasperBoard();
	$boardFn = new BoardFn();
	$security = new Security();
	
  $start = $boardFn->getExecutionTime();  // 수행시간 측정(시작)
	
	$addition = new View();
	
	$boardInfo = new Board();
 	$boardInfo->setName( $security->xss_clean( $_GET['name'] ));
 	$boardInfo->setPage( $security->xss_clean( $_GET['page'] ));
  $boardInfo->setArticle_ID( $security->xss_clean($_GET['id'] ));
  
  $boardName = $boardInfo->getName();
  $page_id = $boardInfo->getPage();
  $article_id = $boardInfo->getArticle_ID();
  $refresh_id = $_GET['r'];
  
	$usrdate = date("Y-m-d");
	$ip = $_SERVER["REMOTE_ADDR"];
?>

<?php
	$article = $board->read( $boardName, $article_id );
	$mode = $boardFn->convertTochooseMode( $article->getMode() );
	$boardFn->refresh($refresh_id, $boardInfo);
?>

<?php
	require('protected_session.php');
	require('view_session.php');
	require('comment_ok.php');
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
	<title><?php echo $article->getSubject(); ?> - Jaspers</title>
	<link rel="stylesheet" type="text/css" href="./css/mystyle.css">
	<script src="./script/myscripts.js"></script>
	
	<script>
	
    function jasper_comment(boardName, pageID, articleID, commentID, type){
    
      var frmName = "comment_frm_" + commentID;
  //    alert ( id + "-" + type );
  //    alert ( document.forms[frmName].elements("choose").value );
  //    alert ( frmName );
      var myForm = document.forms[frmName];

      var startID = 1;
      var lastID = 5;
      
      while ( startID <= lastID ){
        
        var hiddenField = document.createElement("input");
        
        hiddenField.setAttribute("type", "hidden");
        
        switch ( startID )
        {
          case 1:    
            hiddenField.setAttribute("name", "boardName");
            hiddenField.setAttribute("value", boardName);   
            break;
            
          case 2:
            hiddenField.setAttribute("name", "pageID");
            hiddenField.setAttribute("value", pageID);
            break;
      
          case 3:
            hiddenField.setAttribute("name", "articleID");
            hiddenField.setAttribute("value", articleID);
            break;
            
          case 4:
            hiddenField.setAttribute("name", "commentID");
            hiddenField.setAttribute("value", commentID);
            break;
            
          case 5:
            hiddenField.setAttribute("name", "choose");
            hiddenField.setAttribute("value", type);
            break;
        }
        
        myForm.appendChild(hiddenField);
        
        startID++;
      }

      myForm.submit();
    }
  </script>
</head>
<body>

<h3>게시물 보기(View post)</h3>

<table class="tg_general" style="width:100%;">
  <tr> 
    <td class="tg-nknw" style="background-color:#E2E2E2; width:10%;">
			<span style="font-weight:bold;">번호(num)</span></td>
		<td class="tg-nknw"><?php echo $article->getID(); ?></td>
	</tr>
  <tr> 
    <td class="tg-nknw" style="background-color:#E2E2E2; width:10%;">
			<span style="font-weight:bold;">제목(subject)</span></td>
		<td class="tg-nknw"><?php echo $article->getSubject(); ?></td>
	</tr>
  <tr> 
    <td class="tg-nknw" style="background-color:#E2E2E2; width:10%;">
			<span style="font-weight:bold;">작성자(author)</span></td>
		<td class="tg-nknw"><?php echo $article->getAuthor(); ?></td>
	</tr>	
  <tr> 
    <td class="tg-nknw" style="background-color:#E2E2E2; width:10%;">
			<span style="font-weight:bold;">양식(Mode)</span></td>
		<td class="tg-nknw"><?php echo $boardFn->titleMode($mode); ?></td>
	</tr>	
  <tr> 
    <td class="tg-nknw" style="background-color:#E2E2E2; width:10%;">
			<span style="font-weight:bold;">내용(Memo)</span></td>
		<td class="tg-nknw">
		  <?php echo $article->getMemo(); ?>
		</td>
	</tr>	
  <tr> 
    <td class="tg-nknw" style="background-color:#E2E2E2; width:10%;">
			<span style="font-weight:bold;">등록일자(Regidate)</span></td>
		<td class="tg-nknw"><?php echo $article->getRegidate(); ?></td>
	</tr>	
  <tr> 
    <td class="tg-nknw" style="background-color:#E2E2E2; width:10%;">
			<span style="font-weight:bold;">조회수(Count)</span></td>
		<td class="tg-nknw"><?php echo $article->getCount(); ?></td>
	</tr>
  <tr> 
    <td class="tg-nknw" style="background-color:#E2E2E2; width:10%;">
			<span style="font-weight:bold;">IP주소(IP Addr)</span></td>
		<td class="tg-nknw"><?php echo substr($article->getIP(), 0, 6); ?></td>
	</tr>
</table>

<!-- Footer Menu -->
<?php
  $addition->footerMenu($boardName, $page_id, $article_id);
?>

<!-- Comment -->
<h4>댓글(Comment)</h4>

<?php
  $board->listComment( $boardName, $page_id, $article_id );
?>

<!-- 댓글 달기 -->

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?name=" . $boardName . "&page=" . $page_id . "&id=" . $article_id; ?>">

<table class="tg_general" style="width:100%; margin-top:10px;">
  <tr>
    <td class="tg-nknw" style="background-color:#E2E2E2; width:10%">
      <span style="font-weight:bold;">작성자(Author)</span>
    </td>
    <td style="text-align:left;">
      <input type="text" name="author" class="input_box" value="<?php echo $author;?>">
      <span class="error">* <?php echo $authorErr;?></span>
    </td>
  </tr>
  <tr>
    <td class="tg-nknw" style="background-color:#E2E2E2; width:10%">
      <span style="font-weight:bold;">비밀번호(Password)</span>
    </td>
    <td style="text-align:left;">
      <input type="password" name="passwd" class="input_box" value="<?php echo $passwd;?>">
      <span class="error">* <?php echo $passwdErr;?></span>
    </td>
  </tr>
  <tr>
    <td class="tg-nknw" style="background-color:#E2E2E2; width:10%">
      <span style="font-weight:bold;">비밀번호 확인<br>(Password Check)</span>
    </td>
    <td style="text-align:left;">
      <input type="password" name="passwd2" class="input_box" value="<?php echo $passwd2;?>">
      <span class="error">* <?php echo $passwdChkErr;?></span>
    </td>
  </tr>  
  <tr>
    <td colspan="2" style="height:25px;text-align:left;">
      <br>
      <textarea class="comment_box" name="memo" rows="3" cols="20"><?php echo $memo;?></textarea>
      <span class="error">* <?php echo $memoErr;?></span>
      <br>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <input type="submit" name="submit" value="댓글 달기(Write)" class="comment_button" style="color:#000;">  
    </td>
  </tr>
</table>
</form>

<!-- W3C -->
<table style="width:100%">
  <tr>
    <td>
    	<a href="http://jigsaw.w3.org/css-validator/check/referer">
    		<img style="border:0;width:88px;height:31px"
    			src="//jigsaw.w3.org/css-validator/images/vcss"
    			alt="올바른 CSS입니다!" />
    	</a>
    	<a href="http://jigsaw.w3.org/css-validator/check/referer">
    		<img style="border:0;width:88px;height:31px"
    			src="//jigsaw.w3.org/css-validator/images/vcss-blue"
    			alt="올바른 CSS입니다!" />
    	</a>
    </td>
  </tr>
</table>


<!-- 수행시간(Execution time) -->
<?php

  $end = $boardFn->getExecutionTime();
  $time = $end - $start;
  echo "\t\t\t<br>";
  echo "\t\t\t<span class=\"time_font\">";
  echo "수행시간(Execution Time):";
  echo $time . "초(Sec)</span>";
  
  echo "\t\t\t<br>";
  echo "\t\t\t<span class=\"time_font\">";
  echo "시작시간(Start Time):";
  echo $start . "초(Sec)</span>";
  echo "\t\t\t<br>";
  echo "\t\t\t<span class=\"time_font\">";
  echo "종료시간(End Time):";
  echo $end . "초(Sec)</span>";
?>

</body>
</html>
