<?php
/*	--------------------------------------------
 
 			[HTAccess Manager - v0.1]
 				Compact version

	--------------------------------------------

	MIT License

	Copyright (c) 2019 Mariano Sciacco

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.

	--------------------------------------------

 	Checkout https://github.com/Maxelweb/HTAM for more information
 
 	Developed by Mariano Sciacco
 	[marianosciacco.it]
   --------------------------------------------

*/

// ************* DO NOT CHANGE ANYTHING AFTER THIS LINE  ****************

// ------------ Globals ------------

ini_set('display_errors', 1);
ini_set("allow_url_fopen", 1);
error_reporting(E_ERROR);

include_once('htam-class.php');

define("VERSION", "0.1");
define("REPO", "https://github.com/Maxelweb/HTAM");
$s = isset($_GET['s']) ? $_GET['s'] : "";
$a = isset($_GET['a']) && isset($me) && $me->isAdmin() ? $_GET['a'] : 0;
if ($s == 'users' && isset($_GET['a']) && $_GET['a'] == 1) $a = 1;//add exception : User are allowed to change his own password



// ---------------------------------

?>
<!DOCTYPE html>
<html>
<head>
	<title>HTAccess Manager</title>
	<meta content='width=device-width, initial-scale=1' name='viewport'>
	<meta name="description" content="HTAccess Manager, manage protected directory with user access">
  	<meta name="keywords" content="HTAM,htaccess,manager,htpasswd,maxelweb">
  	<meta name="author" content="Mariano Sciacco">
  	<style type="text/css">
  		body{background-color:#444;color:#333;font-family:Verdana,sans-serif;font-size:10.5pt;line-height:150%}h1,h2,h3,h4{color:#333}h1{font-size:140%;color:#4c5f7d}article,footer,header,nav{max-width:1000px;width:auto;margin:0 auto;padding:10px}header{background-color:#ccc;border-bottom:1px solid #aaa;text-align:center;border-top-right-radius:3px;border-top-left-radius:3px}nav{background-color:#e6e8ed;border-bottom:1px solid #999}nav span{margin-right:5px;display:inline-block}a{color:#3869b7}a:hover{color:#7d38b7}footer a{color:#444}footer a:hover{color:#ccc}article{background-color:#fafafa;min-height:150px}footer{background-color:#999;color:#fff;text-align:center;border-bottom-left-radius:3px;border-bottom-right-radius:3px;font-size:80%}span.good{color:green}span.bad{color:#8b0000}span.box{display:block;border-radius:3px;padding:10px;background-color:#e6e8ed}span.box.good{background-color:#dce5da}span.box.bad{background-color:#e5dada}ul.dir{list-style-type:none}ul.dir li{margin-left:-15px}input{margin:5px}.responsive{overflow-x:auto}table{width:100%}td{padding:3px}tr{background-color:#e9e9e9}tr:nth-child(odd){background-color:#fff}#checker{font-size:80%}.tdcenter td{text-align:center}td{background-color:#e6e8ed}
  	</style>
</head>
<body>

<header>
	<h1>HTAccess Manager</h1>
	<small><strong>Current directory:</strong> <?=$htam->dir();?> <?=$htam->icon();?></small>
</header>

<nav>
	<span>&#x1F3E0; <a href="?">Dashboard</a></span>
	<span>&#x1F4C1; <a href="?s=dir">Manage directory</a></span>
	<span>&#x1F465; <a href="?s=users">Manage users</a></span>
	<span>&#x2615; <a href="?s=updates">Updates</a></span>
</nav>

<article>
<?php
	if($s == "dir")
	{
?>
<section>
	<h3>&#x1F4C1; Directory Management</h3>

<?php

	if($htam->hasHtaccess() && $htam->hasHtpasswd() && !$htam->checkPermissions())
	{
		echo bad("The <code>.htaccess</code> or <code>.htpasswd</code> files are not readable or writable. Set the correct permissions (664) from FTP or <a href='?s=dir&a=6'>attempt to change permissions</a>. ");
	}

	if($a == 1)
	{
		if(!$htam->isProtected())
		{
			if(!$htam->hasHtaccess() || !$htam->hasHtpasswd() || $htam->countAdmin() == 0)
			{
				echo bad("Before enabling protection, you must <a href='?s=dir&a=3'>create htaccess and htpasswd</a> and add at least <strong>one administrator</strong> in <a href='?s=users'>Manage users</a>.");
			}
			else
			{
				if($htam->addProtection())
					echo good("Directory protection activated.");
				else
					echo bad("Error: unable to add directory protection. Check file permissions.");
			}
		}
		else
		{	
			if($htam->removeProtection())
				echo good("Directory protection removed.");
			else
				echo bad("Error: unable to remove directory protection. Check file permissions.");
		}
	}
	elseif($a == 3)
	{
		if($htam->createHtfiles())
			echo good("Files created successfully.");
		else
			echo bad("Error: unable to create one or more files to initialize the script. Change manually write permissions for this folder.");
	}
	elseif($a == 4)
	{
		if($htam->isProtected())
			echo bad("You must disable protection to delete Htaccess.");
		elseif($htam->deleteHtaccess())
			echo good("Htaccess deleted successfully.");
		else
			echo bad("Error: unable to delete Htaccess.");
	}
	elseif($a == 5)
	{
		if($htam->isProtected())
			echo bad("You must disable protection to delete Htpasswd.");
		elseif($htam->deleteHtpasswd())
			echo good("Htpasswd deleted successfully.");
		else
			echo bad("Error: unable to delete Htpasswd.");
	}
	elseif($a == 6)
	{
		if($htam->setPermissions())
			echo good("File permissions (664) applied successfully. Now the script is able to work properly.");
		else
			echo bad("Error: unable to set permissions (664). You must do it manually from FTP.");
	}

?>
</section>

<section>
	<h3>Informations</h3>

	<ul>
		<li><strong>Directory protection: </strong> <?=$htam->isProtected() ? good("Active",1) : bad("Disabled",1) ;?>
			<?php if (isset($me) && $me->isAdmin()):?>[<a href="?s=dir&a=1">change</a>]<?php endif;?>
		</li>
		<li><strong>htaccess: </strong> <?=$htam->hasHtaccess() ? good("Yes",1) : bad("No",1) ;?></li>
		<li><strong>htpasswd: </strong> <?=$htam->hasHtpasswd() ? good("Yes",1) : bad("No",1) ;?></li>
		<li><strong>Area name: </strong> <?=$htam->getInfo()[0];?>
		<li><strong>htaccess location: </strong> <code><?=$htam->hta();?></code>
		<li><strong>htpasswd location: </strong> <code><?=$htam->htp();?></code>	
	</ul>
</section>

<?php if (isset($me) && $me->isAdmin()):?>
<section>
	<h3>Options</h3>

	<ul>
		<li>&#x1F511; <a href="?s=dir&a=1">Toggle directory protection</a></li>	
		<li>&#x1F4C1; <a href="?s=dir&a=2">Show protected sub-directories and files</a></li>
	</ul>

	<ul>
		<?php if(!$htam->hasHtaccess() || !$htam->hasHtpasswd()){ ?>
			<li>&#x2795; <a href="?s=dir&a=3">Create htaccess and/or htpasswd</a> (if not present)</li>
		<?php } if($htam->hasHtaccess()) { ?>
		<li>&#x2716; <a href="?s=dir&a=4" onclick="return confirm('By deleting the .htaccess you will delete all the existing configurations. Are you sure to continue?');">Delete .htaccess file</a> (<b>note:</b> delete all existent config)</li>
		<?php } if($htam->hasHtpasswd()) { ?>
		<li>&#x2716; <a href="?s=dir&a=5" onclick="return confirm('By deleting the .htpasswd you will delete all the current users. Are you sure to continue?');">Delete .htpasswd file</a> (<b>note:</b> delete all users)</li>
		<?php } ?>
	</ul>

<?php

	if($a == 2)
	{
		echo "<h4>Protected files and directories <small>[<a href='?s=dir&a=2'>reload</a> | <a href='?s=dir'>close</a></small>]</h4>";
		$subs = $htam->getSubDirFiles();
		if(empty($subs))
			echo "None";
		else
		{
			echo "<ul class='dir'>";
			foreach ($subs as $sub => $name) 
			{
				if(is_dir($name))
					echo "<li>&#x1F538; <a href='$name'>$name/</a></li>";
				else
					echo "<li>&#x1F539; <a href='$name'>$name</a></li>";
			}
			echo "</ul>";
		}
	}

?>
</section>
<?php endif;?>

<?php
	}
	elseif($s == "users")
	{
?>
<section>
	<h3>&#x1F465; User management</h3>

<?php

	$users = $htam->getUsers();
	$edit = isset($_GET['edit']) && isset($me) && $me->isAdmin() ? $_GET['edit'] : "";

	if($a == 1)
	{
		$un = $_POST['uname'];
		$up = $_POST['upass'];
		$urp = $_POST['urpass'];

		$find = $htam->findUser($un);
		$user = $find > -1 ? $users[$find] : new HUser();
		
		if (isset($me) && $me == $user || $me->isadmin())
		{
			
			if(!$user->changeUsername($un) && $me->isadmin())
			{
				echo bad("The username must contain only alphanumeric characters, dash, underscore and spaces (from 3 to 15 characters long).");
			}
			elseif(!$user->changePassword($up))
			{
				echo bad("The password must be at least 4 characters and maximum 32 characters long.");
			}
			elseif($up != $urp)
			{
				echo bad("The password repeated is different.");
			}
			else
			{
				if($find > -1)
					$users[$find] = $user;
				else
					array_push($users, $user);
				

				if($htam->changeUsers($users))
					echo good("User <strong>$un</strong> has been added/edited successfully. <br> 
						<code>Username: $un - Password: $up</code>");
				else
					echo bad("Error: unable to update users file. Check permissions.");
			}
		}
	}
	elseif($a == 2 && !empty($edit))
	{
		$find = $htam->findUser($edit);
		$user = $find > -1 ? $users[$find] : "";
		if(empty($user))
			echo bad("The user selected has not been found.");
		elseif(isset($me) && $user->name() == $me->name() && $htam->isProtected())
			echo bad("You can't delete yourself while directory protection is active.");
		else
		{
			unset($users[$find]);
			if($htam->changeUsers($users))
				echo good("The user has been removed permanently.");
			else
				echo bad("Error: unable to update users file. Check permissions.");
			
		}
	}
	elseif($a == 3 && !empty($edit))
	{
		$find = $htam->findUser($edit);
		$user = $find > -1 ? $users[$find] : "";
		if(empty($user))
			echo bad("The user selected has not been found.");
		elseif( ($htam->countAdmin() == 1 || (isset($me) && $user->name() == $me->name()) )
					&& $user->isAdmin() && $htam->isProtected())
			echo bad("You can't remove the last administrator or yourself as administrator while protection is enabled.");
		else
		{
			$users[$find]->toggleAdmin($user->isAdmin() ? 0 : 1); 
			if($htam->changeUsers($users))
				echo good("The role of the user <strong>{$user->name()}</strong> has been changed.");
			else
				echo bad("Error: unable to update users file. Check permissions.");
			
		}
	}

?>
</section>

<script>
	var names = [<?php foreach($users as $u) echo "'".$u->name()."',";?>];
	var me = "<?=isset($me)?$me->name():'';?>";
	function checkUsername()
	{
		var i = document.getElementById("ncheck");
		var j = document.getElementById("checker");

		if(i.value === "" || i.value.length < 3 || i.value === me)
			j.innerHTML = "";
		else if(names.indexOf(i.value) > -1)
			j.innerHTML = "<span class='bad'>&#x2716; Username in use! The password will be overrided.</span>"; 
		else
			j.innerHTML = "<span class='good'>&#x2714; Username not used!</span>";
	}

	function setName(name)
	{
		document.getElementById("userform").style.display = 'block';
		document.getElementById("ncheck").value = name;
	}
</script>

<section id="userform" style="display:<?=$me->isAdmin()?'block':'none';?>">
	<h3>Add / edit user</h3>
	<p>If the username is already in use, the password will be overrided with the new one.</p>
	<form action="?s=users&a=1" method="POST">
		<input type="text" name="uname" id="ncheck" onkeyup="checkUsername();" placeholder="Username" value="<?=$a==1?$un:'';?>" <?=$me->isadmin()?'':'disabled';?>>
		<span id="checker"></span><br>
		<input type="password" name="upass" placeholder="Password">
		<input type="password" name="urpass" placeholder="Repeat Password"><br>
		<input type="submit">
	</form>
</section>

<section>

	<h3>Users list</h3>

	<?php
		if(empty($users))
			echo bad("No user found.",1);
		else
		{
			echo "<span class='box'><strong>Total users:</strong> ".count($users).", <strong>Administrators:</strong> ".$htam->countAdmin()." </span>";
			echo "<div class='responsive tdcenter'><table>";
			foreach ($users as $user) 
			{
				echo "<tr>";
				echo "<td><strong>{$user->name()}</strong></td>";
				echo "<td>".($user->isAdmin() ? bad("&#x1F920; administrator",1) : good("&#x1F603; user",1))."</td>";
				echo "<td><span class='box'>" . ((isset($me) && $me->isadmin() || $me == $user) ? "&#x270F; <a href='#' onclick='setName(\"{$user->name()}\"); checkUsername();'>edit password</a>" : "") . "</span></td>"; 
				if(!$user->isAdmin())
					echo "<td><span class='box'>" . ((isset($me) && $me->isadmin()) ? "&#x2B06; <a href='?s=users&edit={$user->name()}&a=3'>set admin</a>" : "") . "</span></td>";
				else
					echo "<td><span class='box'>" . ((isset($me) && $me->isadmin()) ? "&#x2B07; <a href='?s=users&edit={$user->name()}&a=3'>set user</a>" : "") . "</span></td>";
				echo "<td><span class='box'>" . ((isset($me) && $me->isadmin()) ? "&#x274C; <a href='?s=users&a=2&edit={$user->name()}'>delete</a>" : "") . "</span></td>";
				echo "</tr>";
			}
			echo "</table></div>";
		}

	?>
</section>

<?php
	}
	elseif($s == "updates")
	{
?>
<section>
	<h3>&#x2615; Updates</h3>
	<?php 
		$hup = new HUpdates();

		if($hup->latestVersion() == "N/A")
			echo bad("Unable to check for new updates. Try later or checkout the <a href='".REPO."'>official repository</a>.");
		elseif($hup->currentVersion() != $hup->latestVersion()) 
			echo bad("Your version is <b>not</b> up to date.");
		else
			echo good("Your version is up to date.");
	?>

	<h3>Updates checker</h3>
	<ul>
		<li><strong>Current version:</strong> <?=$hup->currentVersion();?></li>
		<li><strong>Latest version:</strong> <?=$hup->latestVersion();?></li>
		<li><strong>Github repository for updates:</strong> <a href="<?=REPO;?>"><?=REPO;?></a></li>
	</ul>

	<?php
		if(!empty($hup->info()))
		{
			echo "<div class='responsive'><table>";
			foreach ($hup->info() as $key => $value) 
			{
				echo "<tr>
						<td><strong>$key</strong></td>
						<td>$value</td>
					  </tr>";
			}
			echo "</table></div>";
		}
	?>

</section>

<?php
	}
	else
	{
?>


<section>
	<h3>&#x1F3E0; Dashboard</h3>

	<p>Welcome to the <em>HTAccess Manager</em>. From here, you can manage users and directory protection.</p>

	<ul>
		<li><strong>Directory protection: </strong> <?=$htam->isProtected() ? good("Active",1) : bad("Disabled",1) ;?>
			<?php if (isset($me) && $me->isAdmin()):?>[<a href="?s=dir&a=1">change</a>]<?php endif;?>
		</li>
		<li><strong>Current Login Username: </strong> <em><?=isset($me) ? $me->name() : "None";?></em></li>
		<li><strong>Total users: </strong> <?=!$htam->getUsers() ? 0 : count($htam->getUsers());?></li>
	</ul>
</section>

<?php
	}
?>
</article>

<footer>
	&#x2615; <a href="<?=REPO;?>">HTAM v<?=VERSION;?></a> &raquo; developed by <a href='https://marianosciacco.it' target="_blank">Maxelweb</a>
</footer>

</body>
</html>
