// JavaScript Document
function passwordstrength(password)
{
        var desc = new Array();
        desc[0] = "Very Weak";
        desc[1] = "Weak";
        desc[2] = "Better";
        desc[3] = "Medium";
        desc[4] = "Strong";
        desc[5] = "Strongest";
        var score   = 0;
        //if password bigger than 6 give 1 point
        if (password.length > 6) score++;
        //if password has both lower and uppercase characters give 1 point      
        if ( ( password.match(/[a-z]/) ) && ( password.match(/[A-Z]/) ) ) score++;
        //if password has at least one number give 1 point
        if (password.match(/\d+/)) score++;
        //if password has at least one special caracther give 1 point
        if ( password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) ) score++;
        //if password bigger than 12 give another 1 point
        if (password.length > 12) score++;
         document.getElementById("passworddescription").innerHTML = desc[score];
         document.getElementById("passwordstrength").className = "strength" + score;
}

function passwordmatch(password, rpassword)
{
	if(password != rpassword)
	{
         document.getElementById("pmatch").innerHTML = "Doesn't match";
	}
	else
	{
         document.getElementById("pmatch").innerHTML = "";
	}
}

function validate()
{
	if(document.userform.username.value.length < 6 || document.userform.username.value.length > 18)
	{
		alert("Username should between 6 to 18 characters");
		document.userform.username.focus();
		return false;
	}
	else if(document.userform.username.value.match(/[ ,!,@,#,$,%,^,&,*,?,_,~,-,(,)]/))
	{
		alert("Special characters and spaces not allowed in username");
		document.userform.username.focus();
		return false;
	}
	else if(document.userform.password.value.length <6 || document.userform.password.value.length > 18)
	{
		alert("Password should between 6 to 18 in length");
		document.userform.password.focus();
		return false;
	}
	else if(document.userform.rpassword.value != document.userform.password.value)
	{
		alert("Password's did not matched");
		document.userform.password.focus();
		return false;
	}
	else if(document.userform.name.value.length < 3 || document.userform.name.value.length > 20)
	{
		alert("Name should between 3 to 20 characters");
		document.userform.name.focus();
		return false;
	}
	else if(document.userform.name.value.match(/[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/))
	{
		alert("Special characters not allowed in Name");
		document.userform.name.focus();
		return false;
	}
	else if(validateemail() == false)
	{
		return false;
	}
	else if(isNaN(document.userform.mobile.value))
	{
		alert("Invalid Mobile Number");
		document.userform.mobile.focus();
		return false;
	}
	else if(document.userform.mobile.value.length != 10)
	{
		alert("Mobile number should be 10 digits");
		document.userform.mobile.focus();
		return false;
	}
	else
	{
		return true;
	}
}

function echeck(str) 
{
		var at="@"
		var dot="."
		var lat=str.indexOf(at)
		var lstr=str.length
		var ldot=str.indexOf(dot)
		if (str.indexOf(at)==-1)
		{
		   alert("Invalid E-mail ID")
		   return false
		}

		if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr)
		{
		   alert("Invalid E-mail ID")
		   return false
		}

		if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr)
		{
		    alert("Invalid E-mail ID")
		    return false
		}

		 if (str.indexOf(at,(lat+1))!=-1)
		 {
		    alert("Invalid E-mail ID")
		    return false
		 }

		 if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot)
		 {
		    alert("Invalid E-mail ID")
		    return false
		 }

		 if (str.indexOf(dot,(lat+2))==-1)
		 {
		    alert("Invalid E-mail ID")
		    return false
		 }
		
		 if (str.indexOf(" ")!=-1){
		    alert("Invalid E-mail ID")
		    return false
		 }

 		 return true					
}



function validateemail()
{
	var emailID=document.userform.email
	
	if ((emailID.value==null)||(emailID.value==""))
	{
		alert("Please Enter your Email ID")
		emailID.focus()
		return false
	}
	if (echeck(emailID.value)==false)
	{
		emailID.value=""
		emailID.focus()
		return false
	}
	return true
 }
