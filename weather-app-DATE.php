<?php

$link = mysqli_connect("127.0.0.1", "admin", "cukVMx97dbdR", "weather"); //Aktualne dane
$query = "SELECT id, TIME(date) as dbTime, tmp, pre, hum FROM temp WHERE date > CURRENT_DATE ORDER BY id DESC LIMIT 48;  ";
$result = mysqli_query($link, $query);

while ($db = mysqli_fetch_array($result)){
  $pieces = explode(':', $db['dbTime']);
	$formattedTime = $pieces[0] . ':' . $pieces[1];
  $timestampsTime[] = $formattedTime;
	$temperatures[] = $db['tmp'];
	$pressure[] = $db['pre'];
	$humidity[] = $db['hum'];
}

$new_date = date('Y-m-d', strtotime($_POST['dateFrom'])); //Data z guziczków
if ($new_date == date('Y-m-d',strtotime("1970-01-01"))){
  $new_date = date('Y-m-d');
}

//Dzień z guziczków średnia
$lastday = "SELECT round(avg(tmp),2)as tmp,round(avg(hum),2)as hum,round(avg(pre),2)as pre FROM temp WHERE date BETWEEN '$new_date' AND date_add('$new_date', interval 1 day); ";
//Dzień z guziczków wykresik
$lastdaygraph = "SELECT id, TIME(date) as dbTime, tmp, pre, hum FROM temp WHERE date BETWEEN '$new_date' AND date_add('$new_date', interval 1 day )AND id%2=0 ORDER BY id DESC; ";
//mysql z zmiennymi do sredniej
$resultold = mysqli_query($link, $lastday);
while ($db = mysqli_fetch_array($resultold)){
  $tmpold[] = $db['tmp'];
	$preold[] = $db['pre'];
	$humold[] = $db['hum'];
}
//mysql z zmiennymi do wykresu
$resultoldgraph = mysqli_query($link, $lastdaygraph);
while ($db = mysqli_fetch_array($resultoldgraph)){
  $piecesg = explode(':', $db['dbTime']);
	$formattedTimeg = $piecesg[0] . ':00';
  $timestampsTimeg[] = $formattedTimeg;
  $tmpoldg[] = $db['tmp'];
	$preoldg[] = $db['pre'];
	$humoldg[] = $db['hum'];
}
?>

<!DOCTYPE HTML>
<style>
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap');
body{
    background-color: #F3F3F3;
    font-family: Roboto;
    margin: 0px;
}
.main{
    display: flex;
    flex-direction: column;
    justify-content: center;
    
}
.navbar {
  display: flex;
  justify-content: center;
  list-style-type: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
  background-color: #333;

}

a {
  display: block;
  color: white;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
}

a:hover:not(.active) {
  background-color: white;
  color: black;
  transition: 0.5s;
  transition-timing-function: linear;
}

.active {
  background-color: #4408ca;
}
.aktualne{
    display: flex;
    color: white;
    padding: 1vw;
    flex-direction: column;
    border-radius: 5px;
    align-items: center;
    align-self: center;
    background-color: #4408ca;
    width: 40vw;
    margin: 10px;
}
.zmiana{
    display: flex;
    color: white;
    padding: 1vw;
    flex-direction: column;
    border-radius: 5px;
    align-items: center;
    align-self: center;
    background-color: #4408ca;
    width: 40vw;
    margin: 10px;
}

h2{
    margin:1vw;
}
.dane{
align-self:center;
  width:80%;
  overflow: scroll;
}
.menu{
  align-self: center;
padding: 10px;
display: flex;
flex-direction: column;
align-items: center;
background-color: blueviolet;
border-radius: 10px;
color: white;
}
.filter{
  align-items: center;
display: flex;
flex-direction: row;
}
button{
  margin-left: 5px;
margin-right: 5px;
padding: 10px;
background: 0;
color: white;
border: 2px solid #dfa7a7;
border-radius: 11px;
box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);
}
button:hover {background-color: rgb(95, 28, 158);}
button:active {
  background-color: rgb(95, 28, 158);
  box-shadow: 0 5px #666;
  transform: translateY(2px);
  transition: 0.1s;
}
@media screen and (max-width: 1000px) {
    .zmiana{
        width: 90%;
    }
    .aktualne{
        width: 90%;
    }
    h2{
        margin:5vw;
    }
    .dane{
      overflow: scroll;
      width:95%;
    }
    .tem{
      width:100%;
    }
}
.tempt{
    background-color: #F3F3F3;
}

</style> 
<html>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
</head>

<body>
<div class="main">
<div class="navbar">
<a class="active" href="weather-app-DATE.php">Aktualne</a>
<a href="weather-app.php">Tydzień</a>
<a href="#contact">Miesiąc</a>

</div>
<div class="aktualne">
    <h2>Warunki Aktualne:</h2>
<table>
  <tr>
    <td width='200'><b>Temperatura:</b></td><td><?php echo round($temperatures[0], 1); ?> &deg;C</td>
  </tr>
  <tr>
    <td><b>Ciśnienie:</b> </td><td><?php echo round($pressure[0], 2); ?> kPa 
  </tr>
  <tr>
    <td><b>Wilgotność:</b></td><td><?php echo round($humidity[0], 1); ?> %</td>
  </tr>
</table>
<h6>aktualizacja: <?php echo $timestampsTime[0];?> </h6>
</div>

<div class="zmiana">
    <h2>Średnia z dnia:</h2>
    <?php echo $new_date?>
    <table>
  <tr>
    <td width='200'><b>Temperatura:</b></td><td><?php echo round($tmpold[0], 1); ?> &deg;C</td>
  </tr>
  <tr>
    <td><b>Ciśnienie:</b> </td><td><?php echo round($preold[0], 2); ?> kPa 
  </tr>
  <tr>
    <td><b>Wilgotność:</b></td><td><?php echo round($humold[0], 1); ?> %</td>
  </tr>
</table>
<br>
</div>

<div class="menu">
  <div>
      Wybierz datę:
  </div>
  <form class="filter" name="Filter" method="POST">
    <button type="submit" name="dateFrom" value="<?php echo ($new_date=date('d-m-Y', strtotime('-1 day', strtotime($new_date)))); ?>"> <</button>
    <?php echo$new_date=date('d-m-Y', strtotime($new_date.'+1 day'))?>
    <button type="submit" name="dateFrom" value="<?php echo ($new_date=date('d-m-Y', strtotime($new_date.'+1 day'))); ?>"> ></button>
  </form>
</div>

<div class="dane">
  <div class="tem">
    <canvas id="temp"></canvas>
    <canvas id="hum"></canvas>
  </div>
</div>

<script>
var xValues = <?php echo json_encode($timestampsTimeg);?>;
var yValues = <?php echo json_encode($tmpoldg);?>;
var hum = <?php echo json_encode($humoldg);?>;

new Chart("temp", {
  type: "bar",
  data: {
    labels: xValues,
    datasets: [{
      label: 'Temperatura',
      fill: false,
      lineTension: 0,
      backgroundColor: "rgba(0,0,255,0.8)",
      borderColor: "rgba(0,0,255,0.1)",
      data: yValues
    }]
  },
  options: {
    legend: {display: true},
    scales: {
      yAxes: [{ticks: {callback:function(value,index,values){return value+'°C'}}}],
      xAxes: [{ticks:{reverse:true}}],
    }
  }
});

new Chart("hum", {
  type: "line",
  data: {
    labels: xValues,
    datasets: [{
      label: 'Wilgotność',
      fill: false,
      lineTension: 0,
      backgroundColor: "rgba(37, 203, 145, 0.9)",
      borderColor: "rgba(37, 203, 145, 0.1)",
      data: hum
    }]
  },
  options: {
    legend: {display: true},
    scales: {
      yAxes: [{ticks: {callback:function(value,index,values){return value+'%'}}}],
      xAxes: [{ticks:{reverse:true}}],
    },
    responsive:true,
  }
});

</script>
</body>
</html>