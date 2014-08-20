<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Server Application Desktop - FC-System</title>
 	<!-- LIBS -->
 	<script type="text/javascript" src="servappdesk.app/js/ext-base.js"></script>
    <script type="text/javascript" src="servappdesk.app/js/ext-all.js"></script>

    <!-- DESKTOP -->
    <script type="text/javascript" src="servappdesk.app/js/StartMenu.js"></script>
    <script type="text/javascript" src="servappdesk.app/js/TaskBar.js"></script>
    <script type="text/javascript" src="servappdesk.app/js/Desktop.js"></script>
    <script type="text/javascript" src="servappdesk.app/js/App.js"></script>
    <script type="text/javascript" src="servappdesk.app/js/Module.js"></script>
	
	<!-- FC-SYSTEM Module -->
	<?php
		$appset = array();
		$dir = opendir(dirname(__FILE__));
		$i = 0;
		while(($file = readdir($dir)) !== false) {
			if(strpos($file, ".app") > -1 && file_exists($file."/com.fcsys.appinfo.json")) {
				$appinfo = file_get_contents($file."/com.fcsys.appinfo.json");
				$appset[$i] = json_decode($appinfo);
				$appset[$i++]->pkg = $file;
			}
		}
		closedir($dir);
		$appnum = count($appset);
	?> 
    <script type="text/javascript">
	MyDesktop = new Ext.app.App({
		init :function(){
			Ext.QuickTips.init();
		},

		getModules : function(){
			return [
				//new MyDesktop.TestWindow(),
				<?php 
				for($i=0; $i < $appnum; $i++) {
					echo "new MyDesktop.".$appset[$i]->application."(),\n				"; 
				}
				?>
			];
		},

		// config for the start menu
		getStartConfig : function(){
			return {
				title: 'FC-System Control Panel',
				iconCls: 'settings',
				toolItems: []
			};
		}
	});

	<?php for($i=0; $i < $appnum; $i++) { ?>
	MyDesktop.<?php echo $appset[$i]->application; ?> = Ext.extend(Ext.app.Module, {
		id:'<?php echo $appset[$i]->application."-xwindow"; ?>',
		init : function(){
			this.launcher = {
				text: '<?php echo $appset[$i]->application; ?>',
				iconCls:'tabs',
				handler : this.createWindow,
				scope: this
			}
		},

		createWindow : function(){
			var desktop = this.app.getDesktop();
			var win = desktop.getWindow('<?php echo $appset[$i]->application."-xwindow"; ?>');
			if(!win){
				win = desktop.createWindow({
					id: '<?php echo $appset[$i]->application."-xwindow"; ?>',
					title:'<?php echo $appset[$i]->title; ?>',
					width:(window.innerWidth < 640 ? '100%' : <?php if(is_numeric($appset[$i]->width)) {echo $appset[$i]->width;} else {echo 800;} ?>),
					height:<?php if(is_numeric($appset[$i]->height)) {echo $appset[$i]->height;} else {echo 480;} ?>,
					iconCls: 'tabs',
					shim:false,
					animCollapse:false,
					border:false,
					constrainHeader:true,
					html:'<iframe src="<?php if(strpos($appset[$i]->exec, "://") > -1) {echo $appset[$i]->exec;} else {echo $appset[$i]->pkg."/".$appset[$i]->exec;} ?>" width="100%" height="100%" style="border:0;"></iframe>'
				});
			}
			win.show();
		}
	});
	<?php } ?>
	</script>
	<script type="text/javascript">
    var winHeight=0;
	var paddingHeight=80;
	var divHeight=160;
    function findDimensions() { 
        if (window.innerHeight) {
            winHeight = window.innerHeight;
        }
        else if ((document.body) && (document.body.clientHeight)) {
            winHeight = document.body.clientHeight;
        }
        if (document.documentElement && document.documentElement.clientHeight && document.documentElement.clientWidth) {
            winHeight=document.documentElement.clientHeight;
        }
        if (document.getElementById("x-desktop-icons")) {
            document.getElementById("x-desktop-icons").style.height = (winHeight-divHeight)-paddingHeight + "px";
        }
    }
    findDimensions();
    window.onresize=findDimensions;
	</script>

	<link rel="stylesheet" type="text/css" href="servappdesk.app/css/ext-all.css" />
    <link rel="stylesheet" type="text/css" href="servappdesk.app/css/desktop.css" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body scroll="no" onload="findDimensions()">

<div id="x-desktop">

<!-- Header of Icon List -->
<div id="x-desktop-header">
	<div id="x-oem-icon">
		<img src="servappdesk.app/images/oem.png"/>
	</div>
	<div id="x-desktop-topmenu">Applications</div>
</div>

<!-- Icon List Set -->
<div id="x-desktop-icons">
    <dl id="x-shortcuts">
		<?php for($i=0; $i < $appnum; $i++) { ?>
        <dt id="<?php echo $appset[$i]->application."-xwindow"; ?>-shortcut">
            <a href="#"><img src="<?php echo $appset[$i]->pkg."/".$appset[$i]->icon; ?>"/>
            <div><?php echo $appset[$i]->title; ?></div></a>
        </dt>
		<?php } ?>
    </dl>
</div>

<!-- Footer Info -->
<div id="x-desktop-footer" style="">
Copyright &copy; 2011-2014 FC-System Computer Inc.<br/>
FC-System Network Group<br/>
About this program: Server Application Desktop, build 20140820<br/>

</div>
</div>

<div id="ux-taskbar" style="display:none">
	<div id="ux-taskbar-start"></div>
	<div id="ux-taskbuttons-panel"></div>
	<div class="x-clear"></div>
</div>

</body>
</html>
