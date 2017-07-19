<?php
// main or home page admin.cp
/* 8-12-2015 update
 * added database table to handle the settings groups
 */
require 'includes/master.inc.php'; // required
require 'includes/functions_admin.php'; //admin functions 
//echo php_uname('a');
if (!$_SERVER['HTTP_REFERER']) { 
		redirect ($site->settings['url']."/index.php");
	} 
		
if (!$_SERVER['HTTP_REFERER'] === $site->settings['url']."/index.php" || !$_SERVER['PHP_SELF']) {
		redirect ($site->settings['url']."/index.php");
	}
if( $Auth->level <> 'admin' )
{
	//the user is not an admin
	redirect ("index.php");
		
}
    $activetab=0;
//print_r($_REQUEST);			  
if (!isset ($_REQUEST['action']))
{
displayit:
if (isset($_REQUEST['activetab'])) 
{
	// find out what tab to display
	$activetab = intval($_REQUEST['activetab']);
	}		
else {
	// if nothing default
	$activetab = 0;}
$tab=0;
$sql = '
 select (SELECT COUNT(*) FROM posts) as post_count, 
(select count(*) from users) as user_count, 
(select count(*) from topics) as topic_count, 
(select count(*) from categories) as cat_count,
(select count(*) from sessions where usertype = 2) as bots ,
(select count(*) from modules) as modules,
(select count(*) from sessions where usertype <>2 and usertype <> 0) as wol,
(select count(*) from sessions where usertype = 0) as guest
limit 1';
 $astats = $database->get_row($sql);
  

if ($settings['siteclosed'] == 1) { 
	$stat = "#6FAF47"; 
	} 
else {
	$stat="#FF0000";
	} 
$postsize = (int)(str_replace('M', '', ini_get('post_max_size')) * 1024 * 1024);
$upsize = (int)(str_replace('M', '', ini_get('upload_max_filesize')) * 1024 * 1024);
$load = sys_getloadavg();
$pt = $_SERVER['DOCUMENT_ROOT'];
$df = DOC_ROOT;
$df = getDirectorySize($df);
$page['date_format'] = date($site->settings['date_format'],time()).' '.date($site->settings['time_format'],TIME_NOW);

$template =  new Template;
$name = $Auth->username;
$nid = $Auth->nid;
     $login = $template->load($page['template_path'].'admin.html', COMMENT) ;
    $page['header'] = $template->load($page['template_path'].'header.html', COMMENT); // load header
	$page['footer'] = $template->load($page['template_path'].'footer.tmpl', COMMENT);
	$page['include'] = $template->load($page['template_path'].'include.tmpl', COMMENT);
	$page['login'] = $login;
	//add the tabs
	$our_plugin->get_plugins(basename($_SERVER['PHP_SELF'])); //run plugin code
	//print_r($page);
	//die();
	$sql = 'select * from admin where area = 0 order by disp_order asc'; // needs to be changed on device type
	
	$root = $database->get_results($sql);
		$tabs = new Template;
		
			foreach ($root as $row)
				{
					
					//$priv = explode(",",$row[$level]);
					if ($priv[0] === '0')
	                 {
						// goto noview;
					} 
					$tabs->load($page['template_path'].'/tab.html',false); //dont show this templates remarks  
					$tab_entry['tab_id'] = $row['cat_id']; 
					$tab_entry['tab_name'] = $row['cat_name']; 
					$tab_entry['tab_title'] = $row['cat_tooltip'];
					$tab_entry['datetime'] = $page['datetime'];
					//$tabs['active_tab'] == $tab; 
					if ($ul == 0 && $activetab == $tab) //sets active tab later it will remember now does it
						{
							$tab_entry['tab_class'] = "active"; // sets the tab active
							$ul =1;
							$class = "cell";
						}         
					else 
						{
							$tab_entry['tab_class'] = ""; // not active
							$ul = 0;
							$class =  "cell hidden-tab";
						}	
						
					$tabs->replace_vars($tab_entry);
					$page['tabs'].= $tabs->get_template(); // add the tab in
					//now add the content !
					$tabs->load($page['template_path'].'/tab_desc.html',false); // load the description template
					$tabs->replace("content",$row['cat_description']);
					
						if  ($tab_entry['tab_class'] = "active")
							{
								//$class = $class = "cell";
							}
						else
							{
								//$class =  "cell hidden-tab";
							}
					$tabs->replace("class",$class);
					$tabs->replace("active_tab",$tab);	
					$tabs->replace("id",$row['cat_id']);
					$tabs->replace("path",$page['path']);
					$tabs->replace("title",	$row['cat_name']);
					$tabs->replace_vars($page);	    
					$page['tab_content'] .=$tabs->get_template();
					$tab++;
					noview:  
				}

//print_r($page);
//die();	
//wget -O - --quiet --no-check-certificate 'http://lightsoundstudiosuk.co.uk/lsb_release -a'
exec ("lsb_release -a",$os);
//print_r($os[1]);
$os = explode(':',$os[1]);
//print_r($os[1]);			
$page['astat'] = $template->load($page['template_path'].'admin/stat.html',COMMENT);
$page['adminstats'] = ""; //do we need this ??
$page['datetime'] = FORMAT_TIME;
$page['path'] = $site->settings['url'];
$page['ds'] = $df['count'];
$page['df']= sizeFormat($df['size']);
$page['php'] = phpversion();
$page['sql'] = getsql();
$page['os'] = $os[1];
$page['mi'] = 0; 
$page['load'] = $load[0];
$page['dbs'] = sizeFormat($dbsize[1]);
$page['dbs1'] = sizeFormat($dbsize[2]); 
$page['pt'] = $df['dircount'];
$page['postsize'] = sizeFormat($postsize);
$page['upsize'] = sizeFormat($upsize);
$page['ver']= $site->settings['version']; 
$page['uol'] = $astats['wol'];;
$page['mod'] = $astats['modules'];
$page['modules']  = get_modules();
$page['total_content'] = $astats['post_count'];
$page['closed'] = $site->settings['siteclosed_url'];
$areas = get_areas();
$page['arealist'] = $areas['existing'];
$page['users'] = get_users();
$page['groups'] = $areas['groups'];
$page['plugins'] = get_plugins();
$page['gen'] = gen();
$page['areas'] = areas();
$page['queue'] = ' not there yet';
$template->load($page['template_path'].'admincp.html', COMMENT);
$page['settings'] = settings();
$page['style'] = style();
$template->replace_vars($page);
$template->publish();
}
$file = rtrim($_SERVER['DOCUMENT_ROOT'],"/").dirname($_SERVER['PHP_SELF']).'/includes/settings.php';
$header ="SETTINGS v". $settings['version']."\n do not edit this file ! use admin cp unless told otherwise";

$name="settings";
if (isset ($_REQUEST['action'])){
	//die($_REQUEST['action']);
switch ($_REQUEST['action']) {
    case "turnstatus":
        if ($settings['siteclosed'] == 0){
			
		$settings['siteclosed'] = 1;} 
		else {$settings['siteclosed'] = 0;}
		writeini ($settings,$file,$header,$name);
		redirect("admin.php");
        break;
    case "change_date":
            switch ($_REQUEST['date']) {
				case "1":
					$_REQUEST['date'] = 'l, d M Y';
				break;
				   
				case "2":
					$_REQUEST['date'] = 'Y-m-d';
				break;
            }  
              switch ($_REQUEST['time']) {
				  case "1":
					$_REQUEST['time'] = 'g:i:s a';
				  break; 
				  case "2":
					$_REQUEST['time'] = 'h:i:s';
				break;	
			  }
			  			
			$settings['date_format'] = $_REQUEST['date'];
			$settings['time_format'] = $_REQUEST['time'];
			writeini ($settings,$file,$header,$name);
         break;
    case "change_url":
    $settings['siteclosed_url'] = $_REQUEST['curl1'];
			writeini ($settings,$file,$header,$name);
			break;
    default:
        
        print_r($_REQUEST);
        die($_REQUEST['action']);
        break;
	}
	unset ($_REQUEST['action']);
	redirect (DOC_ROOT.'/index.php');
	//goto displayit;
}
function get_areas() {
	// draw out the areas
	global $database;
	$sql = "SELECT * FROM `categories` left join modules on categories.area = modules.moduleid order by disp_order, modules.moduleid";
	$results = $database->get_results($sql);
	$return['existing'] ='<select name="edit" style ="float:left;">';
	$return['groups'] ='<select name ="edit" style ="float:left;">';
	if ($results)
	{
		foreach ($results as $row) {
			
			if (empty($opts))
			{
				
				$return['existing'] .= '<optgroup label="'.$row['name'].'">';
				$return['groups'] .= '<option value="'.$row['moduleid'].'"/>'.$row['name'].'</option>';
			}
			elseif ($opts <> $row['name']) {
				
				$return['existing'] .='</optgroup><optgroup label ="'.$row['name'].'">';
				$return['groups'] .= '<option value="'.$row['moduleid'].'"/>'.$row['name'].'</option>';
			}
			
			if ($row['isgroup'] == '1'){
			$return['existing'] .= '<option value="'.$row['cat_id'].'"/>'.$row['cat_name'].'</option>';}
			else {
				
				$return['existing'] .= '<option value="'.$row['cat_id'].'"/>&#8230;&nbsp;'.$row['cat_name'].'</option>';
				}
		    $opts = $row['name']; 	
}
	$return['existing'] .='</select>';
	$return['groups'] .='</select>';
	//print_r($return);
	return $return;
}
return 'sql failed';
}
function get_users() {
	// return the user functions
	global $database,$page;
	$template = new Template; // define new class instance for the infil page
	$template->load($page['template_path'].'admin/users.html');
	$temp['groups'] ='<select name ="edit" style ="float:left;">';
	$sql = "select * from users order by level desc ";
	$results = $database->get_results($sql);
	if ($results)
	{
		// process//
		foreach ($results as $row) {
			
			if (empty($opts))
			{
				
				$temp['groups'] .= '<optgroup label="'.$row['level'].'">';
				//$temp['groups'] .= '<option value="'.$row['moduleid'].'"/>'.$row[''].'</option>';
			}
			elseif ($opts <> $row['level']) {
				
				$temp['groups'] .='</optgroup><optgroup label ="'.$row['level'].'">';
				//$temp['groups'] .= '<option value="'.$row['moduleid'].'"/>'.$row['name'].'</option>';
			}
			
			//if ($row['isgroup'] == '1'){}
			//$return['existing'] .= '<option value="'.$row['cat_id'].'"/>'.$row['cat_name'].'</option>';}
			//else {
				
				$temp['groups'] .= '<option value="'.$row['id'].'"/>&#8230;&nbsp;'.$row['username'].'</option>';
				//}
				
		    $opts = $row['level']; 	
}
		$temp['groups'] .='</select>';
		$temp['arealist'] = '<span style="float:left;">we should put result</span>';
	}

	$template->replace_vars($temp);
	$return = $template->get_template();
	return $return;
}
function get_plugins() {
	// get plugin list !
	global $database,$page,$site;
	$sql = "select * from plugins";
	$template = new Template;
	$template->load($page['template_path'].'admin/plugins.html');
	$dir    = DOC_ROOT.'/includes/plugins';
	$files2 = scandir($dir, 1);
	
	foreach ($files2 as $plugin)
	{
		// loop !!
		if ($plugin != "." && $plugin != "..") {
			$withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $plugin);
			// here we add all the plugin stuff & wrap in a template
			//echo $plugin.'<br>';
			require_once $dir.'/'.$plugin;
			//$info_func = "{$plugin}_info";
			$do = "{$withoutExt}_info";
			$setting = "{$withoutExt}_settings";
			//$run_func = "{$plugin['plugin']}_disabled";
		    // then add it to the output
		    $test = $do();
		    
			$a .= '<b>'.ucwords($test['name']).'</b> ('.$test['version'] .') - '.$plugin.' <br>
			<b>Description</b> '.$test['description'].'<br><b>Returns</b> '.$test['returns'].'<hr>';
			//print_r($test);
		}
		
	} 
	return $a;
}

function gen() {
	global $database, $page, $site, $astats;
	// load the gen template
	//echo $site->settings['siteclosed'];
	exec ("lsb_release -a",$os);
	$os = explode(':',$os[1]);
	$version = explode('.', PHP_VERSION);
	if ($site->settings['siteclosed'] == 0) {$stat = "#6FAF47";} 
    else {$stat="#FF0000";} 
    $load = sys_getloadavg();
	$dbsize = getdbsize();
	$pt = $_SERVER['DOCUMENT_ROOT'];
	$df = $pt.dirname($_SERVER['PHP_SELF']);
	$df = getDirectorySize($df);
	$template = new Template;
	$template->load($page['template_path'].'admin/gen.html');
	$temp['os'] = $os[1];
	$temp['ver']= $site->settings['version'];
	$temp['stat'] = $stat;
	$temp['closed'] = $site->settings['siteclosed_url'];
	$temp['date_format'] = date($site->settings['date_format'],time()).' '.date($site->settings['time_format'],TIME_NOW);
	$temp['load'] = $load[0];
	$temp['php'] = $version[0].'.'.$version[1].'.'.intval($version[2]);
	$temp['sql'] = getsql();
	$temp['dbs'] = sizeFormat($dbsize[1]);
	$temp['dbs1'] = sizeFormat($dbsize[2]);
	$temp['eng'] = $dbsize[3];
	$date = date_create($dbsize[4]);
	$temp['ctime'] = date_format($date, 'd-m-Y H:i:s');
	$temp['ds'] = $df['count'];
	$temp['df']= sizeFormat($df['size']);
	$temp['pt'] = $df['dircount'];
	$temp['pmax'] = GIG * $site->settings['domain_size'];
	$temp['pact'] = $df['size'];
	$temp['domain_size'] = sizeFormat(1073741824 * $site->settings['domain_size']);
	$temp['total_users']  = $astats['user_count'];
	$temp['wol'] = $astats['wol'];
	$temp['bots'] = $astats['bots'];
	$temp['guest'] = $astats['guest'];
	$temp['total_content'] = $astats['post_count'];
	$temp['uploads'] = 0;
	$temp['backup_email'] = $site->settings['adminemail'];
	$template->replace_vars($temp);
	return $template->get_template();
}

function areas() {
	//run area code
		global $page;
		$template = new Template;
		$template->load($page['template_path'].'admin/areas.html');
		$areas = get_areas();
		$temp['arealist'] = $areas['existing'];
		$temp['users'] = get_users();
		$temp['groups'] = $areas['groups'];
		$template->replace_vars($temp);
		return $template->get_template();
}

function settings() {
	//get the settings to edit
	global $page,$site, $database;
	$template = new Template;
	$template->load($page['template_path'].'admin/settings.html');
	$sql = "select * from settings where display = 1 and setting_type = 0 order by s_order asc ";
	$results = $database->get_results($sql);
		$setting_line = new Template; // set up the line html
		foreach ($results as $value){
			// loop the settings only settings stored will be displayed
			$setting_line->load($page['template_path'].'admin/settings_row.html');
			// need a routine to add missing settings
			if ($value['type'] == 1) {
				// here we add the image
				$temp['image'] = '<span><img style="max-height:50px;float:right;margin-top:1%;" src ="'.$site->settings[$value['area']].'"></span>';
			}
			else {$temp['image'] ='';}
			if ($value['type'] == 2)
				{
					$value['value'] = $site->settings[$value['area']];
					if ($value['area'] === 'year') { $tags ="Roman,Standard";}
					else {$tags = "Yes,No";}
					$temp['input'] = yesno_box($value,$tags);
				}
				elseif ($value['type'] == 0 || $value['type'] == 1 ) {
					$value['value'] = $site->settings[$value['area']];
					//text_box($value);
					$temp['input'] = text_box($value);}
				elseif ($value['type'] == 3) { 
					$value['value'] = $site->settings[$value['area']];
					$temp['input'] = select_box($value,"");
					
					}	
			$temp['title'] = $value['title'];
			
			$temp['value']= $site->settings[$value['area']];
			$temp['desc'] = $value['s_desc'];
			
			$setting_line->replace_vars($temp);
			$a .= $setting_line->get_template(); 
		}
		//print_r ($site->settings);
		//echo $a;
		$template->replace( 'settings',$a);
		return $template->get_template();
		
}

function get_modules()
{
	// return formatted module list
	global $database,$site;
	$sql = 'select * from modules';
	$results = $database->get_results($sql);
	foreach ($results as $value){
		// do the biz
		if (!file_exists(DOC_ROOT.$value['mod_path'].'/index.php')){
			$demo .= '<b>'.$value['name'].'</b> ('.$value['version'].') '.$value['description'].'<br>Module Not Found at '.$site->settings['url'].$value['mod_path'].' - please re-install<hr>';
			}
		elseif ($value['enabled'] == 1) {
		$demo .= '<b>'.$value['name'].'</b> ('.$value['version'].') '.$value['description'].'<br>
		installed at <a href="'.$site->settings['url'].$value['mod_path'].'">'.$site->settings['url'].$value['mod_path'].'</a><hr>';
	}
	//file_exists ($filename )
		elseif ($value['enabled'] == 0 ){
		
			//echo DOC_ROOT.$value['mod_path'].'/index.php';
					$demo .= '<b>'.$value['name'].'</b> ('.$value['version'].') '.$value['description'].'<br>
		Not Activated but installed at '.$site->settings['url'].$value['mod_path'].'<hr>';
			}
			else{
		$demo .= '<b>'.$value['name'].'</b> ('.$value['version'].') '.$value['description'].'<br>
		Not Activated but installed at '.$site->settings['url'].$value['mod_path'].'<hr>';
		}
		
	}
	
	
return $demo;
}

function style()
{
	// return the style html
	global $database,$site,$page;
	$template = new Template;
	$template->load($page['template_path'].'admin/style.html');
	return $template->get_template();
} 
?>
