<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
  google.load("visualization", "1", {packages:["corechart"]});
  google.setOnLoadCallback(drawChart);
  function drawChart() {
    var data = google.visualization.arrayToDataTable([
      ['Day', 'AQE 1', 'AQE 2', 'AQE 3'],
      ['2012-10-11',  20, 21, 20],
      ['2012-10-12',  10, 15, 30],
      ['2012-10-13',  20, 25, 30],
      ['2012-10-14',  10, 15, 20],
      ['2012-10-15',  15, 20, 40],
    ]);

    var options = {
      title: 'Temperature, °C',
	  backgroundColor:'none',
	  vAxis:{gridlines:{color:'999999'} },
	  lineWidth:2
	};

    var chart = new google.visualization.LineChart(document.getElementById('chart_div0'));
    chart.draw(data, options);

    /*-------------------*/

	var data = google.visualization.arrayToDataTable([
      ['Day', 'AQE 1', 'AQE 2', 'AQE 3'],
      ['2012-10-11',  50, 41, 20],
      ['2012-10-12',  50, 45, 30],
      ['2012-10-13',  50, 45, 30],
      ['2012-10-14',  50, 45, 20],
      ['2012-10-15',  55, 40, 40],
    ]);

    var options = {
      title: 'Humidity',
	  backgroundColor:'none',
	  vAxis:{gridlines:{color:'999999'} },
	  lineWidth:2
	};

    var chart = new google.visualization.LineChart(document.getElementById('chart_div1'));
    chart.draw(data, options);

	/*-------------------*/

	var data = google.visualization.arrayToDataTable([
      ['Day', 'AQE 1', 'AQE 2', 'AQE 3'],
      ['2012-10-11',  70000, 65200, 75100],
      ['2012-10-12',  71000, 65000, 75150],
      ['2012-10-13',  72000, 66600, 75200],
      ['2012-10-14',  73000, 65000, 75500],
      ['2012-10-15',  72000, 67500, 75600],
    ]);

    var options = {
    title: 'CO',
	  backgroundColor:'none',
	  vAxis:{gridlines:{color:'999999'} },
	  lineWidth:2,
	};

    var chart = new google.visualization.LineChart(document.getElementById('chart_div2'));
    chart.draw(data, options);

  }
</script>


<p>
<div id="chart_div0"></div>
<div id="chart_div1"></div>
<div id="chart_div2"></div>
</p>