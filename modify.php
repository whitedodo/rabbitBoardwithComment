<?php	
/*
	File: modify.php
	Author: Jaspers
	Created by 2018-07-10
  Goal: 게시판 글 수정 화면(Modify)
  Description:
  2018-07-11 / Jasper / CKEditor 추가
*/

	require('jasper.php');
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
	<title>게시물 글 수정(Edit posts) - Board/Jaspers</title>
	<link rel="stylesheet" type="text/css" href="./css/mystyle.css">
	<script src="//cdn.ckeditor.com/4.10.0/standard/ckeditor.js"></script>
	<script src="./script/myscripts.js"></script>
</head>
<body>

<?php
	$board = new JasperBoard();
	$boardFn = new BoardFn();
	
  $start = $boardFn->getExecutionTime();  // 수행시간 측정(시작)
  
	$boardInfo = new Board();
 	$boardInfo->setName($_GET['name']);
 	$boardInfo->setPage($_GET['page']);
  $boardInfo->setArticle_ID($_GET['id']);
  
  $boardName = $boardInfo->getName();
  $page_id = $boardInfo->getPage();
  $article_id = $boardInfo->getArticle_ID();
  
 	$usrdate = date("Y-m-d");
?>

<?php
  $article = $board->read($boardName, $article_id);
?>

<?php
	require('modify_ok.php');
	require('protected_session.php');
?>

<h3>게시물 글 수정(Edit posts)</h3>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?name=" . $boardName . "&id=" . $article_id; ?>">
<table class="tg_general" style="width:100%;">
  <tr>
    <td class="tg-nknw" style="background-color:#E2E2E2; width:10%;">
      <span style="font-weight:bold;">제목(Subject)</span>
    </td>
    <td style="text-align:left;">
      <input type="text" name="subject" class="input_box" value="<?php echo $subject;?>">
      <span class="error">* <?php echo $subjectErr;?></span>
    </td>
  </tr>
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
      <span style="font-weight:bold;">양식(Mode)</span>
    </td>
    <td style="text-align:left;">
      <?php $boardFn->printMode($mode); ?>
    </td>
  </tr>
  <tr>
    <td class="tg-nknw" style="background-color:#E2E2E2;" colspan="2">
      <span style="font-weight:bold;">내용(Memo)</span>
    </td>
  </tr>
  <tr>
    <td colspan="2" style="height:200px;text-align:left;">
      <br>
      <textarea class="ckeditor" name="memo" rows="5" cols="40"><?php echo $memo;?></textarea>
      <span class="error">* <?php echo $memoErr;?></span>
      <br>
    </td>
  </tr>
</table>
<!-- 하단 -->
<table style="width:100%">
  <tr>
    <td>
      <input type="submit" name="submit" value="수정(Modify)" class="button" style="color:#000;">  
      <?php
      echo "<a href=\"list.php?name=$boardName\" class=\"button\">목록(List)</a>";
      ?>
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
