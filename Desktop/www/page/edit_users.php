<?php
if (!isset($_SESSION['loggedin'])) {
	header('location: logout.php');
	exit;
}
$error = "";
if (isset($_POST['Remove'])) {
	$id = $_POST['Remove'];
	PDO_Execute("DELETE FROM users WHERE id = '$id'");
}

if (isset($_POST['ChangePass'])) {
	if ($_POST['password'] != "") {
		$username = $_SESSION['username'];
		$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
		PDO_Execute("UPDATE users SET password = '$password' WHERE username = '$username'");
	} else {
		$error = '<div class="alert alert-warning">Enter a Password!</div>';
	}
}
if (isset($_POST['add'])) {
	if ($_POST['username'] != "" && $_POST['password'] != "" && $_POST['adminaccess'] != "" && $_POST['fullname'] != "") {
		$username = $_POST['username'];
		$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
		$adminaccess = $_POST['adminaccess'];
		$fullname = $_POST['fullname'];
		$contact = $_POST['contact'];
		$location = $_POST['location'];
		PDO_Execute(
			"INSERT INTO users (username,password,adminaccess,fullname,contact,location) 
			VALUES (:username,:password,:adminaccess,:fullname,:contact,:location)",
			array("username" => $username, "password" => $password, "adminaccess" => $adminaccess, "fullname" => $fullname, "contact" => $contact, "location" => $location)
		);
	} else {
		$error = '<div class="alert alert-warning">Enter some values!</div>';
	}
}
if (isset($_POST['update'])) {
	$id = $_POST['update'];
	$user = PDO_FetchRow("SELECT * FROM users WHERE id = :id", array("id" => "$id"));
	if ($user) {
		$password = $_POST['password'] ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password'];
		$adminaccess = $_POST['adminaccess'] ? $_POST['adminaccess'] : $user['adminaccess'];
		$fullname = $_POST['fullname'] ? $_POST['fullname'] : $user['fullname'];
		$contact = $_POST['contact'] ? $_POST['contact'] : $user['contact'];
		$location = $_POST['location'] ? $_POST['location'] : $user['location'];
		PDO_Execute("UPDATE users SET password = '$password', adminaccess = '$adminaccess', fullname = '$fullname',contact = '$contact',location = '$location' WHERE id = '$id'");
	} else {
		$_SESSION['success'] = "ERROR! User Not Found";
	}
}
$users = PDO_FetchAll("SELECT * FROM users");
include "page/header.php";

?>

<body>
	<main>
		<section class="container wrapper py-lg-5 sm" style="max-width: 700px;">
			<?= $error ?>
			<div class="text-center">
				<?php
				if ($_SESSION['adminaccess'] == "yes") {
					echo '<h4 class="display-4 pt-5">Edit Users</h4>';
				} else {
					echo '<h4 class="display-4 pt-5">ChangePass</h4>';
				}
				?>
			</div>
			<?php
			if ($_SESSION['adminaccess'] == "yes") {
			?> <form method="POST" id="myform">
					<div class="row">
						<div class="form-group col">
							<label>Username</label>
							<input type="text" name="username" id="username" class="form-control" require />
						</div>
						<div class="form-group col">
							<label>Password</label>
							<input type="password" name="password" class="form-control" />
						</div>
					</div>
					<div class="row">
						<div class="form-group col">
							<label>Fullname</label>
							<input type="text" name="fullname" id="fullname" class="form-control" />
						</div>
						<div class="form-group col">
							<label>Contact</label>
							<input type="text" name="contact" id="contact" class="form-control" maxlength="11" />
						</div>
					</div>
					<div class="row">
						<div class="form-group col">
							<label>Location</label>
							<input type="text" name="location" id="location" class="form-control" />
						</div>
						<div class="form-group col">
							<label>adminaccess</label>
							<select name="adminaccess" id="adminaccess" class="form-control">
								<option value="no">No</option>
								<option value="yes">Yes</option>
							</select>
						</div>
					</div>

					<div class="my-3 d-grid gap-2" id="button">
						<button class="btn btn-primary btn-block" name="add">Create Account</button>
						<a class="btn btn-secondary btn-block" href="index.php">Back</a>
					</div>
				</form>
				<div style="overflow-x:auto;">
					<table id="Active" class="table table-bordered table-hover">
						<thead>
							<tr>
								<th width=120>Name</th>
								<th>Fullname</th>
								<th>Admin</th>
								<th>contact</th>
								<th>location</th>
								<th width=140>Edit</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($users as $index => $users) : ?>
								<tr>
									<td><?= $users['username']; ?></td>
									<td><?= $users['fullname']; ?></td>
									<td><?= $users['adminaccess']; ?></td>
									<td><?= $users['contact']; ?></td>
									<td><?= $users['location']; ?></td>
									<td>
										<button class="btn btn-primary btn-sm text-center" onclick="edit('<?= $users['username'] ?>','<?= $users['fullname']; ?>','<?= $users['adminaccess']; ?>','<?= $users['contact']; ?>','<?= $users['location']; ?>','<?= $users['id']; ?>')">Edit</i></button>
										<button form="myform" class="btn btn-danger btn-block btn-sm" name="Remove" value="<?= $users['id']; ?>">Remove</button>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>

			<?php
			} else {
			?><form method="POST">
					<div class="form-group">
						<label>Password</label>
						<input type="password" name="password" class="form-control" />
					</div>
					<div class="my-3 d-grid gap-2">
						<button class="btn btn-primary btn-block" name="ChangePass"></span>Change Password</button>
						<a class="btn btn-secondary btn-block" href="index.php">Back</a>
					</div>
				</form>
			<?php
			}
			?>
		</section>
	</main>
</body>
<script src="src/js/jquery-3.6.1.min.js?ver=<?php echo rand(); ?>"></script>
<script>
	function edit(username, fullname, adminaccess, contact, location, id) {
		console.log(username + fullname + contact + location + adminaccess)
		$('#username').val(username);
		$('#fullname').val(fullname);
		$('#contact').val(contact);
		$('#location').val(location);
		$('#adminaccess').val(adminaccess);
		$('#button').html('<button form="myform" class="btn btn-success btn-block " value="' + id + '" name="update">Apply</button><button class="btn btn-secondary btn-block" onclick="clear()">Clear</button>');
	}

	function clear() {
		$('#myform').closest('form').find("input[type=text], textarea").val("");
		$('#button').html('<button class="btn btn-success btn-block " name="add">Add User</button><a class="btn btn-secondary btn-block" href="index.php">Back</a>');
	}
</script>

</html>