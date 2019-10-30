/*
	File: myscripts.js
	Author: Jaspers
	Created by 2018-07-08
	Goal: 게시판 - 자바스크립트
	Description:
	2018-07-10 / Jasper / 한글 추가(나눔고딕)
	2018-08-01 / Jasper / 검색(키워드) 기능
*/

  WebFontConfig = {
    custom: {
        families: ['Nanum Gothic'],
        urls: ['http://fonts.googleapis.com/earlyaccess/nanumgothic.css']
    }
  };
  (function() {
    var wf = document.createElement('script');
    wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
      '://ajax.googleapis.com/ajax/libs/webfont/1.4.10/webfont.js';
    wf.type = 'text/javascript';
    wf.async = 'true';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(wf, s);
  })(); 



function keywordSearch(boardUrl){
  var keyword = "";
  keyword = document.getElementById("keyword").value;
  
  if ( keyword.length < 3){
    alert('최소 3글자 이상 입력');
  }
  else{
    location.href= boardUrl + "&keyword=" + keyword;
  }
}
