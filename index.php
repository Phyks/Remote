<?php
	 // @nom: Index
	 // @auteurs: Phyks (webmaster@phyks.me) and CCC (contact@cphyc.me)
	 // @description:  Main page
	 // See humans.txt file for more info
	 
	 // Copyright (c) 2013 Phyks and CCC
	 // This software is licensed under the zlib/libpng License.

	session_start();
	
	if(!empty($_GET['quit'])) //If we want to go back to the presentation choice form
	{
		session_destroy();// Destroy the session
		header('location: index.php'); //Reload the page
	}
	
	if(!empty($_POST['window'])) //If we chose a specific window, let's save it !
	{
		$_SESSION['window'] = (int) $_POST['window'];
		header('location: index.php');
	}

	//------ Browser specific code ---------
	//Some code to detec iPhone and put adequate tags
	$browser = '';
	if (strpos($_SERVER['HTTP_USER_AGENT'],"iPhone")) //Detect whether browser is Safari Mobile or not (to add correct tags)
		$browser = 'iphone';
	//--------------------------------------
	
	//------ Orders ---------
	//If an order is given (left or right), we execute it and then redirect to this page without arguments in $_GET
	if(!empty($_GET['left']))
	{
		shell_exec('./remote.sh Left '.$_SESSION['window']);
		usleep(2000000);
		header('location:index.php');
		exit;
	}
	elseif(!empty($_GET['right']))
	{
		shell_exec('./remote.sh Right '.$_SESSION['window']);
		usleep(2000000);
		header('location:index.php');
		exit;
	}
	//--------------------------------------
	
	
	////----------- Get the background image ----------------
	if(is_file("tmp/tmp.png")) //Delete the old background file if needed
	{
		unlink("tmp/tmp.png");
	}
	
	//First, we get the ids of all the windows with a title containing ".pdf" (e.g. Evince used to read a pdf document) OR of the window we specified by the $_SESSION var
	if(!empty($_SESSION['window']))
	{
		$ids = (int) $_SESSION['window'];
		
		if(!empty($_POST['command'])) //If custom command passed
		{
			$command = str_replace('$window', $ids, $_POST['command']);
			
			if(strpos($command, '--verbose') !== FALSE)
			{
				$command = substr($command, 0, strpos($command, '--verbose'));
				
				$output = shell_exec($command);
			}
			else
			{
				shell_exec($command);
				header('location: index.php');
				exit;
			}
		}
	}
	else
	{
		$ids = shell_exec("export DISPLAY=:0 && xdotool search --name --desktop 0 \"\"");
	}
	
	$ids = array_filter(explode("\n", $ids)); //Get the ids in array form and delete empty entries
	
	if(count($ids) == 1) //If there's only one window which is ok -> easy :)
	{
		$ids = (int) $ids[0]; //We force ids to be int
	
		shell_exec("export DISPLAY=:0 && import -window ".$ids." tmp/tmp.png"); //We take a screenshot for the background

		$titre = shell_exec("export DISPLAY=:0 && xdotool getwindowname ".$ids); //Get the whole name to forge the title of this page
	}
	elseif(count($ids) > 1) //If there are more than one window with "pdf" in the title, display a form to choose which one you want
	{
		$form_content = '';
		$i = 1;
		$checked = '';

		foreach($ids as $id)
		{
			$name = shell_exec("export DISPLAY=:0 && xdotool getwindowname ".$id);
			
			if($i == 1)
			{
				$checked = 'checked';
				$i = 2;
			}

			$form_content .= "<input type='radio' name='window' value='".$id."' id='".$id."' ".$checked."/><label for='".$id."'>".$name."</label><br/>"; //Fill a variable containing the radio fields
			$checked = '';
		}
	}
	
	//If $ids == NULL -> no window ok -> Just display an error and put a correct title
?>
<!DOCTYPE html>
	<html>
		<head>
		    <meta charset='utf-8' />
		    <title><?php 
		    		if(!empty($titre)) echo $titre; elseif(!empty($form_content)) echo "Remote Presentation"; else echo "Error - Remote Presentation"; ?></title>
		    <script type="text/javascript" src="jquery.js"></script>
			<script type="text/javascript" src="swipe.js"></script>
			<script type="text/javascript">
				function reset_commandline()
				{
					var commandline = document.getElementById('commandline').elements['command'];
	
					commandline.onclick = function ()
					{
						if(commandline.value == commandline.defaultValue)
						{
							commandline.value = '';
						}
					};
					
					commandline.onblur = function()
					{
						if(commandline.value == '')
						{
							commandline.value = commandline.defaultValue;
						}
					};
				}

				if(document.getElementById && document.createTextNode)
				{
					window.onload = function()
					{
						reset_commandline();
					};
				}
			</script>
			<link rel="stylesheet" type="text/css" href="base.css" />
			<link rel="stylesheet" type="text/css" href="design.css" />
			<link rel="author" href="humans.txt" />
			<?php if(!empty($_SESSION['window'])) { ?>
				<meta http-equiv="Refresh" content="10; url=index.php" /> <!-- Redirection toutes les 30 secondes pour actualiser l'affichage -->
			<?php }?>
			<?php if($browser == 'iphone'){ ?>
				<meta name="apple-mobile-web-app-capable" content="yes">
				<meta name="viewport" content="width=device-width, minimum-scale=1.0, user-scalable=no" />
				<link rel="apple-touch-icon" href="iphone.png" />
				<script type="text/javascript">
					$(document).ready(function() {
						window.scrollTo(0, 1);
						$("a").click(function (event) {
							if (  (navigator.standalone)
								  &&
								  ((navigator.userAgent.indexOf("iPhone") > -1) || (navigator.userAgent.indexOf("iPad") > -1))
								) {
								//Prevent iOs from quitting the fullscreen mode when opening a link
								event.preventDefault();
								window.location = $(this).attr("href");
							}
						});
					});
				</script>
			<?php } ?>
		</head>
		<body>
			<?php 
				if(!empty($output))
				{
					echo '<div id="output"><pre>'.$output.'</pre><p><a href="index.php">Go back</a></p></div></body></html>';
					exit;
				}
				
				if(is_file('tmp/tmp.png')) //Protection against the "img not found" little frame
				{
			?>
					<p id="background"" ontouchstart="touchStart(event,'background');" ontouchend="touchEnd(event); if(swipeDirection == 'left') {  window.location='?right=1'; } if(swipeDirection == 'right') { window.location='?left=1'; }" ontouchmove="touchMove(event);" ontouchcancel="touchCancel(event);"><img src="tmp/tmp.png"/></p>
			<?php
				}
				else //Display an error if we don't display the form
				{
					if(empty($form_content))
					{
		?>
						<div id='error'>
							<p>An error occured. Screenshot is not available.</p>
							<p>Have you opened the selected presentation viewer ?</p>
						</div>
			<?php
					}
				}
				if(!empty($titre)) //Else, display the commands
				{
			?>
			<div id="left">
				<p>
					<a href='?left=1'><img src='left.png' alt='Gauche' style='width: 50px;'/></a>
				</p>
			</div>
			<div id="right">
				<p>
					<a href='?right=1'><img src='right.png' alt='Droite' style='width: 50px;'/></a>
				</p>
			</div>
			
			<?php 
				}
				if(!empty($form_content)) { //If needed, display the form ?>
					<form method="post" action="index.php" id="background">
						<p>
							<span class="underline">Window to work with ?</span>
						</p>
						<p>
							<?php echo $form_content; ?>
						</p>
						<p>
							<input type='submit' value='↳'/>
						</p>
					</form>
			<?php 				} 
				if(!empty($_SESSION['window'])) //And if $_SESSION is set, display a link to go back to the window choice
				{
			?>
					<form method="post" action="index.php" id="commandline">
						<p>
							<input type="text" name="command" value="Custom command" size="12"/>
							<input type="submit" id="submit" value="↵"/>
						</p>
					</form>
					<p id="quit"><a href="?quit=1">Go back to form</a></p>
					<p id="refresh"><a href="index.php">↺</a></p>
			<?php
				}
			?>
		</body>
	</html>
