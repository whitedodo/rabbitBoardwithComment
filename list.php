<?php
/*
	File: list.php
	Author: Jaspers
	Created by 2018-07-09
	Description:
	2018-07-10 / Jasper / 웹표준 확인
	2018-07-12 / Jasper / 수행시간 
	2018-07-12 / Jasper / 페이지 ID 산출을 위한 get정보-> function.php으로 이동
	2018-08-01 / Jasper / 검색(키워드) 기능 구현
*/
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
	<title>List - Board/Jaspers</title>
	<link rel="stylesheet" type="text/css" href="./css/mystyle.css">
	<script src="./script/myscripts.js"></script>
</head>
<body>

<?php
	require('jasper.php');
	$board = new JasperBoard();
	$boardFn = new BoardFn();
	
  $start = $boardFn->getExecutionTime();  // 수행시간 측정(시작)
	
	$boardName = $_GET['name'];
	$keyword = $_GET['keyword'];
	$page_id = $boardFn->getPageID($_GET['page']);
	
	$board->listPaging( $boardName, $page_id );
?>

<h3>게시판 목록</h3>

<!-- Article -->
<table class="tg_general" style="text-align:center;">
	<tr>
        <th class="tg-031e" style="width:10%; font-size:12px; color:#333;">
        <span style="font-weight:bold;">번호(Id)</span>
        </th>
        <th class="tg-031e" style="width:25%; font-size:12px; color:#333;">
        <span style="font-weight:bold;">제목(Subject)</span>
        </th>
        <th class="tg-031e" style="width:10%; font-size:12px; color:#333;">
        <span style="font-weight:bold;">작성자(Author)</span>
        </th>
        <th class="tg-031e" style="width:8%; font-size:12px; color:#333;">
        <span style="font-weight:bold;">등록일자(Date)</span>
        </th>
        <th class="tg-031e" style="width:8%; font-size:12px; color:#333;">
        <span style="font-weight:bold;">조회(Count)</span>
        </th>
    </tr>
    <?php
		  $board->listContent( $boardName, $keyword, $page_id );
  	?>
</table>

<!-- Pager -->
<div id="wrapper">
  <div id="content">
    <?php
      $board->pager( $boardName );
    ?>
    <br>
    <table style="width:100%">
      <tr>
        <td style="text-align:center;">
          <input type="hidden" value="<?php echo $boardName; ?>">
          <input type="text" id="keyword" class="input_box" value="<?php echo $subject;?>">
          <a href="javascript:keywordSearch('list.php?name=<?php echo $boardName; ?>');" class="button" style="color:#000;">검색(Search)</a>
        </td>
      </tr>
      <tr>
        <td>
        	<?php echo "<a href=\"write.php?name=$boardName\" class=\"button\">글쓰기(Write)</a>"; ?>
        	<?php echo "<a href=\"rss_paper.php?name=$boardName\" class=\"button\">RSS</a>"; ?>
        </td>
      </tr>
    </table>
  </div>
</div>


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