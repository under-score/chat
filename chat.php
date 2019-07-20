<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<HTML xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">

<head>
<META HTTP-EQUIV="Content-type" CONTENT="text/html; charset=utf-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">

<style>
@font-face { font-family: 'century_gothic'; src: url('century_gothic.ttf') format('truetype'); font-weight: normal; font-style: normal;}
html, body {
  background: grey url("geometry.png") !important;
  color: black !important;
}
img {
	position: relative !important;
}
.spb {
    background-color: #f8f8f8;
    border: 1px solid #c8c8c8;
    border-radius: 5px;
    min-height: 50px;
    font-size: 1.3em;
    width: 50%;
    margin: 10px;
    text-align:left;
    padding: 10px;
    position: relative;
    color:black;
}
.spb .arrow {
    border-style: solid;
    position: absolute;
}
.spb img:not(.initial) {
   max-width: 80%;
   height: auto;
}
.bottom {
    border-color: #c8c8c8 transparent transparent transparent;
    border-width: 15px 15px 0px 15px;
    bottom: -15px;
}
.bottom:after {
    border-color: #f8f8f8 transparent transparent transparent;
    border-style: solid;
    border-width: 14px 14px 0px 14px;
    bottom: 1px;
    content: "";
    position: absolute;
    left: -14px;    
}
.small {
  font-size: .7em;
  font-style:italic;
}
.initial {
  float:left;
  margin-right:30px;
}
#fix {
  position: absolute;
  font-size: 1em;
  margin-top: 20px;
  margin-left: 20px;
  z-index: 2;
}
</style>

<!--link rel="stylesheet" type="text/css" href="projector.css" /-->

<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="initial.min.js"></script>

<script>
var i=0;
var k=0;

function worker2() {

  $.ajax({
  	cache: false,
  	url: 'server.php?k='+k,  
  	success: function(data4) {

      var IS_JSON = true;
      try {
        var json = $.parseJSON(data4);
      }
      catch(err) {
        IS_JSON = false;
      }

          console.log(json);
          
      if (IS_JSON && json!==null) {
        for (var ii = 0, len = json.length; ii < len; ++ii) {
          var line1 = json[ii];
          k=line1.ID; 
  	      if (k>0) { 
            m='<img id="a' + k +'" class="initial"/><div class="small">'  + k + ' ' + line1.NICKNAME + '</div>' + line1.MESSAGE + line1.IMG;
  		    $('#' + i).after('<div class="spb" style="display:none;left:' + line1.MESSAGEPOSITION + '%" id="' + k +'" ><div class="arrow bottom right"></div>' + m + '</div>');
            $('#a'+k).initial({
              name: line1.NICKNAME,
              charCount: 2,
              textColor: '#ffffff',
              seed: 0,
              height: 50,
              width: 50,
              fontSize: 24,
              fontFamily: 'century_gothic,Relative Book,Abel',
              radius: 0
  		    });	
  		    $('#' + k).fadeIn();
  		    $('#main').animate({scrollTop: $('#main').scrollTop() + ($('#' + k).offset().top - $('#main').offset().top)});
            i=k;
          }
        }	
      }
      
  	}
  });
  
}

setInterval(worker2,1000);
</script>
</head>

<body>

<div id="main">
<div id="fix">
Branding
</div>
<div id="0"></div>
</div>

</body>
</html>
 