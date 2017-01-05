// JavaScript Document

var xmlhttp;
var loc;
function loadpro(div_id, url)
{
	loc = div_id;
	xmlhttp=null;
	if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp=new XMLHttpRequest();
	  }
	else
	  {// code for IE6, IE5
	  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	  }
		
	xmlhttp.onreadystatechange=state_Change;
	xmlhttp.open("GET",url,true);
	xmlhttp.send(null);
}

function state_Change()
{
	if (xmlhttp.readyState==4)
	  {// 4 = "loaded"
	  if (xmlhttp.status==200)
		{// 200 = "OK"
		document.getElementById(loc).innerHTML=xmlhttp.responseText;
		}
	  }
	  else
	  {
		  document.getElementById(loc).innerHTML="";
	  }
}

