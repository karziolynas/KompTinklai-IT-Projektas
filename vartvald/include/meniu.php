<html><head>
	<style>
		a:link, a:visited {
  		background-color: #1B4D3E;
  		color: white;
  		padding: 4px 15px;
  		text-align: center;
  		text-decoration: none;
  		display: inline-block;
}		
		a:hover, a:active {
  		background-color: #006A4E;
}
	</style>
</head></html>

<?php
// meniu.php  rodomas meniu pagal vartotojo rolę

if (!isset($_SESSION)) { header("Location: logout.php");exit;}
include("include/nustatymai.php");
$user=$_SESSION['user'];
$userlevel=$_SESSION['ulevel'];
$role="";
{foreach($user_roles as $x=>$x_value)
			      {if ($x_value == $userlevel) $role=$x;}
} 

     echo "<table width=100% border=\"0\" cellspacing=\"1\" cellpadding=\"3\" class=\"meniu\">";
        echo "<tr><td>";
        echo "Prisijungęs vartotojas: <b>".$user."</b>";
        echo "</td></tr><tr><td>";
		if($_SESSION['user'] == "guest") {
			echo "<a href=\"register.php\">Registracija</a> &nbsp;&nbsp;";
		}
        if ($userlevel >= 5) {
			echo "<a href=\"useredit.php\">Redaguoti paskyrą</a> &nbsp;&nbsp;";
			echo "<a href=\"aukcioniuIstorija.php\">Mano aukcionai</a> &nbsp;&nbsp;";
			echo "<a href=\"aukcionuDalyvavimas.php\">Sekami aukcionai</a> &nbsp;&nbsp;";
			echo "<a href=\"operacija1.php\">Įkelti prekę į aukcioną</a> &nbsp;&nbsp;";
			echo "<a href=\"operacija2.php\">Prekių peržiūra</a> &nbsp;&nbsp;";}
     //Trečia operacija galima tik aukštesnių userlevel vartotojams , čia >=5:
        if ($userlevel >5 ) {
            echo "<a href=\"operacija3.php\">Sistemos aukcionai</a> &nbsp;&nbsp;";
       		}   
        //Administratoriaus sąsaja rodoma tik administratoriui
        if ($userlevel == $user_roles[ADMIN_LEVEL] ) {
            echo "<a href=\"admin.php\">Administratoriaus sąsaja</a> &nbsp;&nbsp;";
        }
        echo "<a href=\"logout.php\">Atsijungti</a>";
      echo "</td></tr></table>";
?>       
