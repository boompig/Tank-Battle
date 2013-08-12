<script>
	$(function() {
        // $("#userName a").tooltip();
	});
</script>

<style>
	/** too lazy to make another stylesheet. Fixes weird JQuery UI styling for menus */

	header .ui-icon {
		opacity: .4;
		height: 35px;
		width: 35px;
	}
	
	header .ui-icon:hover {
		opacity: 1;
	}
	
	header #userName {
		text-align: right;
		display: inline;
		margin-left: 500px;
		font-size: 1.1em;
		font-weight: bold;
	}
	
	#userNameMain {
		position: relative;
		bottom: 11px;
	}
	
	header .ui-icon, #userNameMain {
		display: inline;
		margin-right: 10px;
	}
</style>

<div id="userName">
	<span id="userNameMain"><?=$_SESSION['user']->login ?></span>
	<?php
		$contents = '<img src="' . base_url() . 'images/settings.jpg" class="ui-icon ui-icon-settings"  />';
		echo anchor("account/updatePasswordForm", $contents, array("title" => "Change Password"));
		$contents = '<img src="' . base_url() . 'images/logout.png" class="ui-icon ui-icon-logout" title="Logout" />';
		echo anchor("account/logout", $contents, array("title" => "Logout"));
	?>
</div>