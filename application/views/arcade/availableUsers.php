<table>
	<?php 
	if ($users) {
		foreach ($users as $user) {
			if ($user->id != $currentUser->id) {
				echo "<tr>";
					echo "<td>";
						echo anchor("arcade/invite?login=" . $user -> login, $user -> fullName());
					echo "<td>";
				echo "</tr>";
			}
		}
	}
	?>
</table>