// Themes begin
am4core.useTheme(am4themes_dataviz);
am4core.useTheme(am4themes_animated);
// Themes end
function createchart(temperature,puissance,isolation)
{
// Create chart instance
var chart = am4core.create("chartdivpred", am4charts.XYChart);
// Add data
chart.data = generateChartData(temperature,puissance,isolation);

// Create axes
var dateAxis = chart.xAxes.push(new am4charts.DateAxis());

// First axes (consommation)
var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

var series = chart.series.push(new am4charts.LineSeries());
series.dataFields.valueY = "value";
series.dataFields.dateX = "date";
series.strokeWidth = 2;
series.yAxis = valueAxis;
series.name = "Consommation";
series.tooltipText = "{name}: [bold]{valueY}[/]";
series.tensionX = 0.8;

var interfaceColors = new am4core.InterfaceColorSet();
var bullet = series.bullets.push(new am4charts.CircleBullet());
bullet.circle.stroke = interfaceColors.getFor("background");
bullet.circle.strokeWidth = 2;
valueAxis.renderer.line.strokeOpacity = 1;
valueAxis.renderer.line.strokeWidth = 2;
valueAxis.renderer.line.stroke = series.stroke;
valueAxis.renderer.labels.template.fill = series.stroke;
valueAxis.renderer.opposite = false;
valueAxis.renderer.grid.template.disabled = true;

 //second axe (prediction)
var valueAxis2 = chart.yAxes.push(new am4charts.ValueAxis());

var series2 = chart.series.push(new am4charts.LineSeries());
series2.dataFields.valueY = "prediction";
series2.dataFields.dateX = "date";
series2.strokeWidth = 2;
series2.yAxis = valueAxis;
series2.name = "Prédiction";
series2.tooltipText = "{name}: [bold]{valueY}[/]";
series2.tensionX = 0.8;

var interfaceColors = new am4core.InterfaceColorSet();
var bullet = series2.bullets.push(new am4charts.CircleBullet());
bullet.circle.stroke = interfaceColors.getFor("background");
bullet.circle.strokeWidth = 2;
valueAxis2.renderer.line.strokeOpacity = 0;
valueAxis2.renderer.line.strokeWidth = 2;
valueAxis2.renderer.line.stroke = series.stroke;
valueAxis2.renderer.labels.template.fill = series.stroke;
valueAxis2.renderer.opposite = false;
valueAxis2.renderer.grid.template.disabled = true;

// third axe (bar chart)
var degresAxis = chart.yAxes.push(new am4charts.ValueAxis());
degresAxis.title.text = "Degrès jour unifiés";
degresAxis.renderer.line.strokeOpacity = 0;
degresAxis.renderer.line.strokeWidth = 2;
degresAxis.renderer.line.stroke = series.stroke;
degresAxis.renderer.labels.template.fill = series.stroke;
degresAxis.renderer.opposite = true;
degresAxis.renderer.grid.template.disabled = true;

var degresSeries = chart.series.push(new am4charts.ColumnSeries());
degresSeries.dataFields.valueY = "degres";
degresSeries.dataFields.dateX = "date";
degresSeries.yAxis = degresAxis;
degresSeries.tooltipText = "{valueY} degrès jour unifiés";
degresSeries.name = "Degrès jour unifiés";
degresSeries.columns.template.fillOpacity = 0.7;
degresSeries.columns.template.propertyFields.strokeDasharray = "dashLength";
degresSeries.columns.template.propertyFields.fillOpacity = "alpha";
degresSeries.columns.template.propertyFields.stroke = "#2be32e"

var disatnceState = degresSeries.columns.template.states.create("hover");
disatnceState.properties.fillOpacity = 0.9;
// Add legend
chart.legend = new am4charts.Legend();

// Add cursor
chart.cursor = new am4charts.XYCursor();
chart.cursor = new am4charts.XYCursor();
chart.cursor.fullWidthLineX = true;
chart.cursor.xAxis = dateAxis;
chart.cursor.lineX.strokeOpacity = 0;
chart.cursor.lineX.fill = am4core.color("#000");
chart.cursor.lineX.fillOpacity = 0.1;

//export chart
chart.exporting.menu = new am4core.ExportMenu();
chart.exporting.menu.align = "right";
chart.exporting.menu.verticalAlign = "top";




}