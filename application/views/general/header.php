<link rel="stylesheet" href="<?=base_url() ?>css/header.css" />

<header>
	<?php
		$content = '<img id="small-logo" class="small-logo" src="' . base_url()  . 'images/tank.svg" />';
		echo anchor("account/index", $content, array("title" => "Tank Battle home"));
		
		$content = '<h2>Tank Battle</h2>';
		echo anchor("account/index", $content, array("title" => "Tank Battle home"));
	?>
</header>