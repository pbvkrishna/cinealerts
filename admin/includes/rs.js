// JavaScript Document
function rs_check(flag,formvalues)
{
	 if(flag=="1")
	 {
	 alert('Select proper date')
 	 document.getElementById('dto').focus();
	 }
	 else
	 ajaxFunction('rs.php?option=showdetails','new',formvalues,'contentblock');
}