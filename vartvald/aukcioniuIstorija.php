<?php

include("include/nustatymai.php");
$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);  
if ($db->connect_error) {  
    die("Connection failed: " . $db->connect_error);  
}

session_start();
if (!isset($_SESSION['prev']) || ($_SESSION['prev'] != "index"))
{ header("Location:logout.php");exit;}

$userid = $_SESSION['userid'];
$sqlUser = "SELECT * FROM Preke WHERE NaudotojoID='".$userid."'";
$result = mysqli_query($db, $sqlUser);


?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Naudotojo aukcionai</title>
    <style>
		body {font-family: Arial, Helvetica, sans-serif;}
	table {
	margin-left:auto; 
    margin-right:auto;
    margin-bottom: 0px;
    margin-top: 0px;
    border-width: 3px;
    border-style: solid;
    border-color: grey;
    padding: 1px;
    width: 90%;
    border-collapse: collapse;
	}

	th, td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
	}

	th {
    background-color: #04AA6D;
	}

	tr:hover {
    background-color: #f5f5f5;
	}
    </style>
</head>
<body>
Atgal į [<a href="index.php"> Pradžią</a>]
     <center><img src="include/top.png"></center>
	<center><h2>Mano aukcionai: </h2></center>

<?php
	if(mysqli_num_rows($result)){
		echo "<table class='center' border='1' cellspacing='0' cellpadding='3'>";
    	echo "<tr><th><b>Prekės pavadinimas</b></th><th><b>Pradinė Kaina</b></th><th><b>Did.Pastatyta kaina</b></th>";
		echo "<th><b>Pabaigos Data</b></th><th><b>Pabaigos Laikas</b></th><th><b>Ar galioja?</b></th><th><b>Kam parduota?</b></th></tr>";
    while($row = mysqli_fetch_assoc($result)) 
	{	 
		$BiggestBidding = $row['PradineKaina'];
		$sqlHighestBid = "SELECT MAX(NaujaKaina) FROM kainosPastatymas WHERE prekesID='".$row['PrekesID']."'";
		if($resultBid = mysqli_query($db, $sqlHighestBid)){
			$highestBid = mysqli_fetch_assoc($resultBid);
				if(!empty($highestBid['MAX(NaujaKaina)'])){
		
					if($highestBid['MAX(NaujaKaina)'] > $BiggestBidding){
						$BiggestBidding = $highestBid['MAX(NaujaKaina)'];					
					}			
	   			}
		}
		$HighestBidder = "NIEKAM";
		$sqlHighestBiddder = "SELECT klientoID FROM kainosPastatymas WHERE NaujaKaina='".$BiggestBidding."'";
		$resultBidder = mysqli_query($db, $sqlHighestBiddder);
		if(mysqli_num_rows($resultBidder)){
			$highestBidder = mysqli_fetch_assoc($resultBidder);
			$sqlBidder = "SELECT username FROM users WHERE userid='".$highestBidder['klientoID']."'";
				if($resultBidder2 = mysqli_query($db, $sqlBidder)){		
					$highestBidder2 = mysqli_fetch_assoc($resultBidder2);
					$HighestBidder = $highestBidder2['username'];
	   			}
		}
		
		echo "<tr><td>".$row['Pavadinimas']. "</td><td>";    
		echo $row['PradineKaina']."€ </td><td>";
		echo $BiggestBidding."€ </td><td>"; 
		echo $row['pabaigosData']."</td><td>"; 
		echo $row['pabaigosLaikas']."</td><td>";
		
		if($row['pabaigosData'] >= date("Y-m-d") && $row['pabaigosLaikas'] > date("h:i:s")){
			echo "TAIP </td><td>";}
		else {
			echo "NE </td><td>";}
		echo $HighestBidder."</td></tr>";
      		
	}
	}
	else {
		echo "<center><h2>Naudotojas nėra įkėlęs aukciono į sistemą!</h2></center>";
	}
 ?>
</table>
	
</body>
</html>