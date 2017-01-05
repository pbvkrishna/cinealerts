// JavaScript Document
var popwindow_inc=0;
var mypopups=new Array();
var executed=0;
var txt="";
function ajaxFunction(file, targetwindow, formobject, location, tabfocus)
{
//if(typeof(tabfocus)=="undefined") tabfocus=0;
executed=0;
if(!tabfocus)
{
	tabfocus = 0;
}
var xmlHttp;

try
  {
  // Firefox, Opera 8.0+, Safari
  xmlHttp=new XMLHttpRequest();
  }
catch (e)
  {
  // Internet Explorer
  try
    {
    xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    }
  catch (e)
    {
    try
      {
      xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
      }
    catch (e)
      {
          try
		  {
		  xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		catch (e)
		  {
		  alert("Your browser does not support AJAX!");
		  
		  return false;
		  }
      }
    }
  }
  xmlHttp.open("POST",file,true);
	xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	if(formobject)
	{
		
		formvalues=get(formobject);
		xmlHttp.send(formvalues);
		
	}
	else
	{
		 
		xmlHttp.send(null);
	}

  
  xmlHttp.onreadystatechange=function()
    {

    if(xmlHttp.readyState==4)
      {
	
		  document.getElementById(location).innerHTML=xmlHttp.responseText;
		  if(formobject)
		  {
			  setTimeout("setfocus('"+tabfocus+"')",1000);
		  }
		  
		 /* if(formobject)
		  {
	
		  if(typeof(tabfocus)=="undefined")
		  {
			setTimeout("document.forms[0].elements["+0+"].focus()",600);
	
		  }
		  }*/
	  }
	  else
	  {
		  document.getElementById(location).innerHTML="<center><h5><style='font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold;'>Loading... </span><img src='images/ajax-loader.gif'></h5></center>";	
	  }
     }
 }


function setfocus(ti)
{



	if(!document.forms[0].elements[ti] || document.forms[0].elements[ti].type == "button")
   {
	  ti=0;
   }



for(i=0;i<document.forms[0].elements.length;i++)
 {
	 
	 
	 

    if(document.forms[0].elements[ti].readOnly == true || document.forms[0].elements[ti].disabled == true)
   {

 	ti++;

   }
  
  
	   
	  if(!document.forms[0].elements[ti]  || document.forms[0].elements[ti].type == "button")
			  ti=0;
		  
   }




 	if(document.forms[0].elements[ti])
	{
	 document.forms[0].elements[ti].focus();
	}
   /*if(document.forms[0] && typeof(ti)!="undefined")
	  {
          document.forms[0].elements[ti].focus();
		
	  }
	  else if(!(document.forms[0] && typeof(ti)!="undefined"))
	  {
		  	  document.forms[0].elements[0].focus();
	  }*/
   
   
}





function windowclose()
{
  
  if(popwindow_inc>0)
  {
	  document.getElementById('popupscreen').innerHTML =  mypopups[popwindow_inc-1];
	  popwindow_inc--;
  }
  else
  {
	 document.getElementById('popupscreen').innerHTML="";
	 document.getElementById("wholecontent").style.zIndex=3;
	 document.getElementById("popupscreen").style.zIndex=-2;
	 document.getElementById("blocker").style.zIndex=-1;
  }
}

function get(fobj) {
	var flag=0;
    var str = "";

   var valueArr = null;

   var val = "";

   var cmd = "";

   for(var i = 0;i < fobj.elements.length;i++)

   {

       switch(fobj.elements[i].type)

       {

           case "text":
                  
                str += fobj.elements[i].name +

                 "=" + escape(fobj.elements[i].value) + "&";
    
                 
                 break;
			 case "hidden":
                  
                str += fobj.elements[i].name +

                 "=" + escape(fobj.elements[i].value) + "&";
    
                 
                 break;

           case "select-one":

                str += fobj.elements[i].name +

                "=" + fobj.elements[i].options[fobj.elements[i].selectedIndex].value + "&";

                break;
			case "textarea":

				str += fobj.elements[i].name +

                "=" + fobj.elements[i].value + "&";

				break;



			case "checkbox":

				if(fobj.elements[i].checked)
				{
				str += fobj.elements[i].name +

                "=" + fobj.elements[i].value + "&";
				}

				break;

			case "radio":

				if(fobj.elements[i].checked)
				{
				str += fobj.elements[i].name +

                "=" + fobj.elements[i].value + "&";
				}


				break;

			    

       }

   }

   str = str.substr(0,(str.length - 1));

   return str;

   }
   
   function confirmmsg()
   {
		if(document.getElementById('ctxt').value!='true' && document.getElementById('ctxt').value!='false' && document.getElementById('ctxt').value!='' )
		{
			txt = document.getElementById('ctxt').value;
			document.getElementById('ctxt').value = 'false';
				if(txt != 'no alert'){
					if(!confirm(txt))
					{
					cret = document.getElementById('cret').value;
					var formele = cret.split(','); 
					for(var i=0; i<formele.length;i++)
					{
						document.getElementById(formele[i]).value="";
					}
					document.getElementById('ctxt').value="";
					}
					else
					{
						document.getElementById('ctxt').value = 'true';
					}
				}
		}
   }
   
   function alertmsg()
   {
	   
   }