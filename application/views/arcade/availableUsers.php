<link rel="stylesheet" href="<?=base_url() ?>css/arcade.css" />

<table>
	<?php 
		// want > 1 to exclude self
		if ($users && sizeof($users) > 1) {
			foreach ($users as $user) {
				if ($user->id != $currentUser->id) {
					echo "<tr>";
						echo "<td>";
							echo anchor("arcade/invite?login=" . $user -> login, $user -> fullName());
						echo "<td>";
					echo "</tr>";
				}
			}
		} else {
			echo "<div class='error'>No other players are currently logged in</div>";
		}
	?>
</table>