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
$sqlBids = "SELECT * FROM kainosPastatymas WHERE klientoID='".$userid."'";
$result = mysqli_query($db, $sqlBids);
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
	<center><h2>Naudotojo sekami aukcionai: </h2></center>

<?php
	if(mysqli_num_rows($result)){
		echo "<table class='center' border='1' cellspacing='0' cellpadding='3'>";
    	echo "<tr><th><b>Prekės pavadinimas</b></th><th><b>Pradinė Kaina</b></th><th><b>Naudotojo Pastatyta kaina</b></th><th><b>Did.Pastatyta kaina</b></th>";
		echo "<th><b>Pabaigos Data</b></th><th><b>Ar Naudotojas yra pastatęs didžiausią sumą?</b></th></tr>";
    while($row = mysqli_fetch_assoc($result)) 
	{	 
		$sqlItem = "SELECT Pavadinimas, PradineKaina, pabaigosData FROM Preke WHERE PrekesID='".$row['prekesID']."'";
		if($resultItem = mysqli_query($db, $sqlItem)){
			$rowItem = mysqli_fetch_assoc($resultItem);
		}
		//if($rowItem['pabaigosData'] > date("Y-m-d"))
		//{
		$BiggestBidding = $rowItem['PradineKaina'];
		$sqlHighestBid = "SELECT MAX(NaujaKaina) FROM kainosPastatymas WHERE prekesID='".$row['prekesID']."'";
		if($resultBid = mysqli_query($db, $sqlHighestBid)){
			$highestBid = mysqli_fetch_assoc($resultBid);
				if(!empty($highestBid['MAX(NaujaKaina)'])){
		
					if($highestBid['MAX(NaujaKaina)'] > $BiggestBidding){
						$BiggestBidding = $highestBid['MAX(NaujaKaina)'];					
					}			
	   			}
		}
		
		echo "<tr><td>".$rowItem['Pavadinimas']. "</td><td>";    
		echo $rowItem['PradineKaina']."€ </td><td>";
		echo $row['NaujaKaina']."€ </td><td>"; 
		echo $BiggestBidding."€ </td><td>"; 
		echo $rowItem['pabaigosData']."</td><td>"; 
		
		if($BiggestBidding == $row['NaujaKaina']){
			echo "TAIP </td></tr>";}
		else {
			echo "NE </td></tr>";}
      		
	}
	}
	//}
	else {
		echo "<center><h2>Naudotojas neseka jokių aukcionų!</h2></center>";
	}
 ?>
</table>
	
</body>
</html>