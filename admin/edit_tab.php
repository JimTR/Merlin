<?php
	/* 
	 * Edit_tab.php
	 * Created 01-04-2015
	 * update  03-10-2015
	 * use -  Edit the tabbed divs & std areas
	 * requires master inc
	 */
	 define('DOC_ROOT', realpath(dirname(__FILE__) . '/../'));
	 
		 
     require DOC_ROOT.'/includes/master.inc.php';
     //print_r($_REQUEST);
     //die();
     if($_REQUEST){
		  $area = $_REQUEST['edit'];
		 } 
		 
	  if (empty($area) and $_POST['save']<>'save') {
		  redirect('/');
		  //echo 'both false';
		  } // fuck off nasty	
     $template = new Template;
     $html_file = $page['template_path'].'admin/edit_tab.html'; // set the html file
     //die ($html_file);
     $page['header'] = $template->load($page['template_path'].'header.html', COMMENT); // load header
	 $page['footer'] = $template->load($page['template_path'].'footer.tmpl', COMMENT); // load footer
	 $page['include'] = $template->load($page['template_path'].'include.tmpl', COMMENT); // load includes
	 $page['adminstats'] = '';
	 
	 $page['login'] = $template->load($page['template_path'].'admin.html', COMMENT) ;
     if($Auth->loggedIn()) 
        {
			   if (!$Auth->level === 'admin') {
			   	  redirect  ($_SERVER['HTTP_REFERER']);
			   	  
				} 
			    
        }
			   
	  else
				{
				  	redirect  ($_SERVER['HTTP_REFERER']);
				}
	 
      
		  // run editor
		  if ($_POST['save'] <>'save')
		  {
			  //print_r($_POST);
			  //die();
			$sql = 'select *, modules.can_child as child from categories left join modules on categories.area = modules.moduleid  where cat_id = '.$area; 
			$tab = $database->get_row($sql);
			$page['title'] = 'Editing '.$tab['cat_name'];
			$page['cat_id'] = $tab['cat_id'];
			$page['cat_description']= $tab['cat_description'];
			//echo 'Area is '.$area. ' the idea is '.$tab['isgroup'].' and lives in '.$tab['area'] ;
			if ($tab['child'] == '1') {
				$page['tabs']='';
				$page['tab_content'] = '<div style ="padding:1%;">We are getting ready to do this</div>';
				$template->load($page['template_path']."index.html", COMMENT);
				  } 
			else {
			$template->load($html_file, COMMENT); // load header
		}
           $template->replace_vars($page);
           $template->publish(); 
               
	     }
	     else
	     {
			 //echo "hit save";
			 //print_r($_POST);
			 //die();
			 //mysql_real_escape_string `
			 $firstname = mysqli_real_escape_string($database->link, $_POST['reply-content']);
			 die ($firstname);
			 $update ='"'.$database->escape($_POST['reply-content']).'"';
			 // UPDATE `categories` SET `cat_id`=[value-1],`cat_name`=[value-2],`cat_description`=[value-3],`cat_tooltip`=[value-4],`isgroup`=[value-5],`groupid`=[value-6],`area`=[value-7],`disp_order`=[value-8],`icon`=[value-9] WHERE 1
			 $sql = 'UPDATE categories SET `cat_description` = '.$update.' WHERE `cat_id` = '.$_POST['cat_id'];
			 $database->query($sql);
			 redirect($site->settings['url'].'/admin.php');
		  }
	  
     
?>
