<?php
include("include/nustatymai.php");
$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);  
if ($db->connect_error) {  
    die("Connection failed: " . $db->connect_error);  
}

session_start();
if (!isset($_SESSION['prev']) || ($_SESSION['prev'] != "index"))
{ header("Location:logout.php");exit;}

?>

<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8">
        <title>Prekiu paieška</title>
        <link href="include/styles.css" rel="stylesheet" type="text/css" >
		
  		<link rel="stylesheet" href="http://www.w3schools.com/lib/w3.css">
 		<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  		<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		
    </head>
    <body>

      <table style="border-width: 2px; border-style: dotted;"><tr><td>
         Atgal į [<a href="index.php">Pradžia</a>]
      </td></tr></table>
			
		<div class="jumbotron text-center" style="color:green">
            <h1>Esamos prekės aukcione</h1>
        </div><br>

 <?php
		$sqlProduct = "SELECT * FROM Preke ORDER BY PrekesID ASC";
		$resultProduct = mysqli_query($db, $sqlProduct);
		if($resultProduct){
			while($rowProduct = mysqli_fetch_assoc($resultProduct)) {
				if($rowProduct['pabaigosData'] > date("Y-m-d") ||
				   ($rowProduct['pabaigosData'] == date("Y-m-d") && $rowProduct['pabaigosLaikas'] > date("h:i:s"))){
				$productID = $rowProduct['PrekesID'];
				$sqlPhoto = "SELECT nuotrauk FROM Nuotrauka WHERE PrekesID = ".$productID;
				$resultPhoto = mysqli_query($db, $sqlPhoto);
				$HighestBidderID = "";
				$BiggestBidding = $rowProduct['PradineKaina'];
				$sqlHighestBid = "SELECT MAX(NaujaKaina) FROM kainosPastatymas WHERE prekesID='".$productID."'";
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
				echo "<div class='container'>	<div class='row'>";
				?> 
		
				<div class="col-sm-4">
					<div class="w3-container w3-teal">
  						<h1><?php echo $rowProduct['Pavadinimas'] ?> </h1>
					</div>
					<?php if($row = mysqli_fetch_assoc($resultPhoto)){?>
            		<a href="auction.php?auctionId=<?php echo $productID ?>"><img alt="Prekė nuotraukos neturi"
							src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($row['nuotrauk']); ?>"
						 style="width:300px; height:300px;"/><br><br> <?php }?></a>
					<div class="w3-container w3-teal">
						<p style="font-size: 15px"> <?php echo $rowProduct['Aprasas']?> </p>
					</div>

					<div class="w3-container w3-teal">
  						<p style="font-size: 20px">Pradinė kaina: <?php echo $rowProduct['PradineKaina']?> € </p>
					</div> 
					<div class="w3-container w3-teal">
  						<p style="font-size: 20px">Pastatyta kaina: <?php echo $BiggestBidding?> € </p>
					</div> 
					<div class="w3-container w3-teal">
  						<p style="font-size: 20px"><b> <a href="auction.php?auctionId=<?php echo $productID ?>" style="color:white">Daugiau:</a></b></p>
					</div> <br>
				</div> 
        	
				
				<?php } } }
		echo"</div></div>"; ?>