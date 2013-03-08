<script src="http://d3js.org/d3.v3.min.js"></script>
<p></p>
<script type="text/javascript">
var data = [4, 8, 15, 16, 23, 42];
var chart = d3.select("p").append("div")
    .attr("class", "chart");
chart.selectAll("div")
    .data(data)
  .enter().append("div")
    .style("width", function(d) { return d * 10 + "px"; })
    .text(function(d) { return d; });
</script>
<style type="text/css">
.chart div {
  font: 10px sans-serif;
  background-color: steelblue;
  text-align: right;
  padding: 3px;
  margin: 1px;
  color: white;
}
</style>