<?php	
/*
  <program>  Copyright (C) 2018  Jaspers
	File: protected.php
	Author: Jaspers
	Created by 2018-07-12
  Goal:
	Description:
*/

	require('jasper.php');
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
	<title>보호된 게시물(Protected posts) - Board/Jaspers</title>
	<link rel="stylesheet" type="text/css" href="./css/mystyle.css">
  <script src="./script/myscripts.js"></script>
</head>
<body>

<?php
	$board = new JasperBoard();
	$boardFn = new BoardFn();
	
  $start = $boardFn->getExecutionTime();  // 수행시간 측정(시작)
	
  $boardName = $_GET['name'];
  
  if ( !empty ($_GET['page'] ) )
    $page_id = $_GET['page'];

  if ( !empty ($_POST['page'] ) )
    $page_id = $_POST['page'];

	$article_id = $_GET['id'];
?>

<?php
	require('protected_ok.php');
?>

<h3>보호된 게시물(Protected posts)</h3>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]). "?name=" . $boardName . "&id=" . $article_id; ?>">
<input type="hidden" name="page" value="<?php echo $page_id; ?>">
<table class="tg_general" style="width:100%;">
  <tr>
    <td class="tg-nknw" style="background-color:#E2E2E2; width:10%;">
      <span style="font-weight:bold;">비밀번호 입력해주세요.(Please enter your password.)</span>
    </td>
  </tr>
  <tr>
    <td style="text-align:left;">
      <input type="password" name="passwd" class="input_box" value="<?php echo $passwd;?>">
      <span class="error">* <?php echo $passwdErr;?></span>
    </td>
  </tr>
</table>
<!-- 하단 -->
<table style="width:100%">
  <tr>
    <td>
      <input type="submit" name="submit" value="전송(Submit)" class="button" style="color:#000;">
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
