<?php
@session_start();
sessioncheck(3);
switch($_REQUEST['option'])
{
	case "users": $users_sel = " class= 'sel' "; $url = '<a href="cpanel.php?option=users">Users</a>';
					 break;
	case "cities": $cities_sel = " class= 'sel' "; $url = '<a href="cpanel.php?option=cities">Cities</a>';
					 break;
					 
	case "theaters": $theaters_sel = " class= 'sel' "; $url = '<a href="cpanel.php?option=theaters">Theaters</a>';
					 break;
	case "movies": $movies_sel = " class= 'sel' "; $url = '<a href="cpanel.php?option=movies">Movies</a>';
					 break;
	case "status": $status_sel = " class= 'sel' "; $url = '<a href="cpanel.php?option=status">Movie Status</a>';
					break;
	case "theaterstatus": $theaterstatus_sel = " class= 'sel' "; $url = '<a href="cpanel.php?option=theaterstatus">Theater Status</a>';
					 break;
	case "comments": $comments_sel = " class= 'sel' "; $url = '<a href="cpanel.php?option=comments">comments</a>';
					 break;
	case "gallery": $photos_sel = " class= 'sel' "; $url = '<a href="cpanel.php?option=gallery">Galleries</a>';
					 break;
	case "audio": $audio_sel = " class= 'sel' "; $url = '<a href="cpanel.php?option=audio">Audio</a>';
					 break;
	case "articles": $articles_sel = " class= 'sel' "; $url = '<a href="cpanel.php?option=articles">Articles</a>';
	break;
	case "social": $social_sel = " class= 'sel' "; $url = '<a href="cpanel.php?option=social">Social</a>';
					 break;
	case "featured": $featured_sel = " class= 'sel' "; $url = '<a href="cpanel.php?option=featured">Featured</a>';
					 break;
	case "news": $news_sel = " class= 'sel' "; $url = '<a href="cpanel.php?option=news">News</a>';
					 break;
	
	default: $home_sel = " class= 'sel' "; $url = '<a href="cpanel.php?option=home">Home</a>'; break;
}


?>

<table  width="100%" cellpadding="0" cellspacing="0" height="35" border="0" style="border:2px solid #0199CB;border-bottom:8px solid #0199CB;">

	<tr>

	 <td align="center" valign="middle" class="suckertreemenu" ><ul id="treemenu1">
	 <li <?=$home_sel?> ><a href="cpanel.php?option=home">Home</a></li>
	 <li <?=$users_sel?> ><a href="cpanel.php?option=users">Users</a></li>
	 <li <?=$cities_sel?> ><a href="cpanel.php?option=cities">Cities</a></li>
	 <li <?=$theaters_sel?> ><a href="cpanel.php?option=theaters">Theaters</a></li>
	 <li <?=$movies_sel?> ><a href="cpanel.php?option=movies">Movies</a></li>
	 <li <?=$status_sel?> ><a href="cpanel.php?option=status">Movie Status</a></li>
	 <li <?=$theaterstatus_sel?> ><a href="cpanel.php?option=theaterstatus">Theater Status</a></li>
	 <li <?=$comments_sel?> ><a href="cpanel.php?option=comments">Comments</a></li>
	 <li <?=$photos_sel?> ><a href="cpanel.php?option=gallery">Galleries</a></li>
	 <li <?=$audio_sel?> ><a href="cpanel.php?option=audio">Audio</a></li>
	 <li <?=$articles_sel?> ><a href="cpanel.php?option=articles">Articles</a></li>
	 <li <?=$social_sel?> ><a href="cpanel.php?option=social">Social</a></li>
	 <li <?=$featured_sel?> ><a href="cpanel.php?option=featured">Featured</a></li>
	 <li <?=$news_sel?> ><a href="cpanel.php?option=news">News</a></li>
	 <li ><a href="cpanel.php?option=logout">Logout</a></li>
	</ul>
	</td>

	</tr>

</table>
