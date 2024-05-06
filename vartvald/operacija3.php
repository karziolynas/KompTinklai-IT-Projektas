<?php
// operacija3.php  Parodoma registruotų vartotojų lentelė

session_start();
if (!isset($_SESSION['prev']) || ($_SESSION['prev'] != "index"))
{ header("Location: logout.php");exit;}

?>
<html>
    <head>
		<style>
				.isolated-component {
		 all: revert;
		}	
		</style>
        <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8">
        <title>Administratoriaus operacija</title>
        <link href="include/styles.css" rel="stylesheet" type="text/css" >
    </head>
    <body>
        <table class="center" ><tr><td>
            <center><img src="include/top.png"></center>
        </td></tr><tr><td> 
 <?php
		include("include/meniu.php"); //įterpiamas meniu pagal vartotojo rolę
 			$db=mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
			$sql = "SELECT prekesID, pavadinimas, pabaigosData, NaudotojoID, PradineKaina FROM Preke ORDER BY pabaigosData DESC";
			$result = mysqli_query($db, $sql);
			if (!$result || (mysqli_num_rows($result) < 1))  
			{echo "Klaida skaitant lentelę users"; exit;}
 ?> 
			
		</table>
        <center><font size="5">Sistemos aukcionų lentelė:</font></center><br>
		
    <table class="center" border="1" cellspacing="0" cellpadding="3">
    <tr><td><b>Aukciono ID</b></td><td><b>Pardavėjas</b></td><td><b>Prekės pavadinimas</b></td><td><b>PabaigosData</b></td>
		<td><b>Kam parduota</b></td></tr>
<?php
        while($row = mysqli_fetch_assoc($result)) 
	{	 
		$BiggestBidding = $row['PradineKaina'];
		$sqlHighestBid = "SELECT MAX(NaujaKaina) FROM kainosPastatymas WHERE prekesID='".$row['prekesID']."'";
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
			
			
		$sqlUsername = "SELECT username FROM users WHERE userid='".$row['NaudotojoID']."'";
		$resultUsername = mysqli_query($db, $sqlUsername);
		$username = mysqli_fetch_assoc($resultUsername);
			
	  	$auctionID = $row['prekesID'];
		$productName = $row['pavadinimas'];
		$endDate = $row['pabaigosData'];
		echo "<tr><td>".$auctionID. "</td><td>"; 
		echo "<a style='all:revert' href='userRatings.php?userID=".$row['NaudotojoID']."'>".$username['username']."</a></td><td>";
		echo "<a style='all:revert' href='auction.php?auctionId=".$auctionID."'>".$productName."</a></td><td>";
		echo $endDate."</td><td>"; 
		echo $HighestBidder."</td></tr>"; 
      		
	}
 ?>
	  </table>
  </body></html>
