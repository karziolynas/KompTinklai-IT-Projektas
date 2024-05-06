<?php

include("include/nustatymai.php");
$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);  
if ($db->connect_error) {  
 	die("Connection failed: " . $db->connect_error);  
}

session_start();
if (!isset($_SESSION['prev']) || ($_SESSION['prev'] != "index"))
{ header("Location:logout.php");exit;}

$auctionId = $_GET['auctionId'];

$sql = "SELECT * FROM Preke WHERE PrekesID=".$auctionId;
$resultProduct = mysqli_query($db, $sql);
if($resultProduct){
	$row = mysqli_fetch_assoc($resultProduct);
	$creatorID = $row['NaudotojoID'];
	$sqlPhoto = "SELECT nuotrauk FROM Nuotrauka WHERE PrekesID='".$auctionId."'";
	if($resultPhoto = mysqli_query($db, $sqlPhoto)){
		$photoData = mysqli_fetch_assoc($resultPhoto);
	}
	$sqlAuctionCreator = "SELECT username FROM users WHERE userid='".$row['NaudotojoID']."'";
	if($resultAuctionCreator = mysqli_query($db, $sqlAuctionCreator)){
		$creatorData = mysqli_fetch_assoc($resultAuctionCreator);
		$creatorUsername = $creatorData['username'];
	}
}
$date = $row['pabaigosData'];
$time = $row['pabaigosLaikas'];
$combinedDateTime = date('Y-m-d H:i:s', strtotime("$date $time"));
//mysqli_free_result($resultPhoto);
//mysqli_free_result($resultProduct);
$Bid = "";
$BidErr = "";
$userid = $_SESSION['userid'];
if(isset($_POST["submit"])){
	if (empty($_POST["Bid"])) {
		$BidErr = "Nenurodyta kaina!";} 
	else {
		if(is_numeric($_POST["Bid"])) {
    		$Bid = stripslashes(trim($_POST["Bid"]));}
		else{ 
			$BidErr = "Nurodyta netinkamo formato kaina!";}
	}
	if($_POST["Bid"] < $row['PradineKaina']){
		$BidErr = "Statoma kaina negali būti mažesnė už jau didžiausią pastatytą kainą!";
	}
	else{
	$sqlBid = "INSERT INTO kainosPastatymas (klientoID, prekesID, NaujaKaina) VALUES ('$userid', '$auctionId', '$Bid')";
	mysqli_query($db, $sqlBid);}
}

$HighestBidderID = "";
$BiggestBidding = $row['PradineKaina'];
$sqlHighestBid = "SELECT MAX(NaujaKaina) FROM kainosPastatymas WHERE prekesID='".$auctionId."'";
if($resultBid = mysqli_query($db, $sqlHighestBid)){
	$highestBid = mysqli_fetch_assoc($resultBid);
	if(!empty($highestBid['MAX(NaujaKaina)'])){
		
		if($highestBid['MAX(NaujaKaina)'] > $BiggestBidding){
			$BiggestBidding = $highestBid['MAX(NaujaKaina)'];
			$sqlClientID = "SELECT klientoID FROM kainosPastatymas WHERE NaujaKaina='".$BiggestBidding."'";
			if($resultID = mysqli_query($db, $sqlClientID)){
				$highestID = mysqli_fetch_assoc($resultID);
				$HighestBidderID = $highestID['klientoID'];
			}
		}
	   }
}
//mysqli_free_result($resultBid);
$HighestBidderName = "Pirkėjų nėra";
$sqlClientName = "SELECT username FROM users WHERE userid='".$HighestBidderID."'";
if($resultClient = mysqli_query($db, $sqlClientName)){
	$ClientUsername = mysqli_fetch_assoc($resultClient);
	$HighestBidderName = $ClientUsername['username'];
	
	
}

$CommentErr =  "";
if(isset($_POST["commentSubmit"])){
	if (empty($_POST["comment"])){
		$CommentErr = "Komentaras tuščias!";
	}
	else{
		$Comment = $_POST["comment"];
		$sqlComment = "INSERT INTO komentarai (tekstas, klientoID, prekesID) VALUES ('$Comment', '$userid', '$auctionId')";
		mysqli_query($db, $sqlComment);
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
		#element1 {display:inline-block;margin-right} 
		#element2 {display:inline-block;margin-left:110px} 
        body {
            font-family: Arial, Helvetica, sans-serif;;
            margin: 0;
            padding: 0;
            background-color: #FFFFFF
        }
		header {
            background-color: #04AA6D;
            color: #fff;
            text-align: center;
            padding: 1rem;
        }
		.auction-item {
            width: 80%;
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 5px;
        }
		.auction-item img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }
		.auction-item h2 {
            color: #333;
        }

        .auction-item p {
            color: #666;
        }
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
		.container{
  			max-width: 600px;
 			 margin: 0 auto;
			}
    </style>
</head>
<body>
			Atgal į [<a href="operacija2.php">Aukcioną</a>]
        <center><img src="include/top.png"></center>

    <div class="auction-item">
		<center>
		<img alt="Prekė nuotraukos neturi" src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($photoData['nuotrauk']);?>"
		style="width:300px; height:300px;"/><br><br></center>
		
        <div id='element1'><h2><?php echo $row['Pavadinimas']?></h2> </div>
		<div id='element2'><p>Prekę į aukcioną įkėlė:
			<a href="userRatings.php?userID=<?php echo $creatorID ?>"><?php echo"$creatorUsername"?></a></p></div>
        <p><?php echo $row['Aprasas']?></p>
        <p>Pradinė kaina: <?php echo $row['PradineKaina']?> €</p>
		<p>Didžiausia pastatyta kaina: <?php echo $BiggestBidding?> €   &emsp;&emsp;&emsp;&emsp;Priklauso - <?php echo $HighestBidderName?></p>
		<p>Aukciono pabaiga: <?php echo $row['pabaigosData']?> &emsp; <?php echo $row['pabaigosLaikas']?>     </p>
		<h2>Liko laiko:</h2><p id="laikas"></p>
    	
		<script>
   		var countDownDate = <?php echo strtotime($combinedDateTime) ?> * 1000;
    	var now = <?php echo time() ?> * 1000;
    	var x = setInterval(function() {
        now = now + 1000;
        var distance = countDownDate - now;
		var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);
        document.getElementById("laikas").innerHTML = days + "d " + hours + "h " +
            minutes + "m " + seconds + "s ";
		if (distance < 0) {
            clearInterval(x);
            document.getElementById("laikas").innerHTML = "AUKCIONAS BAIGĖSI!";
        	}
    	}, 1000);
    	</script>
		
		<?php if($creatorID != $_SESSION['userid']) {?>
		<h2>Pastatyti kainą: </h2>
		
		<form method="post" enctype="multipart/form-data" action="" >
    	<input type="text" id="Bid" name="Bid" placeholder="0.00" style="width:25%">
			<span class="error">* <?php echo $BidErr;?></span><br> 
        <input type="submit" name="submit" value="Pastatyti kainą">
		</form>
		<?php } ?>
    </div>
	<br><br><br><br>
	
	<form method="post" enctype="multipart/form-data" action="" >
	<div class="auction-item">	
		<h2>Komentarai </h2>
		<?php 
			$sqlComments = "SELECT tekstas, klientoID, data FROM komentarai WHERE prekesID='".$auctionId."'";
			if($resultComments = mysqli_query($db, $sqlComments)){
				while($rowComments = mysqli_fetch_assoc($resultComments)){
					$sqlUserID = "SELECT username FROM users WHERE userid='".$rowComments['klientoID']."'";
					$resultUserID = mysqli_query($db, $sqlUserID);
					$userIDRow = mysqli_fetch_assoc($resultUserID);
					$username = $userIDRow['username'];
					echo "<div class='auction-item'><b>".$username."</b> pakomentavo:  <br>";
					echo $rowComments['tekstas'];
					echo "</div><br><br>";
				}
			}
		?>
		
		<div class="form-group">
      	<p style="text-align: left;">Rašyti komentarą:</p>
      	<input type="text" id="comment" name="comment" placeholder="Rašyti savo komentarą čia..." style="width:95%">
			<span class="error">* <?php echo $CommentErr;?></span><br>
    	</div>
    	<input type="submit" name="commentSubmit" value="Paskelbti">
	</div>
	</form>

	
	</body>
</html>
		
