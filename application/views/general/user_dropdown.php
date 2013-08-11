<script>
	$(function() {
        $( "#menu" ).menu({ position: { my: "left top"} });
	});
</script>

<style>
	/** too lazy to make another stylesheet. Fixes weird JQuery UI styling for menus */

	#menu, .ui-menu-item, .ui-widget-content, .ui-menu {
		background: #fff !important;
	}
	
	.ui-state-active {
		background: #fff !important;
		border: none !important;
	}
	
	a.ui-state-focus {
		border: none !important;
	}
	
	#menu a:hover {
		background #fff !important;
	}
	
	.ui-menu {
		margin-left: 10px;
	}
	
	#menu a, #menu a:visited {
		color: #1BA5E0;
	}
</style>

<div id="userName" class="dropdown">
	<ul id="menu">
		<li>
			<a href="#"><?=$_SESSION['user']->login ?></a>
			<ul>
				<li><a href="#"><?=anchor("arcade/index", "Lobby") ?></a></li>
				<li><a href="#"><?=anchor("account/updatePasswordForm", "Change Password") ?></a></li>
				<li><a href="#"><?=anchor("account/logout", "Logout") ?></a></li>
			</ul>
		</li>
	</ul>
</div>