<!-- custom style -->
<link rel="stylesheet" href="<?=base_url() ?>css/header.css" />

<header>
	<div id="logoAndName">
		<?php
			$loggedIn = isset($_SESSION['user']);
			$controller = $loggedIn ? "arcade" : "account";
		
			$content = '<img id="small-logo" class="small-logo" src="' . base_url()  . 'images/tank.svg" />';
			echo anchor($controller . "/index", $content, array("title" => "Tank Battle home"));
			
			$content = '<h2>Tank Battle</h2>';
			echo anchor($controller . "/index", $content, array("title" => "Tank Battle home", "id" => "appNameLink"));
		?>
	</div>
	<?php
		if ($loggedIn) {
			$this->load->view("general/user_dropdown");
		}
	?>
</header>