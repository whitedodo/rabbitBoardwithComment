<?php
/*
	File: rss_paper.php
	Author: Jaspers
	Created by 2018-07-13
	Goal: 게시판 Model 구현
	Description:
	2018-07-13 / Jasper / Rss_paper
*/

  header("Content-Type: application/rss+xml");
  header("Content-Type: text/xml");
  $now = date("D, d M Y H:i:s T"); // 현재시간
  
	require('jasper.php');
	
	$board = new JasperBoard();
	$boardName = $_GET['name'];
	$itemList = $board->readArticle($boardName, 2); // 1: {Article}, 2: {Item}
	
	$rssInfo = new RSSInfo();
	$url = "http://" . $_SERVER["HTTP_HOST"] . "/board/";
	$rssInfo->setTitle("Jasper RSS with " . $boardName );
	$rssInfo->setLink( $url . "list.php?name=" . $boardName );
	$rssInfo->setDescription( "RSS Feeder with Board" );
	$rssInfo->setPubDate( $now );
	
?>
<?php
  // 1. Bodies(몸체) - XML(RSS)

  echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
  echo "\t\t<rss version=\"2.0\">\n";
  echo "\t\t\t<channel>\n";
  echo "\t\t\t\t<title>";
  echo $rssInfo->getTitle();
  echo "</title>\n";
  echo "\t\t\t\t<link>";
  echo $rssInfo->getLink();
  echo "</link>\n";
  echo "\t\t\t\t<description>";
  echo $rssInfo->getDescription();
  echo "</description>\n";
  echo "\t\t\t\t<pubDate>";
  echo $rssInfo->getPubDate();
  echo "</pubDate>\n\n"; // XML이 만들어진 시간을 입력한다.
  
  // Item - 값 채우기
  
  $index = 0;
  
  foreach ( $itemList as $item ){
  
    if ( $index != 0 ){
      echo "\t\t\t\t";
      echo "<item>\n";
      echo "\t\t\t\t\t";
      echo "<author>";
      echo $item->getAuthor();
      echo "</author>\n";
      echo "\t\t\t\t\t";
      echo "<category>";
      echo $item->getCategory();
      echo "</category>\n";
      echo "\t\t\t\t\t";
      echo "<title>";
      echo $item->getTitle();
      echo "</title>\n";
      echo "\t\t\t\t\t";
      echo "<link>";
      echo $url;
      echo "view.php?name=$boardName&amp;page=1&amp;id=";
      echo $item->getLink();
      echo "</link>\n";
      echo "\t\t\t\t\t";
      echo "<guid>";
      echo $url;
      echo "view.php?name=$boardName&amp;page=1&amp;id=";
      echo $item->getGuid();
      echo "</guid>\n";
      echo "\t\t\t\t\t";
      echo "<pubDate>";
      echo $item->getPubDate();
      echo "</pubDate>\n";
      echo "\t\t\t\t\t";
      echo "<description><![CDATA[";
      echo $item->getDescription();
      echo "]]></description>\n";
      echo "\t\t\t\t";
      echo "</item>\n";
    }
    else{  
      $index++;
    }
  }
  echo "</channel>\n";
  echo "</rss>\n";
?>