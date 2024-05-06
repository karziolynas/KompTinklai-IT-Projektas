<?php
// operacija1.php
// skirta sukurti aukciono įrašą

include("include/nustatymai.php");
$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);  
if ($db->connect_error) {  
    die("Connection failed: " . $db->connect_error);  
}

session_start();
if (!isset($_SESSION['prev']) || ($_SESSION['prev'] != "index"))
{ header("Location:logout.php");exit;}


$PavadErr = $AprErr = $PradineKainErr = $dateErr = $timeErr = "";
$Pavad = $Apr = $PradineKain = $date = $time = "";
$status = $statusMsg = $statusMsg2 = ''; 
if(isset($_POST["submit"])){ 
    $status = 'error';
	
	if (empty($_POST["Pavadinimas"])) {
    	$PavadErr = "Nenurodytas prekės pavadinimas!";
		$Pavad = NULL;}
	else {  
		$Pavad = stripslashes(trim($_POST["Pavadinimas"]));}
	if (empty($_POST["Aprasas"])) {
    	$AprErr = "Nenurodytas prekės aprašas!";
		$Apr = NULL;} 
	else {
    	$Apr = stripslashes(trim($_POST["Aprasas"]));}

	$currentDate = date("Y-m-d");
	if(empty($_POST["Data"])){
		$dateErr = "Data nurodyta netinkama!";
	}
	else if($_POST["Data"] < $currentDate || ($_POST["Data"] == $currentDate && ($_POST["Laikas"] < date("H:i")))) {
		$dateErr = "Data nurodyta netinkama!";
	}
	else if(empty($_POST["Laikas"])){
		$timeErr = "Nurodytas netinkamas laikas";
	}
	else if ($_POST["Data"] >= $currentDate && ($_POST["Data"] == $currentDate && $_POST["Laikas"] < date("H:i"))){
		$timeErr = "Nurodytas netinkamas laikas!";
	}
	else{
		echo $_POST["Laikas"];
		echo date("H:i");
		$time = $_POST["Laikas"];
		$date = $_POST["Data"];
	}
	
	if (empty($_POST["PradineKaina"])) {
    	$PradineKainErr = "Nenurodyta pradinė kaina!";} 
	else {
		if(is_numeric($_POST["PradineKaina"])) {
    		$PradineKain = stripslashes(trim($_POST["PradineKaina"]));}
		else{ 
			$PradineKainErr = "Nenurodytas tinkamas skaičius!";}
	}
	
	if($Apr != NULL && $Pavad != NULL && $date != NULL){
		$userid = $_SESSION['userid'];
		$sql = "INSERT INTO ".TBL_ITEM. " (Pavadinimas, Aprasas, PradineKaina, NaudotojoID, pabaigosData, pabaigosLaikas)
		VALUES ('$Pavad', '$Apr', '$PradineKain', '$userid', '$date', '$time')";
		$insert = mysqli_query($db, $sql);
	
	if($insert){
		$status = 'success'; 
        $statusMsg = "Prekė įkelta į aukcioną.";}}
	else{ 
        $statusMsg = "Įkėlimas nepavyko, pabandykite dar kartą.";}
	
		//Paveikslėlio įkelimas į duombazę
	if(!empty($_FILES["image"]["name"])) { 
        $fileName = basename($_FILES["image"]["name"]); 
        $fileType = pathinfo($fileName, PATHINFO_EXTENSION); 
		
		$allowedTypes = array('jpg','png','jpeg','gif');  //galimi įkelti failai 
        if(in_array($fileType, $allowedTypes)){ 	  // patikrinama ar keliamas failas yra tinkamo formato
            $image = $_FILES['image']['tmp_name'];       //nuskaitomi duomenys ir įkeliama į duomenų bazę
            $imgContent = addslashes(file_get_contents($image)); 
			$lastSql = "SELECT PrekesID FROM Preke ORDER BY PrekesID DESC LIMIT 1";
			$select = mysqli_query($db, $lastSql);
			$priceid = mysqli_fetch_assoc($select);
			$id = $priceid['PrekesID']; 
			$sql = "INSERT INTO Nuotrauka (IkelimoData, nuotrauk, PrekesID) VALUES (NOW(), '$imgContent', '$id')";
			$insert = mysqli_query($db, $sql);
			
			if($insert){ 
                $status = 'success'; 
                $statusMsg2 = "Nuotrauka įkelta sėkmingai.";}
			else{ 
                $statusMsg2 = "Įkėlimas nepavyko, pabandykite dar kartą.";}  
		}else{ 
            $statusMsg2 = 'Netinkamas failo formatas.'; } 
	}
	else{ 
        $statusMsg2 = 'Pasirinkite nuotrauką įkelimui.';}
	
	
}
echo $statusMsg;
echo $statusMsg2;

?>

<html>
    <head>
		<style>
			body {font-family: Arial, Helvetica, sans-serif;}
			* {box-sizing: border-box;}

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
			.container {
			  border-radius: 5px;
			  background-color: #f2f2f2;
			  padding: 20px;
			}
		</style>
		
        <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
        <title>Aukcionas</title>
        <link href="include/styles.css" rel="stylesheet" type="text/css" >
    </head>
    <body>
    <table style="border-width: 2px; border-style: dotted;"><tr><td>
         Atgal į [<a href="index.php">Pradžia</a>]
      </td></tr>
	</table><br>
			
		<div style="text-align: center;color:green">
            <h1>Kurti aukciono įrašą</h1>
			<div class="container">
  			<form method="post" enctype="multipart/form-data" action="" >
    			<label for="Pavadinimas">Prekės pavadinimas</label><br>
    			<input type="text" id="Pavadinimas" name="Pavadinimas" placeholder="Jūsų prekė..">
				<span class="error">* <?php echo $PavadErr;?></span><br><br>

    			<label for="Aprasas">Prekės aprašas</label><br>
    			<textarea id="Aprasas" name="Aprasas" placeholder="Aprašas..." style="height:50px"></textarea>
				<span class="error">* <?php echo $AprErr;?></span><br><br>
				
				<label for="PradineKaina">Pradinė prekės kaina</label><br>
    			<input type="text" id="PradineKaina" name="PradineKaina" placeholder="0.00" style="width:5%">
				<span class="error">* <?php echo $PradineKainErr;?></span><br>
				
				<br>
   				<label>Pasirinkite nuotraukos failą:</label>
    			<input type="file" name="image"><br>
				<br><br>
				
				<p>Pasirinkite aukcionio pabaigos datą ir laiką</p>
				<input type="date" id="Data" name="Data">
				<span class="error">* <?php echo $dateErr;?></span><br>
				
				<input type="time" id="Laikas" name="Laikas">
				<span class="error">* <?php echo $timeErr;?></span><br>
				
				<br><br>
				<input type="submit" name="submit" value="Paleisti aukcioną">
				<br>
    			
				
  			</form>
			</div>
   </div><br>
</body>
</html>