<?php

$pdo = new PDO('mysql:host=localhost;port=8889;dbname=Expenses', 
	'klogan', 'edie');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
function flashMessages() {
	if ( isset($_SESSION['error']) ) {
	echo('<p style="color:red">'.$_SESSION["error"]."</p>\n");
	unset($_SESSION['error']);
	}
	if ( isset($_SESSION['success']) ) {
		echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
		unset($_SESSION['success']);
	}
}

function validateExpense() {
	if ( strlen($_POST['price']) == 0 OR
		strlen($_POST['description']) == 0 ) {
		return 'Price and description are required';
		}
}

function deleteButton($row) {
	echo('<a href="#" onclick="return false"');
	echo('X'</a>);
}
	
?>

<?php

session_start();

// handle request to create a new expense
if ( isset($_POST['price']) OR isset($_POST['description']) ) {
	
	$msg = validateExpense();
	if ( is_string($msg) ) {
		$_SESSION['error'] = $msg;
		header("Location: index.php");
		return;
	}
	// if valid, continue adding
	
	$sql = "INSERT INTO Expenses (price, description)
		VALUES (:price, :description)";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array(
		':price' => intval($_POST['price']),
		':description' => $_POST['description']));

	$_SESSION['success'] = 'Expense Added';
	header( 'Location: index.php' );
	return;
}

// handle request to delete an expense 
if ( isset($_POST['delete']) && isset($_POST['delete_price']) && isset($_POST['delete_description']) ) {
	$sql = "DELETE FROM Expenses WHERE (price = :delete_price AND description = :delete_description)";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array(
		':price' => intval($_POST['delete_price']),
		':description' => $_POST['delete_description']));
	$_SESSION['success'] = 'Record deleted';
	header( 'Location: index.php' );
	return;
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
	<link rel="apple-touch-icon" href="icon.png">

	<title>Expense Tracker</title>
	
	<style>
		
		body {
			font-family: -apple-system;
			font-size: 20px;
			background-color: #817;
			color: #333;
			width: 100%;
			margin: 0;
		}
		header {
			/* width: 100%; */
			/* background-color: #eee; */
			text-align: center;
			color: #639;
		}
		header h1 {
			font-size: 3.5rem;
			line-height: 0;
			margin-top: 0.3em;}
		main {
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			width: 100%;
		}
		table {
			width: 90%;
			border-radius: 10px;
			padding: 1em;
			background-color: #36b;
			color: white;
		}
		thead {
			font-weight: bold;
		}
		form {
			padding-bottom: 1em;
			text-align: center;
		}
		#wrapper {
			background-color: white;
			border-radius: 10px;
			padding-top: 5px;
			padding-bottom: 10px;
			margin: 10px;
		}
		input {
			border: 1px solid #36b;
			border-radius: 4px;
			width: 70vw;
			box-sizing: border-box;
			padding: 4px;
			font-size: 1.2em;
			margin-bottom: 3px;
		}
		input::-webkit-outer-spin-button,
		input::-webkit-inner-spin-button {
		  -webkit-appearance: none;
		}
		
		</style>
	
  </head>

<body>

<div id="wrapper">
	 
<header>
	
	<p>Total This Month:</p>
	<h1>
		<?php
		$res1 = $pdo->prepare("select sum(price) as monthlysum from Expenses where month(date) = month(current_date());");
		$res1->execute();
		while ($row = $res1->fetch(PDO::FETCH_ASSOC))
		{
		echo "$" . "$row[monthlysum]";
		}
		?>
	</h1>
	<hr>
</header>
<main>
	
	<?php flashMessages(); ?>
	
	<form method="post">
		<input type="tel" name="price" placeholder="Price">
		<input type="text" name="description" placeholder="Description">
		<input type="submit" value="Submit"/>
	</form>
	
	<table>
		<form method="post" id="deleteForm">
			<input type="hidden" value="delete">
	<?php
		echo('<thead>Current month purchases</thead>');
		$stmt = $pdo->query('select * from Expenses where month(date) = month(current_date()) order by date desc;');
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
			echo('<tr><td>');
			echo('$' . $row['price']);
			echo('</td><td>');
			echo($row['description']);
			echo('</td><td>');
			deleteButton($row);
			echo("</td></tr>\n");
		}

	?>
		</form>
	</table>
	
	

	

<table>
	<thead>Prior monthly totals</thead>
	<tr>
		<td>$3874</td>
		<td>December 2022</td>
	</tr>
	<tr>
		<td>$4538</td>
		<td>November 2022</td>
	</tr>
	<tr>
		<td>$4125</td>
		<td>October 2022</td>
	</tr>
</table>


</main>

</div>
</body>
</html>