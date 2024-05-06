<?php

include("include/nustatymai.php");
$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);  
if ($db->connect_error) {  
 	die("Connection failed: " . $db->connect_error);  
}

session_start();
if (!isset($_SESSION['prev']) || ($_SESSION['prev'] != "index"))
{ header("Location:logout.php");exit;}

$userID = $_GET['userID'];
$sqlUser = "SELECT * FROM users WHERE userid='".$userID."'";
$result = mysqli_query($db, $sqlUser);
if($result){
	$row = mysqli_fetch_assoc($result);
}

$soldItemCount = 0;
$sqlUserItems = "SELECT PrekesID from Preke WHERE NaudotojoID='".$userID."'";
$select = mysqli_query($db, $sqlUserItems);
if(mysqli_num_rows($select)){
	while($rowProductIDs = mysqli_fetch_assoc($select)){
		$sqlProductInBiddingTable = "SELECT * FROM kainosPastatymas WHERE prekesID='".$rowProductIDs['PrekesID']."'";
		$findProduct = mysqli_query($db, $sqlProductInBiddingTable);
		if(mysqli_num_rows($findProduct)) {
			$soldItemCount = $soldItemCount + 1;
		}
	}
}




$averageRating = 0;
$ratingErr = $commentErr = "";
$userRating = $_SESSION['userid'];

$sqlFindUser = "SELECT reitingaParasiusioID FROM Reitingai WHERE reitingaParasiusioID='".$userRating."'";
$find = mysqli_query($db, $sqlFindUser);
if(mysqli_num_rows($find)){
	if(isset($_POST["commentSubmit"]) && isset($_POST["rate"]) && !empty($_POST["comment"])){
		$rating = $_POST["rate"];
		$comment = $_POST["comment"];
		$sqlRating = "UPDATE Reitingai SET pardavejoID = '$userID', reitingaParasiusioID = '$userRating',
		reitingas ='$rating', tekstas = '$comment' WHERE reitingaParasiusioID='$userRating' AND pardavejoID='$userID' ";
		$insert = mysqli_query($db, $sqlRating);
	}
	else if(empty($_POST["comment"])){
		$commentErr="Atsiliepimas būtinas!";
	}
	else if(empty($_POST["rate"])){
		$ratingErr="Vertinimas būtinas, palikite žvaigždutę!";
	}
}
else {
	if(isset($_POST["commentSubmit"]) && isset($_POST["rate"]) && !empty($_POST["comment"])){
		$rating = $_POST["rate"];
		$comment = $_POST["comment"];
		$sqlRating = "INSERT INTO Reitingai (pardavejoID, reitingaParasiusioID, reitingas, tekstas)
		VALUES ('$userID', '$userRating', '$rating', '$comment')";
		$insert = mysqli_query($db, $sqlRating);
	}
	else if(empty($_POST["comment"])){
		$commentErr="Atsiliepimas būtinas!";
	}
	else if(empty($_POST["rate"])){
		$ratingErr="Vertinimas būtinas, palikite žvaigždutę!";
	}
}
?>

<html>
	<head>
	<link rel="stylesheet" type="text/css" href="star.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aukciono prekė</title>
		<style>
			body {
            font-family: Arial, Helvetica, sans-serif;;
            margin: 0;
            padding: 0;
            background-color: #FFFFFF
        	}
			.box {
            width: 80%;
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
			border-radius: 5px;}
						input[type=text], select, textarea {
			  width: 40%;
			  padding: 12px;
			  border: 1px solid #ccc;
			  border-radius: 4px;
			  box-sizing: border-box;
			  margin-top: 6px;
			  margin-bottom: 16px;
			  resize: vertical;
			}
			input[type=submit] {
			  background-color: #04AA6D;
			  color: white;
			  padding: 12px 20px;
			  border: none;
			  border-radius: 4px;
			  cursor: pointer;
			}

			input[type=submit]:hover {
			  background-color: #45a049;
			}
				
		</style>
	</head>
	
<body>	
			Atgal į [<a href="operacija2.php">Aukcioną</a>]
        <center><img src="include/top.png"></center>
	
<div class="box">
	<h2 >Pardavėjo - <?php echo $row['username'];?> atsiliepimai:</h2>
			<?php 
			$sqlComments = "SELECT tekstas, reitingaParasiusioID, data, reitingas FROM Reitingai WHERE pardavejoID='".$userID."'";
			if($resultComments = mysqli_query($db, $sqlComments)){
				if(mysqli_num_rows($resultComments)){
					while($rowComments = mysqli_fetch_assoc($resultComments)){
						$averageRating = $averageRating + $rowComments['reitingas'];
						$sqlUserID = "SELECT username FROM users WHERE userid='".$rowComments['reitingaParasiusioID']."'";
						$resultUserID = mysqli_query($db, $sqlUserID);
						$userIDRow = mysqli_fetch_assoc($resultUserID);
						$username = $userIDRow['username'];
						echo "<div class='box'><b>".$username."</b> ".$rowComments['reitingas']."★ pakomentavo:  <br>";
						echo $rowComments['tekstas'];
						echo "</div><br><br>";
					}
				}
				else{
					echo "Pardavėjas atsiliepimų neturi!";
				}
			}
			if($averageRating > 0){
				$averageRating = $averageRating / mysqli_num_rows($resultComments);
				echo "<p> Vidurkis:  ";	echo number_format($averageRating, 2, '.', ''); echo "★ </p>";
			}
			echo "<p>Naudotojas yra pardavęs - ".$soldItemCount." prekes </p>"
		 	?>
	<?php if($userID != $_SESSION['userid']){?>
	<h2>Įvertinkite pardavėją ir parašykite atsiliepimą:</h2>

<form method="post" enctype="multipart/form-data">
  <div class="rate">
  <input type="radio" id="star5" name="rate" value="5" />
    <label for="star5" title="text">5 stars</label>
    <input type="radio" id="star4" name="rate" value="4" />
    <label for="star4" title="text">4 stars</label>
    <input type="radio" id="star3" name="rate" value="3" />
    <label for="star3" title="text">3 stars</label>
    <input type="radio" id="star2" name="rate" value="2" />
    <label for="star2" title="text">2 stars</label>
    <input type="radio" id="star1" name="rate" value="1" />
    <label for="star1" title="text">1 star</label> 
	<span class="error"> <?php echo $ratingErr;?></span>
  </div>
	<br><br><br>
      <input type="text" id="comment" name="comment" placeholder="Rašyti savo atsiliepimą čia..." style="width:95%">
		<span class="error">* <?php echo $commentErr;?></span><br>
    	<input type="submit" name="commentSubmit" value="Paskelbti">
</form>
	<?php } ?>
</div>

</body>
</html>