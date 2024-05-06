<html><head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js">
     </script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js">
     </script>
<style>
#zinutes {
    font-family: "Trebuchet MS", Arial; border-collapse: collapse; width: 70%;
}
#zinutes td {
    border: 1px solid #ddd; padding: 8px;
}
#zinutes tr:nth-child(even){background-color: #f2f2f2;}
#zinutes tr:hover {background-color: #ddd;}
</style>
	</head>
	
<body>
	Atgal į [<a href="index.php">Pradžia</a>]
	<center><h3>Žinučių sistema</h3></center>
    <table style="margin: 0px auto;" id="zinutes">

<?php
		session_start();
$dbc=mysqli_connect('localhost','stud', 'stud','stud');
		if(!$dbc){
			die ("Negaliu prisijungti prie MySQL:"	.mysqli_error($dbc));
		}	
		if($_POST !=null){
	$vardas = $_SESSION['user']; 
	$epastas =$_SESSION['umail'];
	$kam = $_POST['kam'];
	$zinute = $_POST['zinute'];
			$ip=$_SERVER['REMOTE_ADDR'];
	$sql = "INSERT INTO phplab (vardas, epastas, gavejas, zinute, data,ip) VALUES ('$vardas', '$epastas','$kam', '$zinute',now(),'$ip')";
    if (!mysqli_query($dbc, $sql))  die ("Klaida įrašant:" .mysqli_error($dbc));
	}
			//  nuskaityti viska bei spausdinti 
	echo "<tr><td>Nr</td><td>Kas siuntė</td><td>Siuntėjo e-paštas</td><td>Gavėjas</td><td>Data  (IP)</td><td>Žinutė</td></tr>";
	$sql = "SELECT * FROM phplab";
    $result = mysqli_query($dbc, $sql);
	{while($row = mysqli_fetch_assoc($result))
		{
		echo "<tr><td>".$row['id']."</td><td>".$row['vardas']."</td><td>".$row['epastas']."</td><td>".$row['gavejas'].
			"</td><td>".$row['data']."(".$row['ip'].")"."</td><td>".$row['zinute']."</td></tr>";
		} 
    };
	echo "</table>";
?>
<br>
<center><h3>Įveskite naują žinutę</h3></center>		
<div class="container">
  <form method='post'>
     
	  <div class="form-group col-lg-4">
          <label for="kam" class="control-label">Kam skirta:</label>
		  
<?php
		  include("include/nustatymai.php");
		  $db=mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
			$sql = "SELECT username,userlevel,email "
            . "FROM " . TBL_USERS . " ORDER BY userlevel DESC,username";
			$result = mysqli_query($db, $sql);
			if (!$result || (mysqli_num_rows($result) < 1))  
			{echo "Klaida skaitant lentelę users"; exit;}
														   
		echo "<select name=\"kam\">";
		while ($row = mysqli_fetch_assoc($result)) 
			{$user= $row['username'];
			 echo "<option value=".$user.">".$user."</option>";
		    }
		echo "</select>";
?>		  
		           
     </div>
     <div class="form-group col-lg-12">
          <label for="zinute" class="control-label">Žinutė:</label>
          <textarea name='zinute' class="form-control input-sm"></textarea>
     </div>
     <div class="form-group col-lg-2">
         <input type='submit' name='ok' value='siųsti' class="btnbtn-default">
     </div>
	  
 </form>
</div>
</body>
