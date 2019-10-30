<?php
/*
	File: function.php
	Author: Jaspers
	Created by 2018-07-08
	Description:
	2018-07-10 / Jasper / BoardFn으로 View_Function, Board_Function 통합
	2018-07-11 / Jasper / printMode(), chooseMode() 외 다수 추가
	2018-07-12 / Jasper / view.php 버그, refresh() 출력으로 개선 기능 추가
	2018-07-12 / Jasper / Security 클래스 추가
	2018-07-12 / Jasper / XSS_Filter 탑재
*/


class BoardFn{

	private $pattern;
	private $url;
	
	public function __construct(){
	
    $this->pattern = '/^.*(?=^.{8,15}$)(?=.*\d)(?=.*[a-zA-Z])(?=.*[!@#$%^&+=]).*$/';
	}
	
	// 양식(Mode)
	public function printMode($mode){
	  
	  echo "\t\t\t<select name=\"mode\">\n";
	  	  
	  if ( !empty($mode) ){
	    $index = $this->convertTochooseMode($mode);
	    echo "\t\t\t\t<option value=\"" . $this->chooseMode($index) . "\">";
	    echo $this->titleMode($index);
	    echo "</option>";
    }
	  
	  for ($index = 1; $index <= $this->sizeMode(); $index++){
	    echo "\t\t\t\t<option value=\"" . $this->chooseMode($index) . "\">";
	    echo $this->titleMode($index);
	    echo "</option>";
	  }
	  
	  echo "\t\t\t</select>";
	}
	
  public function sizeMode(){
    return 2;
  }
  
  public function convertTochooseMode($keyword){
  
    $id = -1;
    for ( $index = 0; $index <= $this->sizeMode(); $index++){
      
      if ( strcmp( $this->chooseMode($index), $keyword) == 0 )
      {
        $id = $index;
        break;
      }
    }
    
    return $id; 
  }
	
	public function chooseMode($index){	
	  
    switch ($index){
    
      case 1:
        return "general";
      
      case 2:
        return "protected";
    }
	}
	
	public function titleMode($index){

    switch ($index){
    
      case 1:
        return "일반(General)";
      
      case 2:
        return "보호(Protected)";
    }	
	
	}
	
	public function chooseTitle($index){
		
		$title = '';
		
		switch ($index){
						
			case 1:
				$title = '번호(Num)';
				break;
				
			case 2:
				$title = '제목(Subject)';
				break;
				
			case 3:
				$title = '작성자(Author)';
				break;
				
			case 4:
				$title = '내용(Memo)';
				break;
				
			case 5:
				$title = '등록일자(Regidate)';
				break;
				
			case 6:
				$title = '조회수(Count)';
				break;
				
			case 7:
				$title = 'IP주소(IP Addr)';
				break;
				
			default:
				break;
			
		}
		
		return $title;
	}

  public function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }
  
  // 암호 패턴 출력(정규식-Regular Expression)
  public function passwordPattern(){
    return $this->pattern;
  }
  
  // 리프레시 버그
  public function refresh($value, $boardInfo){
    
    if ( empty ($value) && !empty($boardInfo) )
    { 
      $boardName = $boardInfo->getName();
      $page = $boardInfo->getPage();
      $article_id = $boardInfo->getArticle_ID();
      $refresh = 'y';
      
      header("Location: view.php?name=$boardName&page=$page&id=$article_id&r=$refresh");
    }
    
  }
  
  // 수행시간 측정
  public function getExecutionTime() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
  }
  
  // 페이지 번호
  public function getPageID( $id ){
  
    $page_id = 0;
    
  	if ( $id != 0 )
    	$page_id = $id;
    else
      $page_id = 1;
      
    return $page_id;
  }
  


}

class Security{
/*
 * XSS filter 
 *
 * This was built from numerous sources
 * (thanks all, sorry I didn't track to credit you)
 * 
 * It was tested against *most* exploits here: http://ha.ckers.org/xss.html
 * WARNING: Some weren't tested!!!
 * Those include the Actionscript and SSI samples, or any newer than Jan 2011
 *
 *
 * TO-DO: compare to SymphonyCMS filter:
 * https://github.com/symphonycms/xssfilter/blob/master/extension.driver.php
 * (Symphony's is probably faster than my hack)
 */
  public function xss_clean($data)
  {
    // Fix &entity\n;
    $data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
    $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
    $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
    $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

    // Remove any attribute starting with "on" or xmlns
    $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

    // Remove javascript: and vbscript: protocols
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

    // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

    // Remove namespaced elements (we do not need them)
    $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

    do
    {
      // Remove really unwanted tags
      $old_data = $data;
      $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
    }
    while ($old_data !== $data);

    // we are done...
    return $data;
    
  }

}
?>