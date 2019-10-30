<?php
/*
	File: model.php
	Author: Jaspers
	Created by 2018-07-08
	Goal: 게시판 Model 구현
	Description:
	2018-07-10 / Jasper / 게시판 기본 윤곽 설계
	2018-07-11 / Jasper / listComment 외 다수 작업
	2018-07-12 / Jasper / Board
	2018-08-01 / Jasper / 검색(키워드) 기능
*/
?>
<?php

  abstract class IBoard{
  
    abstract public function listContent($boardName, $keyWord, $pageID);
    abstract public function read($boardName, $articleID);
    abstract public function readArticle($boardName, $choose);
    abstract public function listComment($boardName, $pageID, $articleID);
    abstract public function updateComment($boardName, $comment);
    abstract public function removeComment($boardName, $comment);
    abstract public function isPasswordComment($boardName, $comment);
    abstract public function write($boardName, $article);
    abstract public function writeComment($boardName, $comment);
    abstract public function updateReplyCount($boardName, $articleID);
    abstract public function modify($boardName, $article);
    abstract public function remove($boardName, $article);
  	abstract public function updateCount($boardName, $article);
    abstract public function isArticle($boardName, $article);
    abstract public function isPassword($boardName, $article);
    
  }
  
  class Board{
    private $name;
    private $page;
    private $article_id;
    
    // getter and setter
    public function getName(){
      return $this->name;
    }
    
    public function getPage(){
      return $this->page;
    }
    
    public function getArticle_ID(){
      return $this->article_id;
    }
    
    public function setName($name){
      $this->name = $name;
    }
    
    public function setPage($page){
      $this->page = $page;
    }
    
    public function setArticle_ID($article_id){
      $this->article_id = $article_id;
    }
    
  }
  
  class Article{
  
    private $id;
    private $subject;
    private $author;
    private $password;
    private $memo;
    private $reply;
    private $mode;
    private $ip;
    private $regidate;
    private $count;
    
    public function __construct(){
    }
    
    public function __destruct(){
    }
    
    // Getter & Setter
    public function getID(){
      return $this->id;
    }
    
    public function getSubject(){
      return $this->subject;
    }
    
    public function getAuthor(){
      return $this->author;
    }
    
    public function getPassword(){
      return $this->password;
    }
    
    public function getMemo(){
      return $this->memo;
    }
    
    public function getReply(){
      return $this->reply;
    }
    
    public function getMode(){
      return $this->mode;
    }
    
    public function getIP(){
      return $this->ip;
    }
    
    public function getRegidate(){
      return $this->regidate;
    }
    
    public function getCount(){
      return $this->count;
    }
  
    public function setID($id){
      $this->id = $id;
    }
    
    public function setSubject($subject){
      $this->subject = $subject;
    }
    
    public function setAuthor($author){
      $this->author = $author;
    }
    
    public function setPassword($password){
      $this->password = $password;
    }
    
    public function setMemo($memo){
      $this->memo = $memo;
    }
    
    public function setReply($reply){
      $this->reply = $reply;
    }
    
    public function setMode($mode){
      $this->mode = $mode;
    }
    
    public function setIP($ip){
      $this->ip = $ip;
    }
    
    public function setRegidate($regidate){
      $this->regidate = $regidate;
    }
    
    public function setCount($count){
      $this->count = $count;
    }
    
  }
  
  class Comment{
  
    private $id;
    private $article_id;
    private $memo;
    private $author;
    private $password;
    private $ip;
    private $regidate;
    
    public function __construct(){
    }
    
    public function __destruct(){
    }
    
    // Getter & Setter
    public function getID(){
      return $this->id;
    }

    public function getArticle_ID(){
      return $this->article_id;
    }
    
    public function getMemo(){
      return $this->memo;
    }
    
    public function getAuthor(){
      return $this->author;
    }
    
    public function getPassword(){
      return $this->password;
    }
    
    public function getIP(){
      return $this->ip;
    }
    
    public function getRegidate(){
      return $this->regidate;
    }
  
    public function setID($id){
      $this->id = $id;
    }
    
    public function setArticle_ID($article_id){
      $this->article_id = $article_id;
    }
    
    public function setMemo($memo){
      $this->memo = $memo;
    }
  
    public function setAuthor($author){
      $this->author = $author;
    }
    
    public function setPassword($password){
      $this->password = $password;
    }
    
    public function setIP($ip){
      $this->ip = $ip;
    }
    
    public function setRegidate($regidate){
      $this->regidate = $regidate;
    }  
  }
  
  class RSSInfo{
  
    private $title;
    private $link;
    private $description;
    private $pubDate;
    
    // getter & setter
    public function getTitle(){
      return $this->title;
    }
    
    public function getLink(){
      return $this->link;
    }
    
    public function getDescription(){
      return $this->description;
    }
    
    public function getPubDate(){
      return $this->pubDate;
    }
    
    public function setTitle($title){
      $this->title = $title;
    }
    
    public function setLink($link){
      $this->link = $link;
    }
    
    public function setDescription($description){
      $this->description = $description;
    }
    
    public function setPubDate($pubDate){
      $this->pubDate = $pubDate;
    }
    
  }
  
  class RSS{
  
    private $author;
    private $category;
    private $title;
    private $link;
    private $guid;
    private $pubDate;
    private $description;
    
    // getter & setter
    public function getAuthor(){
      return $this->author;
    }
    
    public function getCategory(){
      return $this->category;
    }
    
    public function getTitle(){
      return $this->title;
    }
    
    public function getLink(){
      return $this->link;
    }
    
    public function getGuid(){
      return $this->guid;
    }
    
    public function getPubDate(){
      return $this->pubDate;
    }
    
    public function getDescription(){
      return $this->description;
    }
    
    public function setAuthor($author){
      $this->author = $author;
    }
    
    public function setCategory($category){
      $this->category = $category;
    }
    
    public function setTitle($title){
      $this->title = $title;
    }
    
    public function setLink($link){
      $this->link = $link;
    }
    
    public function setGuid($guid){
      $this->guid = $guid;
    }
    
    public function setPubDate($pubDate){
      $this->pubDate = $pubDate;
    }
    
    public function setDescription($description){
      $this->description = $description;
    }
        
  }

?>