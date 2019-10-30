<?php

/*
	File: Jasper.php
	Author: Jaspers
	Created by 2018-07-08
	Description:
	2018-07-10 / Jasper / 추가, 삭제, 수정 기능 구현
	2018-07-11 / Jasper / Comment 기능 추가
	2018-07-11 / Jasper / 삭제 - 구현 부분 수정
	2018-07-12 / Jasper / SQL Injection 점검(paging->operate())
	2018-07-12 / Jasper / MySQL 한글깨짐 방지(Query - UTF8 / 3개)
	2018-07-12 / Jasper / htmlEntities 한글 개선
	2018-07-12 / Jasper / JasperBoard->read() 내 - while(row()){ $index 추가 }
  2018-07-12 / Jasper / JasperBoard->read() 내 - $article = "";로 변경
  2018-07-12 / Jasper / JasperBoard->read() - MVC 구현의 View 버그 발견 (Refresh)
  2018-07-12 / Jasper / JasperBoard->read() - XSS 스크립트 방지
  2018-07-13 / Jasper / JasperBoard->readArticle() - 추가 (Article, RSS) 지원
	2018-07-13 / Jasper / IP주소 일부만 출력 (댓글 보기 영역 수정)
	2018-08-01 / Jasper / 검색(키워드) 추가
	2019-10-07 / Jasper / isPassword(,) 버그 수정
*/


header("Content-Type: text/html; charset=UTF-8");
header('X-Frame-Options: DENY');  // 'X-Frame-Options'
header('X-Frame-Options: ALLOW-FROM https://youtube.com/');

require('connect.php');
require('crypt.php');
require('model.php');
require('function.php');

class View{
  
  public function footerMenu($boardName, $pageID, $articleID){
    
    echo "\t\t<table style=\"width:100%;\">\n";
    echo "\t\t\t<tr>\n";
    echo "\t\t\t\t<td>\n";
    echo "\t\t\t\t\t";
    echo "<a href=\"write.php?name=$boardName\" class=\"button\">글쓰기(Write)</a>\n";
    echo "<a href=\"list.php?name=$boardName&page=$pageID\" class=\"button\">목록(List)</a>\n";
    echo "<a href=\"remove.php?name=$boardName&id=$articleID\" class=\"button\">삭제(Remove)</a>\n";
    echo "<a href=\"modify.php?name=$boardName&id=$articleID\" class=\"button\">수정(Modify)</a>\n";
    echo "\n\t\t\t\t";
    echo "</td>\n";
    echo "\t\t\t\t</tr>\n";
    echo "\t\t\t\t\t</table>\n";
  }
}

/*
  Class: Core - Paging
  Author: Jungdy
  Description: Algorithm with Board.  
	1. Jungdy / Implement to Paging System. (페이징 시스템 구현) / 2018-07-09
  
*/
class Paging{

	private $conn;
	private $num;
	private $page;
	private $list;
	private $block;
	private $pageNum;
	private $blockNum;
	private $nowBlock;
	private $s_page;
	private $e_page;
	
	// Constructor and Destructor(생성자와 소멸자)
	public function __construct( $conn ){
		$this->conn = $conn;
	}
  
  public function __destruct(){
    
  }
  
  // Getter and Setter
  
  public function getNum(){
    return $this->num;
  }
  
  public function getPage(){
    return $this->page;
  }
  
  public function getList(){
    return $this->list;
  }
  
  public function getBlock(){
    return $this->block;
  }
  
  public function getPageNum(){
    return $this->pageNum;
  }
  
  public function getBlockNum(){
    return $this->blockNum;
  }
  
  public function getNowBlock(){
    return $this->nowBlock;
  }
  
  public function getS_Page(){
    return $this->s_page;
  }
  
  public function getE_Page(){
    return $this->e_page;
  }
  
  public function setNum($num){
    $this->num = $num;
  }
  
  public function setPage($page){
    $this->page = $page;
  }
  
  public function setList($list){
    $this->list = $list;
  }
  
  public function setBlock($block){
    $this->block = $block;
  }
  
  public function setPageNum($pageNum){
    $this->pageNum = $pageNum;
  }
  
  public function setBlockNum($blockNum){
    $this->blockNum = $blockNum;
  }
  
  public function setNowBlock($nowBlock){
    $this->nowBlock = $nowBlock;
  }
  
  public function setS_Page($s_page){
    $this->s_page = $s_page;
  }
  
  public function setE_Page($e_page){
    $this->e_page = $e_page;
  }
  
  // Method
	public function operate($boardName, $page){
	
    $link = mysql_connect($this->conn->getHost(), 
		                      $this->conn->getUser(), 
		                      $this->conn->getPw()) or 
		                      die('Could not connect' . mysql_error());
		                      
		mysql_set_charset('utf8',$link);
		
		mysql_select_db($this->conn->getDBName()) or die('Could not select database');
	
	  //$query = "SELECT id FROM board_$boardName ORDER BY id DESC"; // SQL Injection - 미점검
	  $query = sprintf("SELECT id FROM board_%s ORDER BY id DESC", 
	                    mysql_real_escape_string($boardName));       // SQL Injection 점검
	  
    $result = mysql_query($query) or die('Query failed: ' . mysql_error());;

    $this->setNum( mysql_num_rows( $result ) );
    
    $this->setPage( $page?$page:1 );
    
    $this->setList( 10 );
    $this->setBlock ( 10 );
    
    $this->setPageNum( ceil($this->getNum() / $this->getList()) ); // 총 페이지
    $this->setBlockNum( ceil($this->getPageNum() / $this->getBlock() ) ); // 총 블록
    $this->setNowBlock( ceil( $page / $this->getBlock() ) );
    
    // Block이 0일 때
    if ( $this->getNowBlock() == 0 )
      $this->setNowBlock( 1 );
    
    $this->setS_page( ( $this->getNowBlock() * $this->getBlock() ) - 2 );
    
    if ( $this->getS_page() <= 1 ) {
      $this->setS_page( 1 );
    }
    $this->setE_page( $this->getNowBlock() * $this->getBlock() );
    
    if ($this->getPageNum() <= $this->getE_page()) {
      $this->setE_page( $this->getPageNum() );
    }
    
		// Free resultset
		mysql_free_result($result);
		
		// Closing connection
		mysql_close($link);
    
	}
	
	public function message(){
	
    echo "현재 페이지는".$this->getPage()."<br/>";
    echo "현재 블록은".$this->getNowBlock()."<br/>";

    echo "현재 블록의 시작 페이지는".$this->getS_page()."<br/>";
    echo "현재 블록의 끝 페이지는".$this->getE_page()."<br/>";

    echo "총 페이지는".$this->getPageNum()."<br/>";
    echo "총 블록은".$this->getBlockNum()."<br/>";
	}
	
	public function pager( $boardName ){
	
    $s_page = $this->getPage() - 1;
    $n_page = $this->getPage() + 1;
    $e_page = $this->getPageNum();
    
    echo "\t\t\t<table id=\"pager_font\" style=\"width:100%;\">\n";
    echo "\t\t\t\t<tr>\n";
    
    // 처음, 이전
    echo "\t\t\t\t<td style=\"width:20%;\">\n";
    echo "\t\t\t\t\t\n"; 
    echo "\t\t\t\t\t<a href=\"$PHP_SELP?name=$boardName&page=1\">처음(First)</a>\n";
    
    if ( $s_page != 0 )
      echo "\t\t\t\t<a href=\"$PHP_SELP?name=$boardName&page=$s_page\">이전(Prev)</a>\n";
    
    echo "\t\t\t\n";
    echo "\t\t\t</td>\n";
    
    // 실제 페이지
    echo "\t\t\t<td>\n";
    echo "\t\t\t\t<ul id=\"pager\">\n";
    for ( $p = $this->getNowBlock(); $p <= $this->getE_page(); $p++ ) {
    
      if ( $this->getPage() == $p )
      {
        echo "\t\t\t\t\t<li>";
        echo "<a href=\"$PHP_SELP?name=$boardName&page=$p\" class=\"active\">$p</a></li>\n";
      }
      else{
        echo "\t\t\t\t\t<li>";
        echo "<a href=\"$PHP_SELP?name=$boardName&page=$p\">$p</a></li>\n";
      }
    }
    echo "\t\t\t\t</ul>\n";
    echo "\t\t\t\t</td>";
    
    // 다음, 마지막
    echo "\n<td style=\"width:20%\">\n";
    echo "\t\t\t\t\n";
    if ( $this->getPage() != $e_page ){
      echo "\t\t\t";
      echo "<a href=\"$PHP_SELP?name=$boardName&page=$n_page\">Next(다음)</a>\n";
    }
    echo "\t\t\t";
    echo "<a href=\"$PHP_SELP?name=$boardName&page=$e_page\">Last(마지막)</a>\n";
    
    echo "\t\t</td>\n";
    echo "\t\t\t</tr>\n";
    echo "\t\t\t</table>\n";
	}
	
}

class JasperBoard extends IBoard{
	
	private $conn;
	private $crypt;
	private $paging;
	
	// 생성자
	public function __construct(){
		$this->conn = new Connect('localhost', 'rabbit2me', '1234', 'rabbit2me');
		$this->crypt = new Bcrypt();
		$this->paging = new Paging($this->conn);
	}
	
	// 소멸자
  public function __destruct(){
    
  }
  
  // 페이징 구현
  public function listPaging($boardName, $pageID){
    $this->paging->operate($boardName, $pageID);
  }
  
  public function pager($boardName){
    $this->paging->pager($boardName);
  }
  
  // 컨텐츠 출력
	public function listContent($boardName, $keyWord, $pageID){
		
		$link = mysql_connect($this->conn->getHost(), 
		                      $this->conn->getUser(), 
		                      $this->conn->getPw()) or 
		                      die('Could not connect' . mysql_error());
		                      
		mysql_set_charset('utf8',$link);
		
		mysql_select_db($this->conn->getDBName()) or die('Could not select database');
		
		// Paging
		$paging = $this->paging;
		$s_point = ($paging->getPage() -1 ) * $paging->getList();
		$s_list = $paging->getList();
		
		// Board Function
		$boardFn = new BoardFn();
		$targetKeywordIndex = $boardFn->convertTochooseMode("protected");
		$targetKeyword = $boardFn->chooseMode($targetKeywordIndex);
		
		//echo $targetKeywordIndex . "-" . $targetKeyword;
		
		// Performing SQL query
/*		$query = "SELECT id, subject, author, reply, mode, regidate, count " . 
		         "FROM board_$boardName ORDER BY id DESC " .
		         "LIMIT $s_point, $s_list"; // SQL 인젝션 미점검
*/

    mysql_query("set session character_set_connection=utf8;");
    mysql_query("set session character_set_results=utf8;");
    mysql_query("set session character_set_client=utf8;");
    
    if ( $keyWord != "" )
    {
      $query = sprintf("SELECT id, subject, author, reply, mode, regidate, count " . 
  		         "FROM board_%s WHERE subject like '%%%s%%' ORDER BY id DESC " .
  		         "LIMIT %s, %s",
                mysql_real_escape_string($boardName),
                mysql_real_escape_string($keyWord),
                mysql_real_escape_string($s_point),
                mysql_real_escape_string($s_list)); // SQL 인젝션 점검
    }
    
    if ($keyWord == ""){
      $query = sprintf("SELECT id, subject, author, reply, mode, regidate, count " . 
  		         "FROM board_%s ORDER BY id DESC " .
  		         "LIMIT %s, %s",
                mysql_real_escape_string($boardName),
                mysql_real_escape_string($s_point),
                mysql_real_escape_string($s_list)); // SQL 인젝션 점검
    }

		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		
		// DB Article
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

			echo "\t<tr>\n";
			
			$articleID = $row['id'];
			
			$index = 1;
			foreach ($row as $col_value) {
				
				switch ( $index )
			  {
			    // 글 제목
  			  case 2:	
  			    echo "\t\t<td class=\"tg-031e\">\n";
  			    
  			    // 보호 글 여부
  			    if ( strcmp( $row['mode'], $targetKeyword ) == 0 ){
          		$targetKeywordIndex = $boardFn->convertTochooseMode( $row['mode'] );
          		echo "[" . $boardFn->titleMode($targetKeywordIndex) . "]&nbsp;";
  			    }
  			    
  			    // 게시물  			    
	  		    echo "<a href=\"view.php?name=$boardName&page=$pageID&id=$articleID\">";
	  		    echo htmlentities($col_value, ENT_QUOTES | ENT_IGNORE, "UTF-8"); 
	  		    echo "</a>";
	  		    
	  		    // 댓글 여부
	  		    if ( $row['reply'] != 0 ){
	  		      echo "&nbsp;&nbsp;";
	  		      echo "[" . $row['reply'] . "]";
	  		    }
	  		    
	  		    echo "</td>\n";
	  		    
			      break;
			    
			    // 댓글 출력 없음
			    case 4:
			      break;
			      
			    // 보호글 출력 없음
			    case 5:
			      break;
			    
			    default:			
    				echo "\t\t<td class=\"tg-031e\">";
	  		    echo htmlentities($col_value, ENT_QUOTES | ENT_IGNORE, "UTF-8"); 
    				echo "</td>\n";
    				break;
				}
				
  			$index++;
			}
			echo "\t</tr>\n";
			
		}
		
		// Free resultset
		mysql_free_result($result);
		
		// Closing connection
		mysql_close($link);
		
	}
	
	// readArticle($boardName, $choose) 
	// choose = 1 (Article), 2 (RSS)
  public function readArticle($boardName, $choose){
  
    $itemList[] = $var;
    $link = mysql_connect($this->conn->getHost(), 
		                      $this->conn->getUser(), 
		                      $this->conn->getPw()) or 
		                      die('Could not connect' . mysql_error());
		                      
		mysql_set_charset('utf8',$link);
		
		mysql_select_db($this->conn->getDBName()) or die('Could not select database');
		
		// Board Function
		$boardFn = new BoardFn();
		$targetKeywordIndex = $boardFn->convertTochooseMode("protected");
		$targetKeyword = $boardFn->chooseMode($targetKeywordIndex);
		
		//echo $targetKeywordIndex . "-" . $targetKeyword;
		
		// Performing SQL query
/*		$query = "SELECT id, subject, author, reply, mode, regidate, count " . 
		         "FROM board_$boardName ORDER BY id DESC " .
		         "LIMIT $s_point, $s_list"; // SQL 인젝션 미점검
*/

    mysql_query("set session character_set_connection=utf8;");
    mysql_query("set session character_set_results=utf8;");
    mysql_query("set session character_set_client=utf8;");

    $query = sprintf("SELECT id, subject, author, memo, reply, mode, regidate, ip, count " .
                     "FROM board_%s ORDER BY id DESC",
                      mysql_real_escape_string($boardName)); // SQL 인젝션 점검

		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		
		// DB Article
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			
	    if ( strcmp( $row['mode'], $targetKeyword ) != 0 ){
        
        switch ( $choose )
        {
          // Article
          case 1:
            
            $article = new Article();
      
            $article->setID( htmlentities( $row['id'], ENT_QUOTES | ENT_IGNORE, "UTF-8") );
            $article->setSubject( $row['subject'] );
            $article->setAuthor( $row['author'] );
            $article->setMemo( htmlspecialchars_decode($row['memo']) );
            $article->setReply( $row['reply'] );
            $article->setMode( $row['mode'] );
            $article->setRegidate( $row['regidate'] );
            $article->setIP( $row['ip'] );
            $article->setCount( $row['regidate'] );
            
     	      array_push( $itemList, $article );
     	      
            break;
          
          // RSS
          case 2:
            $item = new RSS();
    	   		$targetKeywordIndex = $boardFn->convertTochooseMode( $row['mode'] );
        		$category = $boardFn->titleMode($targetKeywordIndex);
    	      
    	      $item->setAuthor( $row['author'] );
    	      $item->setCategory( $category );
    	      $item->setTitle( $row['subject'] );
    	      $item->setLink( $row['id'] );
    	      $item->setGuid( $row['id'] );
     	      $item->setPubDate( $row['regidate'] );
     	      $item->setDescription( htmlspecialchars_decode($row['memo']) );
     	      
     	      array_push( $itemList, $item );
     	      
     	      break;
	      
			  } // end of switch
			
  		} // end of if
  		
  	} // end of while
		
		// Free resultset
		mysql_free_result($result);
		
		// Closing connection
		mysql_close($link);
		
		return $itemList;
		
  }
	
	// 게시글 읽기 (MVC 출력 버그)
	public function read($boardName, $articleID){
		
		$article = "";
		$security = new Security();
		
    $link = mysql_connect($this->conn->getHost(), 
		                      $this->conn->getUser(), 
		                      $this->conn->getPw()) or 
		                      die('Could not connect' . mysql_error());
		                      
		mysql_set_charset('utf8',$link);
		
		mysql_select_db($this->conn->getDBName()) or die('Could not select database');
		
		// Performing SQL query
/*		$query = 'SELECT * FROM board_' . $boardName . ' ' . 
		           'where id = ' . $articleID;    // SQL 인젝션 미점검
*/		
    $query = sprintf("SELECT * FROM board_%s " . 
                     "WHERE id = '%s'",
                    mysql_real_escape_string($boardName),
                    mysql_real_escape_string($articleID)); // SQL 인젝션 점검
		
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		
		$boardFn = new BoardFn();
		
		$index = 1;
		
		// DB Article
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		
		  if ( $index == 1 ){
		    $article = new Article();
		  }
		  
		  // XSS 필터 적용
		  $id = htmlentities($row['id'], ENT_QUOTES | ENT_IGNORE, "UTF-8");
		  $id = $security->xss_clean($id);
		  
		  $article->setID($id);
 		  $article->setSubject(htmlentities($row['subject'], ENT_QUOTES | ENT_IGNORE, "UTF-8"));
 		  $article->setAuthor(htmlentities($row['author'], ENT_QUOTES | ENT_IGNORE, "UTF-8"));
 		  $article->setPassword(htmlentities($row['password'], ENT_QUOTES | ENT_IGNORE, "UTF-8"));
 		  $article->setMemo(htmlspecialchars_decode($row['memo']));
 		  $article->setReply(htmlentities($row['reply'], ENT_QUOTES | ENT_IGNORE, "UTF-8"));
 		  $article->setMode(htmlentities($row['mode'], ENT_QUOTES | ENT_IGNORE, "UTF-8"));
 		  $article->setIP(htmlentities($row['ip'], ENT_QUOTES | ENT_IGNORE, "UTF-8"));
 		  $article->setRegidate(htmlentities($row['regidate'], ENT_QUOTES | ENT_IGNORE, "UTF-8"));
 		  $article->setCount(htmlentities($row['count'], ENT_QUOTES | ENT_IGNORE, "UTF-8"));
 		  
 		  $index++;
		}
		
		// Free resultset
		mysql_free_result($result);
		
		// Closing connection
		mysql_close($link);
		
		return $article;
		
	}
	
  public function listComment($boardName, $pageID, $articleID){
  
		$link = mysql_connect($this->conn->getHost(), 
		                      $this->conn->getUser(), 
		                      $this->conn->getPw()) or 
		                      die('Could not connect' . mysql_error());
		                      
		mysql_set_charset('utf8',$link);
		
		mysql_select_db($this->conn->getDBName()) or die('Could not select database');
		
		// Performing SQL query
/*		$query = "SELECT id, memo, author, regidate, ip FROM board_" . $boardName . "_comment " .
		         " WHERE article_id = $articleID ORDER BY id DESC ";  // SQL 인젝션 미점검
*/

    mysql_query("set session character_set_connection=utf8;");
    mysql_query("set session character_set_results=utf8;");
    mysql_query("set session character_set_client=utf8;");

    $query = sprintf("SELECT id, memo, author, regidate, ip FROM board_%s_comment " .
        		         " WHERE article_id = %s ORDER BY id DESC ",
                    mysql_real_escape_string($boardName),
                    mysql_real_escape_string($articleID)); // SQL 인젝션 점검
                    
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());

		$count = mysql_num_rows( $result );
		
		// DB Article
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

      echo "\t";
      echo "<form name=\"comment_frm_" . $row['id'] . "\" method=\"post\" ";
      echo "action=\"comment_ud.php\">\n";

      echo "\t<table class=\"tg_general\" style=\"width:100%; margin-top:10px;\">";
			echo "\t<tr>\n";
			
			$index = 1;
			foreach ($row as $col_value) {
				
				// 셀 환경 설정
				switch ( $index )
				{
				  // 번호
				  case 1:
  				  echo "\t\t<td class=\"tg-031e\" style=\"width:8%;\">";
    				echo "\t\t$count";
		 				echo "</td>\n";
				    break;
				    
				  // 제목
				  case 2:
  				  echo "\t\t<td class=\"tg-031e\" style=\"width:42%;\">";
    				echo "<textarea class=\"comment_box\" name=\"memo\" rows=\"3\" cols=\"20\">";
    				echo htmlentities($col_value, ENT_QUOTES | ENT_IGNORE, "UTF-8");
  	  			echo "</textarea>";
		 				echo "</td>\n";
  				  break;
				  
	        // 등록일자
				  case 4:
  				  echo "\t\t<td class=\"tg-031e\" style=\"width:10%;\">";
    				echo "\t\t";
    				echo htmlentities($col_value, ENT_QUOTES | ENT_IGNORE, "UTF-8");
  				  break;
  				  
  				// IP주소
  				case 5:  				  
    				echo "\t\t\n<br>";
    				echo substr(htmlentities($col_value, ENT_QUOTES | ENT_IGNORE, "UTF-8"),0, 6);
		 				echo "</td>\n";
    				break;
				  
				  // 비밀번호
				  case 6:
  				  echo "\t\t<td class=\"tg-031e\" style=\"width:10%;\">";
    				echo "\t\t";
    				echo htmlentities($col_value, ENT_QUOTES | ENT_IGNORE, "UTF-8");
		 				echo "</td>\n";
				    break;
				  
				  default:
  				  echo "\t\t<td class=\"tg-031e\" style=\"width:10%;\">";
    				echo "\t\t";
    				echo htmlentities($col_value, ENT_QUOTES | ENT_IGNORE, "UTF-8");
     				echo "</td>\n";
  				  break;
				}
				
  			$index++;
			
			}
			
			$count--;
			
			// 화면 출력 - 기능(Update, Delete)
			echo "\t\t<td class=\"tg-031e\">";
			echo "<input type=\"password\" name=\"passwd\" class=\"input_box\">";
      echo "<span class=\"error\">* $passwdErr</span>";
			echo "</td>\n";
			echo "\t\t<td class=\"tg-031e\" style=\"width:20%;\">";
			echo "<input type=\"button\"";
			echo " onclick=\"";
			echo "jasper_comment('" . $boardName . "', '" . $pageID ."', '";
			echo $articleID . "', '" . $row['id'] . "', 'm')\"";
			echo " value=\"수정(Modify)\"";
			echo " class=\"comment_handle_btn\" style=\"color:#000;\">";
			echo "<input type=\"button\"";
			echo " onclick=\"";
			echo "jasper_comment('" . $boardName . "', '" . $pageID ."', '";
			echo $articleID . "', '" . $row['id'] . "', 'd')\"";
			echo " value=\"삭제(Remove)\"";
			echo " class=\"comment_handle_btn\" style=\"color:#000;\">";
			echo "</td>\n";
			
			echo "\t</tr>\n";
      echo "\t</table>\n";
			echo "</form>\n";
			
		}
		
		// Free resultset
		mysql_free_result($result);
		
		// Closing connection
		mysql_close($link);
  
  }
  
  public function updateComment($boardName, $comment){
    
	  $link = mysql_connect($this->conn->getHost(), 
		                      $this->conn->getUser(), 
		                      $this->conn->getPw()) or 
		                      die('Could not connect' . mysql_error());
		                      
		mysql_set_charset('utf8', $link);
		
		mysql_select_db($this->conn->getDBName()) or die('Could not select database');

    mysql_query("set session character_set_connection=utf8;");
    mysql_query("set session character_set_results=utf8;");
    mysql_query("set session character_set_client=utf8;");
		
		$password = $this->crypt->decrypt( $comment->getPassword() );
		
		// Performing SQL query
		/*
		$query = 'UPDATE `board_' . $boardName . '_comment`' .
		         ' SET `memo` = \'' . $comment->getMemo() . '\'' .
		         ' WHERE' .
		         ' `id` = \'' . $comment->getID() . '\' and' .
		         ' `password` = \'' . $password . '\';';
		*/  // SQL 인젝션 미점검
		
		$query = sprintf("UPDATE `board_%s_comment` SET `memo`='%s' " .
        		         "WHERE `id` ='%s' AND `password` = '%s'",
                    mysql_real_escape_string($boardName),
                    mysql_real_escape_string($comment->getMemo()),
                    mysql_real_escape_string($comment->getID()),
                    mysql_real_escape_string($password));       // SQL 인젝션 점검
		
		//echo $query;
		
		$result = mysql_query($query, $link) or die('Query failed: ' . mysql_error());
		
		// Closing connection
		mysql_close($link);
	  
	  return $result;
	  
	}
  
  public function removeComment($boardName, $comment){
  
    $id = $comment->getID();
    $passwd = $comment->getPassword();
    
    $link = mysql_connect($this->conn->getHost(), 
		                      $this->conn->getUser(), 
		                      $this->conn->getPw()) or 
		                      die('Could not connect' . mysql_error());
		                      
		mysql_set_charset('utf8', $link);
		
		mysql_select_db($this->conn->getDBName()) or die('Could not select database');

    mysql_query("set session character_set_connection=utf8;");
    mysql_query("set session character_set_results=utf8;");
    mysql_query("set session character_set_client=utf8;");
		
	  // 댓글 ID, 댓글 비밀번호가 존재할 때
	  if ( $id != "" && $passwd != "" ){

      //echo "참";
  		$password = $this->crypt->decrypt( $comment->getPassword() );
  		
  		// Performing SQL query
/*  		
  		$query = 'DELETE FROM `board_' . $boardName . '_comment`' . 
  		         ' WHERE `id` = \'' . $comment->getID() . 
  		         '\' AND `password` = \'' . $password . '\'';
*/ // SQL Injection 미점검
		         
		  $query = sprintf("DELETE FROM board_%s_comment WHERE `id` = '%s' AND " .
 		                   "`password` = '%s'",
 		                   mysql_real_escape_string($boardName),
 		                   mysql_real_escape_string($comment->getID()),
 		                   mysql_real_escape_string($password));       // SQL Injection 점검
  	
  		$result = mysql_query($query, $link) or die('Query failed: ' . mysql_error());  	
		}
		

		// 게시글 ID만 존재할 때
		if ( $comment->getArticle_ID() != "" ){
		
  		// Performing SQL query
  		$query = 'DELETE FROM `board_' . $boardName . '_comment`' . 
  		         ' WHERE `article_id` = \'' . $comment->getArticle_ID() . '\'';
  		         
  		
		  $query = sprintf("DELETE FROM board_%s_comment WHERE `article_id` = '%s'",
 		                   mysql_real_escape_string($boardName),
 		                   mysql_real_escape_string($comment->getArticle_ID()));       // SQL Injection 점검
  	
  		$result = mysql_query($query, $link) or die('Query failed: ' . mysql_error());  			
  		
		}
				
		// Closing connection
		mysql_close($link);
  	
	  
	  return $result;
  }
	
  public function isPasswordComment($boardName, $comment){
  
    $link = mysql_connect($this->conn->getHost(), 
		                      $this->conn->getUser(), 
		                      $this->conn->getPw()) or 
		                      die('Could not connect' . mysql_error());
		                      
		mysql_set_charset('utf8', $link);
		
		mysql_select_db($this->conn->getDBName()) or die('Could not select database');

    mysql_query("set session character_set_connection=utf8;");
    mysql_query("set session character_set_results=utf8;");
    mysql_query("set session character_set_client=utf8;");
		
		$password = $this->crypt->decrypt( $comment->getPassword() );

/*
		$query = 'SELECT password FROM board_' . $boardName . '_comment ' . 
		         'where id = \'' . $comment->getID() . '\' ' . 
		         'and password = \'' . $password . '\'';
*/ // SQL Injection 미점검		
		
	  $query = sprintf("SELECT password FROM board_%s_comment WHERE `id` = '%s' AND " .
	                   "`password` = '%s'",
	                   mysql_real_escape_string($boardName),
	                   mysql_real_escape_string($comment->getID()),
	                   mysql_real_escape_string($password));       // SQL Injection 점검
		
		$result = mysql_query($query, $link) or die('Query failed: ' . mysql_error());

		// DB Article
  	$password = "";
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
      $password = $row['password'];
    }
		
		// Free resultset
		mysql_free_result($result);
		
		// Closing connection
		mysql_close($link);
		
		if ( empty($password) )
		  return false;
	  else
	    return $password;
  
  }
	
	public function write($boardName, $article){
	
	  $link = mysql_connect($this->conn->getHost(), 
		                      $this->conn->getUser(), 
		                      $this->conn->getPw()) or 
		                      die('Could not connect' . mysql_error());
		                      
		mysql_set_charset('utf8', $link);
		
		mysql_select_db($this->conn->getDBName()) or die('Could not select database');

    mysql_query("set session character_set_connection=utf8;");
    mysql_query("set session character_set_results=utf8;");
    mysql_query("set session character_set_client=utf8;");
		
		$password = $this->crypt->decrypt( $article->getPassword() );
		
		// Performing SQL query
		/*
		$query = 'INSERT INTO `board_' . $boardName . '` (`id`, `subject`, `author`, ' . 
		         '`password`, `memo`, `mode`, `ip`, `regidate`, `count`) ' .
		         'VALUES (NULL, \''. $article->getSubject() . '\', \'' . 
		         $article->getAuthor() . '\', \'' . 
		         $password . '\', \'' . $article->getMemo() . '\', \'' .
		         $article->getMode() . '\', \'' . 
		         $article->getIP() . '\', \'' . 
		         $article->getRegidate() . '\', \'' . $article->getCount() . '\')';
		*/ // SQL Injection 미점검
		
	  $query = sprintf("INSERT INTO `board_%s` (`id`, `subject`, `author`, " . 
		                 "`password`, `memo`, `mode`, `ip`, `regidate`, `count`) " .
        		         "VALUES (NULL, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
	                   mysql_real_escape_string($boardName),
	                   mysql_real_escape_string($article->getSubject()),
	                   mysql_real_escape_string($article->getAuthor()),
	                   mysql_real_escape_string($password),
	                   mysql_real_escape_string($article->getMemo()),
	                   mysql_real_escape_string($article->getMode()),
	                   mysql_real_escape_string($article->getIP()),
	                   mysql_real_escape_string($article->getRegidate()),
	                   mysql_real_escape_string($article->getCount()));  // SQL Injection 점검
		
		
		$result = mysql_query($query, $link) or die('Query failed: ' . mysql_error());
	  
		// Closing connection
		mysql_close($link);
	  
	  return $result;
	}
	
	public function writeComment($boardName, $comment){
	
	  $link = mysql_connect($this->conn->getHost(), 
		                      $this->conn->getUser(), 
		                      $this->conn->getPw()) or 
		                      die('Could not connect' . mysql_error());
		                      
		mysql_set_charset('utf8', $link);
		
		mysql_select_db($this->conn->getDBName()) or die('Could not select database');

    mysql_query("set session character_set_connection=utf8;");
    mysql_query("set session character_set_results=utf8;");
    mysql_query("set session character_set_client=utf8;");
		
		$password = $this->crypt->decrypt( $comment->getPassword() );
		
		// Performing SQL query
		/*
		$query = 'INSERT INTO `board_' . $boardName . '_comment` (`id`, `article_id`, `memo`, ' . 
		         '`author`, `password`, `ip`, `regidate`) ' .
		         'VALUES (NULL, \''. $comment->getArticle_ID() . '\', \'' .
		         $comment->getMemo() . '\', \'' . $comment->getAuthor() . '\', \'' . 
		         $password . '\', \'' . $comment->getIP() . '\', \'' . 
		         $comment->getRegidate() . '\')';
		*/ // SQL Injection 미점검
		
		$query = sprintf("INSERT INTO `board_%s_comment` (`id`, `article_id`, `memo`, " . 
		         "`author`, `password`, `ip`, `regidate`) " .
		         "VALUES (NULL, '%s', '%s', '%s', '%s', '%s', '%s')",
             mysql_real_escape_string($boardName),
             mysql_real_escape_string($comment->getArticle_ID()),
             mysql_real_escape_string($comment->getMemo()),
             mysql_real_escape_string($comment->getAuthor()),
             mysql_real_escape_string($password),
             mysql_real_escape_string($comment->getIP()),
             mysql_real_escape_string($comment->getRegidate()));  // SQL Injection 점검
		
	  $result = mysql_query($query, $link) or die('Query failed: ' . mysql_error());
		
		// Closing connection
		mysql_close($link);
	  
	  return $result;
	}
	
  public function updateReplyCount($boardName, $articleID){
  
	  $link = mysql_connect($this->conn->getHost(), 
		                      $this->conn->getUser(), 
		                      $this->conn->getPw()) or 
		                      die('Could not connect' . mysql_error());
		                      
		mysql_set_charset('utf8', $link);
		
		mysql_select_db($this->conn->getDBName()) or die('Could not select database');

    mysql_query("set session character_set_connection=utf8;");
    mysql_query("set session character_set_results=utf8;");
    mysql_query("set session character_set_client=utf8;");
		
/*
		$query = 'SELECT count(*) FROM board_' . $boardName . '_comment ' . 
		         'where article_id = \'' . $articleID . '\'';
*/  // SQL Injection 미점검	
		
	  $query = sprintf("SELECT count(*) FROM board_%s_comment WHERE `article_id` = '%s'" ,
	                   mysql_real_escape_string($boardName),
	                   mysql_real_escape_string($articleID));       // SQL Injection 점검
		
		$result = mysql_query($query, $link) or die('Query failed: ' . mysql_error());
		$row = mysql_fetch_array($result);
		$count = $row[0];

		// Free resultset
		mysql_free_result($result);
		
		// Performing SQL query
		/*
		$query = 'UPDATE `board_' . $boardName . '`' .
		         ' SET `reply` = \'' . $count .
		         '\' WHERE `board_' . $boardName . '`.`id` = \'' . $articleID . '\';';
		*/ // SQL Injection 미점검
		
	  $query = sprintf("UPDATE `board_%s` SET `reply` = '%s' WHERE `board_%s`." . 
	                   "`id` = '%s'",
                 mysql_real_escape_string($boardName),
                 mysql_real_escape_string($count),
                 mysql_real_escape_string($boardName),
                 mysql_real_escape_string($articleID));       // SQL Injection 점검
		
		//echo $query;
		
		$result = mysql_query($query, $link) or die('Query failed: ' . mysql_error());
		
		// Closing connection
		mysql_close($link);
	  
	  return $result;
  }
	
	public function modify($boardName, $article){
	
	  $link = mysql_connect($this->conn->getHost(), 
		                      $this->conn->getUser(), 
		                      $this->conn->getPw()) or 
		                      die('Could not connect' . mysql_error());
		                      
		mysql_set_charset('utf8', $link);

    mysql_query("set session character_set_connection=utf8;");
    mysql_query("set session character_set_results=utf8;");
    mysql_query("set session character_set_client=utf8;");
		
		mysql_select_db($this->conn->getDBName()) or die('Could not select database');
		
		// Performing SQL query
		/*
		$query = 'UPDATE `board_' . $boardName . '`' .
		         ' SET `subject` = \'' . $article->getSubject() .
		         '\', `author` = \'' . $article->getAuthor() .
             '\', `memo` = \'' . $article->getMemo() .
             '\', `mode` = \'' . $article->getMode() .
		         '\' WHERE `board_' . $boardName . '`.`id` = \'' . $article->getID() . '\';';
		*/  // SQL Injection 미점검
		
	  $query = sprintf("UPDATE `board_%s` SET `subject` = '%s', `author` = '%s', " .
	                   "`memo` = '%s', `mode` = '%s' WHERE `board_%s`.`id` = '%s'",
	                   mysql_real_escape_string($boardName),
                     mysql_real_escape_string($article->getSubject()),
                     mysql_real_escape_string($article->getAuthor()),
                     mysql_real_escape_string($article->getMemo()),
                     mysql_real_escape_string($article->getMode()),
                     mysql_real_escape_string($boardName),
	                   mysql_real_escape_string($article->getID()));       // SQL Injection 점검
		
		$result = mysql_query($query, $link) or die('Query failed: ' . mysql_error());
		
		// Closing connection
		mysql_close($link);
	  
	  return $result;
	  
	}	
	
	public function remove($boardName, $article){
	
	  $link = mysql_connect($this->conn->getHost(), 
		                      $this->conn->getUser(), 
		                      $this->conn->getPw()) or 
		                      die('Could not connect' . mysql_error());
		                      
		mysql_set_charset('utf8', $link);
		
		mysql_select_db($this->conn->getDBName()) or die('Could not select database');

    mysql_query("set session character_set_connection=utf8;");
    mysql_query("set session character_set_results=utf8;");
    mysql_query("set session character_set_client=utf8;");
	
		$password = $this->crypt->decrypt( $article->getPassword() );
	
		// Performing SQL query
/*
		$query = 'DELETE FROM `board_' . $boardName . '`' . 
		         ' WHERE `id` = \'' . $article->getID() . 
		         '\' AND `password` = \'' . $password . '\'';
*/  // SQL Injection 미점검
	
	  $query = sprintf("DELETE FROM `board_%s` WHERE `id` = '%s' AND `password` = '%s'" ,
	                   mysql_real_escape_string($boardName),
	                   mysql_real_escape_string($article->getID()),
	                   mysql_real_escape_string($password));       // SQL Injection 점검
	                   
		$result = mysql_query($query, $link) or die('Query failed: ' . mysql_error());  
		
		// Closing connection
		mysql_close($link);
	  
	  return $result;
	}
	
	public function updateCount($boardName, $article){
	
	  $link = mysql_connect($this->conn->getHost(), 
		                      $this->conn->getUser(), 
		                      $this->conn->getPw()) or 
		                      die('Could not connect' . mysql_error());
		                      
		mysql_set_charset('utf8', $link);
		
		mysql_select_db($this->conn->getDBName()) or die('Could not select database');

    mysql_query("set session character_set_connection=utf8;");
    mysql_query("set session character_set_results=utf8;");
    mysql_query("set session character_set_client=utf8;");
		
/*
		$query = 'SELECT count(*) FROM board_' . $boardName . ' ' . 
		         'where id = \'' . $article->getID() . '\'';
*/ // SQL Injection 미점검	
	  $query = sprintf("SELECT * FROM `board_%s` WHERE `id` = '%s'" ,
	                   mysql_real_escape_string($boardName),
	                   mysql_real_escape_string($article->getID()));       // SQL Injection 점검
		
		$result = mysql_query($query, $link) or die('Query failed: ' . mysql_error());
		
		// DB Article
  	$count = 0;
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
      $count = $row['count'];
    }
		
		// Free resultset
		mysql_free_result($result);
		
		// Performing SQL query
/*
		$query = 'UPDATE `board_' . $boardName . 
		         '` SET `count` = ' . ( $count + 1 ) .
		         ' WHERE `board_' . $boardName . '`.`id` = ' . $article->getID() . ';';
*/ // SQL Injection 미점검		
			
	  $query = sprintf("UPDATE `board_%s` SET `count` = '%s' WHERE `board_%s`.`id` = '%s'" ,
	                   mysql_real_escape_string($boardName),
	                   mysql_real_escape_string($count + 1),
	                   mysql_real_escape_string($boardName),
	                   mysql_real_escape_string($article->getID()));       // SQL Injection 점검
		
		$result = mysql_query($query, $link) or die('Query failed: ' . mysql_error());
	  
		// Closing connection
		mysql_close($link);
	  
	  return $result;
	  
	}	
	
	public function isArticle($boardName, $article){
	
	  $link = mysql_connect($this->conn->getHost(), 
		                      $this->conn->getUser(), 
		                      $this->conn->getPw()) or 
		                      die('Could not connect' . mysql_error());
		                      
		mysql_set_charset('utf8', $link);
		
		mysql_select_db($this->conn->getDBName()) or die('Could not select database');

    mysql_query("set session character_set_connection=utf8;");
    mysql_query("set session character_set_results=utf8;");
    mysql_query("set session character_set_client=utf8;");
		
/*
		$query = 'SELECT id FROM board_' . $boardName . ' ' . 
		         'where id = \'' . $article->getID() . '\'';
*/ // SQL Injection 미점검

	  $query = sprintf("SELECT id FROM board_%s WHERE id ='%s'" ,
	                   mysql_real_escape_string($boardName),
	                   mysql_real_escape_string($article->getID()));       // SQL Injection 점검

		$result = mysql_query($query, $link) or die('Query failed: ' . mysql_error());

		// DB Article
  	$id = "";
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
      $id = $row['id'];
    }
		
		// Free resultset
		mysql_free_result($result);
		
		// Closing connection
		mysql_close($link);
		
		if ( empty($id) )
		  return false;
	  else
	    return true;
	}
	
	public function isPassword($boardName, $article){
	
    $usrPasswd = "";
	  $link = mysql_connect($this->conn->getHost(), 
		                      $this->conn->getUser(), 
		                      $this->conn->getPw()) or 
		                      die('Could not connect' . mysql_error());
		                      
		mysql_set_charset('utf8', $link);
		
		mysql_select_db($this->conn->getDBName()) or die('Could not select database');

    mysql_query("set session character_set_connection=utf8;");
    mysql_query("set session character_set_results=utf8;");
    mysql_query("set session character_set_client=utf8;");
		
		$password = $this->crypt->decrypt( $article->getPassword() );
/*
		$query = 'SELECT password FROM board_' . $boardName . ' ' . 
		         'where id = \'' . $article->getID() . '\' ' . 
		         'and password = \'' . $password . '\'';
*/ // SQL Injection 미점검

	  $query = sprintf("SELECT password FROM board_%s WHERE id ='%s' and password ='%s'" ,
	                   mysql_real_escape_string($boardName),
	                   mysql_real_escape_string($article->getID()),
                     mysql_real_escape_string($password));       // SQL Injection 점검

		$result = mysql_query($query, $link) or die('Query failed: ' . mysql_error());

		// DB Article
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
      $usrPasswd = $row['password'];
    }
		
		// Free resultset
		mysql_free_result($result);
		
		// Closing connection
		mysql_close($link);
		
		if ( empty($usrPasswd) ){
		  return false;
		}
	  else{
	    return $usrPasswd;
	  }
	}
	
}
?>
