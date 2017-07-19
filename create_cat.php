<?php
//create_cat.php

define('DOC_ROOT', realpath(dirname(__FILE__) . '/../'));
require DOC_ROOT.'/includes/master.inc.php'; // required
if (!empty($_REQUEST['edit'])) {
	//die ('we have set the area to '.$_REQUEST['area']);
	define('AREA',$_REQUEST['edit']);
}
//else {
//define('AREA','0');
//}

if ($_POST['action'] == 'Quit') { redirect ('index.php');} // hit the quit button see template
$template = new Template;

if($Auth->loggedIn()) 
           {
			   //die("auth done logged in");
			   $name = $Auth->username;
			   $id = session_id();
			   $nid = $Auth->nid;
			    $login = $template->load($page['template_path'].'admin.html', COMMENT) ;
			    
			    
			   }
			   
	else
				{
					
					$name ="Guest";
					$login = $template->load( $site->settings['url'].'/templates/guest.html') ;
					redirect ("index.php");
					
				}
				
	//writeid ($id,$nid,$database); 
	
$page['header'] = $template->load($page['template_path'].'/header.html', COMMENT);
$page['footer'] = $template->load($page['template_path'].'/footer.tmpl', COMMENT);
$page['include'] = $template->load( $page['template_path'].'include.tmpl', COMMENT);
$page['navi'] = '<a style="color:#FFFFFF" href="index.php">Shop->New Category</a>';
$page['title'] = "Create Category";
$page['path'] = $site->settings['url'];
$page['login'] = $login;
$page['error'] = $Error;
$page['datetime'] = FORMAT_TIME;
$page['substyle'] = AREA;
$template->load ($page['template_path'].'/create_cat.html', COMMENT);


if( $Auth->level <> 'admin' )
{
	//the user is not an admin
	die ("hit a none admin");
	redirect ("index.php");
		
}
else
{
	
	//the user has admin rights
	
	if($_SERVER['REQUEST_METHOD'] = 'POST' and !is_null($_REQUEST['edit'])  )
	{
		//the form hasn't been posted yet, display it
		//die( 'Area = '.AREA);
		  $sql = "SELECT cat_id, cat_name, cat_description, modules.can_child as child FROM categories left join modules on categories.area = modules.moduleid  where isgroup = 1 and area =".AREA ;
		 
		$result = $database->query($sql);
		
		
		$catlist= '<select name="groupid" id="groupid" style="float:left;"><option value="0">None</option> ';
					if ($result){
					$cat = $database->get_results($sql);
				
					foreach ($cat as $row)
					{
						
						if ($row['child'] == '0')
						{ 
							$catlist = 'This area Can not be a sub group also the \'is group\' option has no effect
							<input type="hidden" name="kids" id="kids" value ="true" >';
							goto nokids;
							}
						$catlist .= '<option value="' . $row['cat_id'] . '">' . $row['cat_name'] . '</option>';
						
					}
					$catlist .='</select>';
					nokids:
				}
					// publish
					$template->replace ("catlist",$catlist); 
					if($site->settings['showphp'] === false) { $template->removephp();}
					$template->replace_vars($page); 
					$template->publish();
					
	}
	else
	{
		//the form has been posted, so save it
		if (!$_POST['isgroup'] ===1)
		{ 
			$sql = "INSERT INTO categories(cat_name, cat_description , isgroup, groupid,area)
				   VALUES('".$database->escape($_POST['cat_name']) . "',
				 '".$database->escape($_POST['cat_description']) ."',
				 '".$database->escape($_POST['isgroup'])."',
				 '".$database->escape($_POST['groupid'])."',
				 '".$database->escape($_POST['substyle'])."')";
			 }
			 else{
				 $sql = "INSERT INTO categories(cat_name, cat_tooltip , isgroup, groupid,area)
				   VALUES('".$database->escape($_POST['cat_name']) . "',
				 '".$database->escape($_POST['cat_description']) ."',
				 '".$database->escape($_POST['isgroup'])."',
				 '".$database->escape($_POST['groupid'])."',
				 '".$database->escape($_POST['substyle'])."')";
			 }
		$result = $database->query($sql);
		if(!$result)
		{
			//something went wrong, display the error
			echo 'Error' . mysqli_error();
		}
		else
		{
			//$template->replace_vars($page); 
			//$template->publish();
			redirect('/admin.php?activetab=2');
		}
	} 
}


?>
