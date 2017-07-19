<?php 
//define('DOC_ROOT', realpath(dirname(__FILE__) . '/../'));
define ('DOC_ROOT' , realpath(dirname(__FILE__)));
echo 'this is the real path '.realpath(dirname(__FILE__));
//DOC_ROOT .='/new';
//require 'includes/master.inc.php'; // start up stuff

echo '<br>we are running ! on '.DOC_ROOT;
echo '<br>We should be including '.DOC_ROOT.'/includes/master.inc.php'; 
print_r($settings['config']);
$sql = "show tables";
//$dele ="SELECT CONCAT( 'DROP TABLE ', GROUP_CONCAT(table_name) , ';' ) AS statement FROM information_schema.tables WHERE table_name like 'usebb_%';";
$dele = "DROP TRIGGER IF EXISTS `topic_after_delete`;
CREATE TRIGGER `topic_after_delete` AFTER DELETE ON `topics`
 FOR EACH ROW UPDATE users SET topicnum = topicnum - 1 
WHERE id = OLD.topic_by LIMIT 1
DROP TRIGGER IF EXISTS `update_topics`;
CREATE TRIGGER `update_topics` AFTER INSERT ON `topics`
 FOR EACH ROW UPDATE users
SET topicnum = topicnum+1 
WHERE id = NEW.topic_by LIMIT 1;
";
$plugins = "CREATE TABLE IF NOT EXISTS `plugins` (
  `pid` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `enabled` int(11) NOT NULL,
  `plugin` text NOT NULL,
  `area` int(11) NOT NULL,
  PRIMARY KEY (`pid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
";

$users = "CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nid` varchar(32) NOT NULL DEFAULT '',
  `username` varchar(65) NOT NULL DEFAULT '',
  `password` varchar(65) NOT NULL DEFAULT '',
  `theme` text NOT NULL,
  `level` enum('user','admin','mod','smod') NOT NULL DEFAULT 'user',
  `avatar` varchar(256) CHARACTER SET armscii8 COLLATE armscii8_bin NOT NULL,
  `dob` bigint(20) NOT NULL,
  `ip` varchar(50) NOT NULL,
  `currentip` varchar(20) NOT NULL,
  `regdate` bigint(30) NOT NULL,
  `lastseen` int(10) NOT NULL,
  `topicnum` int(8) NOT NULL,
  `postnum` int(8) NOT NULL,
  `email` varchar(50) NOT NULL,
  `steamid` bigint(30) NOT NULL,
  `skypeid` varchar(128) NOT NULL,
  `sig` varchar(1024) NOT NULL,
  `nick` varchar(128) NOT NULL,
  `b_priv` int(1) NOT NULL,
  `sex` int(1) NOT NULL,
  `tabs` text NOT NULL,
  `loc` varchar(128) NOT NULL,
  `url` varchar(128) NOT NULL,
  `bio` varchar(500) NOT NULL,
  `posts` int(1) NOT NULL,
  `threads` int(1) NOT NULL,
  `show_ava` text NOT NULL,
  `hosting` enum('0','1','2','3') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
";
$admin = "CREATE TABLE IF NOT EXISTS `admin` (
  `cat_id` int(8) NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(255) NOT NULL,
  `cat_description` mediumtext NOT NULL,
  `cat_tooltip` varchar(64) NOT NULL,
  `isgroup` tinyint(1) NOT NULL,
  `groupid` int(11) NOT NULL,
  `area` int(11) NOT NULL COMMENT 'used for checking template in admin',
  `disp_order` int(11) NOT NULL,
  `icon` varchar(120) NOT NULL,
  `tab_display` text NOT NULL COMMENT 'this is comma delimeted display for device',
  PRIMARY KEY (`cat_id`),
  KEY `area` (`area`),
  KEY `area_2` (`area`)
) ENGINE=InnoDB  DEFAULT CHARSET=ascii AUTO_INCREMENT=1 ;
";
$bots = "CREATE TABLE IF NOT EXISTS `bots` (
  `bot_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'index',
  `user_agent` text NOT NULL,
  `ip` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `bot_name` text NOT NULL,
  `visits` int(11) NOT NULL,
  `last_visit` varchar(20) NOT NULL,
  PRIMARY KEY (`bot_id`),
  UNIQUE KEY `ip` (`ip`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
";
$cats = "
CREATE TABLE IF NOT EXISTS `categories` (
  `cat_id` int(8) NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(255) NOT NULL,
  `cat_description` mediumtext NOT NULL,
  `cat_tooltip` varchar(64) NOT NULL,
  `isgroup` tinyint(1) NOT NULL,
  `groupid` int(11) NOT NULL,
  `area` int(11) NOT NULL,
  `disp_order` int(11) NOT NULL,
  `icon` varchar(120) NOT NULL,
  `tab_display` text NOT NULL COMMENT 'this is comma delimeted display for device',
  PRIMARY KEY (`cat_id`),
  KEY `area` (`area`)
) ENGINE=InnoDB  DEFAULT CHARSET=ascii AUTO_INCREMENT=1 ;
";
$cat_trig = "
CREATE TRIGGER `add_permission` AFTER INSERT ON categories
 FOR EACH ROW INSERT INTO permissions (pcat_id) VALUES (NEW.cat_id);
";
$permissions ="CREATE TABLE IF NOT EXISTS `permissions` (
  `pcat_id` int(8) NOT NULL,
  `guest` varchar(10) NOT NULL DEFAULT '1,0,0,0',
  `banned` varchar(10) NOT NULL DEFAULT '0,0,0,0',
  `user` varchar(10) NOT NULL DEFAULT '1,1,1,0',
  `moderator` varchar(10) NOT NULL DEFAULT '1,1,1,1',
  `admin` varchar(10) NOT NULL DEFAULT '1,1,1,1',
  UNIQUE KEY `pcat_id` (`pcat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
$addAdmin = "INSERT INTO `users` (`id`, `nid`, `username`, `password`, `theme`, `level`, `avatar`, `dob`, `ip`, `currentip`, `regdate`, `lastseen`, `topicnum`, `postnum`, `email`, `steamid`, `skypeid`, `sig`, `nick`, `b_priv`, `sex`, `tabs`, `loc`, `url`, `bio`, `posts`, `threads`, `show_ava`, `hosting`) VALUES
(1, '1c05d986e681f1aa16f21d99e2223062', 'Jim', '7a59f3d867dfecfebebca6fa55954d52', 'light', 'admin', 'http://kbracing.eu/images/linwin.png', -233586000, '86.130.94.43', '86.130.92.188', 1424630157, 1443623662, 2, 78, 'jim@noideersoftware.co.uk', 0, '', '<p>Forum Moderator and some</p>\r\n', 'Admin', 0, 1, '', 'Malvern Worcs UK', '', '', 10, 6, '', '0');
";
$modules = "CREATE TABLE IF NOT EXISTS `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `description` varchar(50) NOT NULL,
  `moduleid` int(11) NOT NULL,
  `mod_path` varchar(256) NOT NULL,
  `enabled` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `moduleid` (`moduleid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
";
$adminrows ="INSERT INTO `admin` (`cat_id`, `cat_name`, `cat_description`, `cat_tooltip`, `isgroup`, `groupid`, `area`, `disp_order`, `icon`, `tab_display`) VALUES
(1, 'General', '<div class=\"cell width-4of4\" style=\"float:left;max-width:25%;min-height:80%;\">
	#astat#</div><div class=\"cell width-3of4\" style=\"padding:0%;float:left;max-width:25%\">
	<h6 style=\"text-align:center;padding-top: 0%; \">Site Status</h6>
	<br>
	<div>
	<span style=\"float:left\"><a href = \"admin.php?action=turnstatus\" title = \"turn the site on or off\" >On/Off Line</a></span>
	<span style=\"float:left;color:#stat#;margin-left:9%;\" class=\"icon icon-16 icon-circle\"></span>
	<span style=\"text-align:center;float:right;\">This closes the site completely</span></div><div><a href =\"#\" onclick=\"loadPopupBox(''popup_box'',''25%'')\">Off line URL</a>
	<span style=\"float:right;color:green\">#closed#</span></div><div style = \"margin-bottom: 0px;\"><a href=\"#\" onclick=\"loadPopupBox(''popup_box2'',''25%'')\">Date & Time</a>
	<span style=\"float:right;color:green;\">#date_format#</span></div></div><div id=\"popup_box\" class=\"popup_box\" style=\"width:30%;height:auto;margin:auto;\">	<!-- OUR PopupBox DIV-->		 <form action=\"/admin.php\" method=\"post\" id=\"subForm1\">		 <h4 style=\"text-align:center;\">Select The Closed URL</h4><br />		 <input type=\"hidden\" value=\"change_url\" name=\"action\"></input><input type=\"hidden\" value=\"#active_tab#\" name=\"activetab\"		 <span style=\"margin-left:2%;float:left;width:100%;\">Enter The site closed URL		 <input type=\"text\"style=\"margin-left:2%;width:60%;\" class=\"text\" id=\"curl1\" name=\"curl1\" value=\"#closed#\">		 </span> 		 <br style=\"clear:both;\" />		 <input type=\"submit\" class=\"button\" style=\"text-align:center;clear:both;margin-left:46%;margin-top:3%;\" value=\"Do it\">		 <input type=\"button\" class=\"button\" onclick=\"unloadPopupBox(''popup_box'')\" style=\"text-align:center;margin-left:2%;margin-top:3%;\" value=\"Quit\">		 </form> 	</div>		 		 <div id=\"popup_box2\" class=\"popup_box\" style=\"min-width:23%;height:auto;margin:auto;\">	<!-- OUR PopupBox DIV-->		 		 <form action=\"admin.php\" method=\"post\" id=\"subForm1\"><input type=\"hidden\" value=\"change_date\" name=\"action\"></input>		 		 <h4>Choose Site Date & Time Format</h4><br>		 		 <span style=\"margin-left:4%;float:left;\"> Choose Date Format 			 		 <select style=\"margin-left:20px;\" name=\"date\">				 		 <option value=\"1\">dd-mm-yyyy</option>				 		 <option value=\"2\">yyyy-mm-dd</option>				 		 <option value=\"3\">dd/mm/yyyy</option>			 		 </select>		 		 </span>		 		 <br style=\"clear:both;\">		 		 <span style=\"margin-left:4%;float:left;margin-top:16px;\">Choose Time Format 			 		 <select style=\"margin-left:20px;\" class=\"text\" name=\"time\">				 		 <option value=\"1\">line 1</option>				 		 <option value=\"2\">line 2</option>			 		 </select>		 		 </span>		 		 <br style=\"clear:both;\"> <input type=\"submit\" class=\"button\" style=\"text-align:center;clear:both;margin-left:46%;margin-top:3%;\" value=\"Do it\">		 <input type=\"button\" class=\"button\" onclick=\"unloadPopupBox(''popup_box2'')\" style=\"text-align:center;margin-left:2%;margin-top:3%;\" value=\"Quit\">		 	 		 </form>		 		 </div>', 'quick Options', 1, 0, 0, 0, '', '1,1,1'),
(2, 'User Control', '    <h3>User Control</h3>#active_tab#', 'user functions', 1, 0, 0, 1, '', ''),
(3, 'Area Control', '<h3>Area Control</h3>#active_tab#', 'areas', 1, 0, 0, 2, '', ''),
(4, 'Modules', '<h3>Modules</h3>#active_tab#', 'Modules & Plugins', 1, 0, 0, 4, '', '1,1,1'),
(5, 'Styling', '<h3>Styling</h3>#active_tab#<br><a href=\"admin.php?activetab=#active_tab#\">test link</a>', 'css & html', 0, 0, 0, 5, '', '1,1,1'),
(6, 'Maintainence', '<h3>Maintainence</h3>#active_tab#', 'tools', 0, 0, 0, 6, '', '1,1,1'),
(7, 'Plugins', 'here we edit plugins', 'Plugin Maintance', 0, 0, 0, 3, '', '');
";
//echo '<br>'.$sql
//$database->query($addAdmin);
echo '<br> ready to do this';
$a = $database->get_results($sql);
echo '<br>done it';
foreach ($a as $row)
{
	$pos = strrpos($row ['Tables_in_midland'], "mybb_");
	if ($pos === false) {
	echo '<br>'.$row ['Tables_in_midland'];
}
  //$del = "DROP TABLE ".$row['Name'];
  //$database->query($del);
}

print_r($a);
?>
