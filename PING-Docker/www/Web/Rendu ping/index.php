<?php
session_start();
if (isset($_SESSION['login']) && isset($_SESSION['pwd'])) {
} else {
    header('location: login.php');
}
?>
<!DOCTYPE html>
<html lang="fr">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Suivi Client</title>

        <!-- Bootstrap -->
        <link href="../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link href="../vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
        <!-- NProgress -->
        <link href="../vendors/nprogress/nprogress.css" rel="stylesheet">
        <!-- bootstrap-daterangepicker -->
        <link href="../vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
        <!-- bootstrap-datetimepicker -->
        <link href="../vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css" rel="stylesheet">
        <!-- Ion.RangeSlider -->
        <link href="../vendors/normalize-css/normalize.css" rel="stylesheet">
        <link href="../vendors/ion.rangeSlider/css/ion.rangeSlider.css" rel="stylesheet">
        <link href="../vendors/ion.rangeSlider/css/ion.rangeSlider.skinFlat.css" rel="stylesheet">
        <!-- Bootstrap Colorpicker -->
        <link href="../vendors/mjolnic-bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css" rel="stylesheet">

        <link href="../vendors/cropper/dist/cropper.min.css" rel="stylesheet">

        <!-- Amchart -->
        <link href="./css/Amchart/Char.css" rel="stylesheet">
        <!-- Custom Theme Style -->
        <link href="../build/css/custom.min.css" rel="stylesheet">
        <!-- Hide data received from PHP into html (cache) and size of dalkia image-->
        <style>
            #hide
            {
                display: none;
            }
            #parametertable
            {
                display: none;
            }
            #dalkia{
                width : 65% ;  
                height : 80% ; 
            }
            #KPI{
                width: 133%;
            }
            #data{
                display: none;
                
            }
            .col-md-2 {
                width: 50%;
                text-align: center;
            }
        </style>   
    </head>
    <body class="nav-md">
        <div class="container body">
            <div class="main_container">
                <div class="col-md-3 left_col">
                    <div class="left_col scroll-view">
                        <div class="navbar nav_title" style="border: 0;">
                            <a class="site_title"><img id="dalkia" src=".\images\logo_final_blanc.png"></a>
                        </div>
                        <div class="clearfix"></div>
                        <br />
                        <!-- sidebar menu -->
                        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                            <div class="menu_section">
                                <ul class="nav side-menu">
                                    <!-- Menu with all the client -->
                                    <?php

                                        require './id_bdd.php';


                                            // if ($dbconnect->connect_error) {
                                            // die("Database connection failed: " . $dbconnect->connect_error);
                                            // }

                                        $query = mysqli_query($dbconnect, "SELECT * FROM client;");
                                            //$query_1 = mysqli_query($dbconnect, "SELECT site.nom FROM client INNER JOIN site ON client_id = site_client_id  WHERE client_id =  ;") ;

                                            // or die (mysqli_error($dbconnect));
                                        $tableau = array();
                                        $tableau_2 = array();

                                        while ($row = mysqli_fetch_array($query)) {
                                                    // Dans un tableau 2 est affecté lid et le nom du client 
                                            $tableau[$row[0]] = $row[1];
                                        }
                                                /////// Verification des index
                                                ///Pour afficher en sous menu , je parcours dabord les id et pour chaque id de clients , je trouve le site assoccié
                                        $output = "";
                                        foreach ($tableau as $key => $value) {

                                            $query_1 = mysqli_query($dbconnect, "SELECT site.nom, site_id FROM client INNER JOIN site ON client_id = site_client_id  WHERE client_id = $key ;");
                                            echo (' <li><a><i class="fa fa-bar-chart-o"></i>' . $value . '<span class="fa fa-chevron-down"></span></a><ul class="nav child_menu">');
                                            while ($row_2 = mysqli_fetch_array($query_1)) {
                                                array_push($tableau_2, $row_2['nom']);
                                                echo ('<li><a onClick="window.location.hash = \'' . $row_2['site_id'] . '\';window.location.reload(true);"> ' . $row_2[0] . '</a></li>');
                                                $output = $output . $row_2['site_id'] . "," . $row_2[0] . "|";
                                            }
                                            echo ('</ul></li> ');
                                        }
                                        echo ('<div id=hide>' . $output . '</div>');
                                    ?>
                                </ul>
                            </div>
                        </div>
                        <!-- /sidebar menu -->

                        <!-- /menu footer buttons -->
                        <div class="sidebar-footer hidden-small">
                            <a data-toggle="tooltip" data-placement="top" title="Logout" href="login.php">
                                <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
                            </a>
                        </div>
                        <!-- /menu footer buttons -->
                    </div>
                </div>

                <!-- top navigation -->
                <div class="top_nav">
                    <div class="nav_menu">
                        <nav>
                            <div class="nav toggle">
                                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                            </div>
                        </nav>
                    </div>
                </div>
                <!-- /top navigation -->

                <!-- page content --> 

                <div class="right_col" role="main">
                    <div id="nom_site"></div> 
                    <!-- KPI Display -->
                    <div class="col-md-12">
                        <div class="">
                            <div class="x_content">
                                <div class="row">
                                    <div id="KPI">
                                        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                            <div class="tile-stats">
                                                <div class="icon"><i class="glyphicon glyphicon-fire"></i></div>
                                                <div class="count" id="totalprediction"></div>
                                                <h3>Consommation totale</h3>
                                                <p>Prédiction</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="KPI">
                                        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                        <div class="tile-stats">
                                                <div class="icon"><i class="glyphicon glyphicon-fire"></i></div>
                                                <div class="count" id="totalconsoN"></div>
                                                <h3>Consommation totale</h3>
                                                <p>Sur l'année en cours</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="KPI">
                                        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                            <div class="tile-stats">
                                                <div class="icon"><i class="glyphicon glyphicon-plane"></i></div>
                                                <div class="count" id="vol"></div>
                                                <h3>Aller retour Paris - New York économisé</h3>
                                                <p>Par rapport à une consomation équivalente au gaz sur l'année en cours</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="">
                            <div class="x_content">
                                <div class="row">
                                    <div id="KPI">
                                        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                            <div class="tile-stats">
                                                <div class="icon"><i class="glyphicon glyphicon-euro"></i></div>
                                                <div class="count" id="cout"></div>
                                                <h3>Coût total</h3>
                                                <p>Prédiction</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="KPI">
                                        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                            <div class="tile-stats">
                                                <div class="icon"><i class="glyphicon glyphicon-euro"></i></div>
                                                <div class="count" id="totalcoutN"></div>
                                                <h3>Coût total</h3>
                                                <p>Sur l'année en cours</p>
                                            </div>
                                        </div>
                                    </div><div id="KPI">
                                        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                            <div class="tile-stats">
                                                <div class="icon"><i class="glyphicon glyphicon-tree-deciduous"></i></div>
                                                <div class="count" id="arbre"></div>
                                                <h3>hectares de forêt plantés</h3>
                                                <p>Par rapport à une consommation équivalente au fioul sur l'année en cours</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /KPI Display -->
                    <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
                    <!-- Chart with amchart-->
                    <div class="row">
                        <div id="chartdivpred"></div>
                    </div>
                        <!-- /Chart with amchart-->
                    <script>
                        // Title integration in the site
                        var site_data = document.getElementById('hide').innerHTML; // set Title of the client
                        for(var i =0; i < site_data.split("|").length; i++){
                            if(site_data.split("|")[i].split(",")[0] == location.hash.split("#")[1]){
                                var html = "<h3>"+site_data.split("|")[i].split(",")[1]+"</h3>";
                                document.getElementById("nom_site").innerHTML = html;
                            }
                        }
                        
                    </script>
                    <div class="row">
                        <!-- form input knob for setting of the prediction -->
                        <div class="col-md-12">
                            <div class="x_panel">
                                <div class="x_title">
                                    <h2>Paramètre de la prediction</h2>
                                    <ul class="nav navbar-right panel_toolbox">
                                    </ul>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content">
                                <div class="col-md-2">
                                    <p>Scénario Météorologique</p>
                                    <br>
                                    <br>
                                    <button type="button" onclick="updatetemp(1);" class="btn btn-info">Froid</button>
                                    <button type="button" onclick="updatetemp(2);"class="btn btn-success">Stable</button>
                                    <button type="button" onclick="updatetemp(3);"class="btn btn-danger">Chaud</button>
                                </div>
                                <div class="col-md-2">
                                    <p>Pourcentage de variation de consommation par l'isolation ou modification de surface</p>
                                    <input class="knob" id="knob_iso" data-width="100" data-height="120" data-min="-100" data-max="100" data-displayPrevious=true data-fgColor="rgba(254,88,21,0.75)" value="0" data-step="1">
                                </div>
                                <div class="col-md-2">
                                    <p>Pourcentage de variation de R1</p>
                                    <input class="knob" id="knob_r1" data-width="100" data-height="120" data-min="-10" data-max="10" data-displayPrevious=true data-fgColor="rgba(254,88,21,0.75)" value="0" data-step="1">
                                </div>
                                <div class="col-md-2">
                                    <p>Pourcentage de variation de R2</p>
                                    <input class="knob" id="knob_r2" data-width="100" data-height="120" data-min="-10" data-max="10" data-displayPrevious=true data-fgColor="rgba(254,88,21,0.75)" value="0" data-step="1">
                                </div>
                                <!--
                                <div class="col-md-2">
                                    <input type="text" class="form-control has-feedback-left" id="inputSuccess1" placeholder="R1">
                                    <span class="fa fa-wrench form-control-feedback left" aria-hidden="true"></span>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control has-feedback-left" id="inputSuccess2" placeholder="R2">
                                    <span class="fa fa-wrench form-control-feedback left" aria-hidden="true"></span>
                                    <br>
                                    <br>
                                    <button type="submit" onclick="updateR1R2();"class="btn btn-success">Submit</button>
                                </div>
                                -->

                            </div>
                        </div>
                    </div>

                </div>
                    <!-- /form input knob -->
                </div> 
                <!-- table of setting to recover the prediction -->
                <div id = "parametertable"></div>  
                <!-- Information for the input knob needed  -->
                <div class="modal fade docs-cropped" id="getCroppedCanvasModal" aria-hidden="true" aria-labelledby="getCroppedCanvasTitle" role="dialog" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="getCroppedCanvasTitle">Cropped</h4>
                            </div>
                            <div class="modal-body"></div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <a class="btn btn-primary" id="download" href="javascript:void(0);" download="cropped.png">Download</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Information for the input knob needed  -->
            </div>
        </div>
        <!-- Cache -->
        <div id="data">
            <div id="temperature"></div>
            <div id="isolation"></div>
            <div id="r1r2"></div>
            <div id="r1"></div>
            <div id="r2"></div>
            <div id="temp_iso"></div>
            <div id="temp_r1"></div>
            <div id="temp_r2"></div>
        </div>
        <script>
        var r1r2 = 
                    <?php
                        require './id_bdd.php';
                        $query = mysqli_query($dbconnect, "SELECT site_id, annee, R1, R2 FROM R1R2;") or die(mysqli_error($dbconnect));
                        $output = "";
                        while ($row = mysqli_fetch_array($query)) {
                            $output = $output . $row['site_id'] . "," . $row['annee'] . "," . $row['R1'] .  ","  . $row['R2'] . "|"; //Again, do some operation, get the output.
                        }
                        echo json_encode($output, JSON_HEX_TAG);
                    ?>;
                    var d = new Date();
                    var thisyear = parseInt(d.getFullYear()) -2;
                //insertion into the KPI
                for( var i = 0 ; i< r1r2.split("|").length ;i++){
                        site_id = r1r2.split("|")[i].split(",")[0];
                        r1r2_annee = r1r2.split("|")[i].split(",")[1];
                        r1_value = r1r2.split("|")[i].split(",")[2];
                        r2_value = r1r2.split("|")[i].split(",")[3];
                        if((site_id == location.hash.split("#")[1]) && (r1r2_annee == thisyear)){
                            console.log(r1_value);
                            document.getElementById('r1').innerHTML = parseInt(r1_value);
                            document.getElementById('r2').innerHTML = parseInt(r2_value);                        }
                    }
        </script>

        <!-- Chart with Amchart-->
        <script src="./amchart/core.js"></script>
        <!-- Attention Internet to DL si intranet-->
        <script src="./amchart/charts.js"></script>
        <script src="./amchart/themes/material.js"></script>
        <script src="./amchart/themes/animated.js"></script>
        <script src="./amchart/themes/dataviz.js"></script>
        <script src="./js/Amchart/Chart.js"></script>
        <!-- JQuerry-->
        <script>  
            // var prediction default
            var temp = "Stable";
            var power = 1;
            var iso =0;
            // gathering all the prediction with all parameters
            var prediction_table = 
            <?php
                require './id_bdd.php';
                $query = mysqli_query($dbconnect, "SELECT * FROM parametre;") or die(mysqli_error($dbconnect));
                $output = "";
                while ($row = mysqli_fetch_array($query)) {
                    $output = $output . $row['parametre_id'] . "," . $row['scenario_meteo'] . "," . $row['puissance_souscrite'] . "," . $row['pourcentage_reduction_isolation'] . "|"; //Again, do some operation, get the output.   
                }
                echo json_encode($output, JSON_HEX_TAG);
            ?>;    
            document.getElementById('parametertable').innerHTML= prediction_table;
            // function to put data into the chart
            function generateChartData(temperature,puissance,isolation) {
                var chartData = [];
                var chartData1 = [];
                var chartData2 = [];
                var chartData3 = [];
                var paramettre_id;
                var prediction_totale=0;
                var totalconsoN=0;
                var totalconsoN1=0;
                var totalconsoN2=0;
                var coutN_value=0;
                // gather setting id
                var paramettertable = document.getElementById('parametertable').innerHTML;
                document.getElementById('temperature').innerHTML = temperature;
                document.getElementById('isolation').innerHTML = isolation;
                for(var i = 0 ; i < paramettertable.split("|").length;i++)
                {
                    if((paramettertable.split("|")[i].split(",")[1]==temperature) && (paramettertable.split("|")[i].split(",")[2]==puissance) && (paramettertable.split("|")[i].split(",")[3]==0))
                    {
                        paramettre_id = paramettertable.split("|")[i].split(",")[0];
                        
                    }
                }        
                // consommation data on SQL       
                var myData =
                    <?php
                        require './id_bdd.php';
                        $query = mysqli_query($dbconnect, "SELECT * FROM consommation;") or die(mysqli_error($dbconnect));
                        $output = "";
                        while ($row = mysqli_fetch_array($query)) {
                            $output = $output . $row['consommation_site_id'] . "," . $row['consommation_date_conso'] . "," . $row['kwh'] . "|"; //Again, do some operation, get the output.   
                        }
                        echo json_encode($output, JSON_HEX_TAG);
                    ?>;
                // prediction data on SQL
                var myDatapred =
                    <?php
                        require './id_bdd.php';
                        $request = "SELECT * FROM prediction WHERE parametre_id IN (SELECT parametre_id FROM parametre WHERE puissance_souscrite = 1 AND pourcentage_reduction_isolation = 0);";
                        $query = mysqli_query($dbconnect, $request) or die(mysqli_error($dbconnect));
                        $output = "";
                        while ($row = mysqli_fetch_array($query)) {
                            $output = $output . $row['prediction_site_id'] . "," . $row['parametre_id'] . "," . $row['prediction_date'] . "," . $row['prediction_value'] . "|"; //Again, do some operation, get the output.   
                        }
                        echo json_encode($output, JSON_HEX_TAG);
                    ?>;
                    var d = new Date();
                    var thisyear = parseInt(d.getFullYear()) -1;
                // insert consommation data
                var degresju =
                    <?php
                        require './id_bdd.php';
                        $query = mysqli_query($dbconnect, "SELECT * FROM degresjour_dju;") or die(mysqli_error($dbconnect));
                        $output = "";
                        while ($row = mysqli_fetch_array($query)) {
                            $output = $output . $row['dju_date'] . "," . $row['dju_valeur'] . "|"; //Again, do some operation, get the output.   
                        }
                        echo json_encode($output, JSON_HEX_TAG);
                    ?>;
                var first = 0;
                var fyear;
                for(var year = 2015 ; year< 2018;year++){
                    for( var i = 0 ; i<= myData.split("|").length -1;i++){
                        site_id = myData.split("|")[i].split(",")[0];
                        date = myData.split("|")[i].split(",")[1];
                        kwh = myData.split("|")[i].split(",")[2];
                        if(site_id == location.hash.split("#")[1]){
                            année = date.split(" ")[0].split("-")[0];
                            mois = date.split(" ")[0].split("-")[1];
                            jour = date.split(" ")[0].split("-")[2];
                            date = année + "-" + mois + "-" + jour
                            if (année == year){
                                if (first == 0)
                                {
                                    first =1;
                                    fyear=année;
                                }
                                if (année == parseInt(thisyear-1))
                                {
                                    totalconsoN = totalconsoN + parseInt(kwh);
                                }
                                if (année == parseInt(thisyear-2))
                                {
                                    totalconsoN1 = totalconsoN1 + parseInt(kwh);
                                }
                                if (année == parseInt(thisyear-3))
                                {
                                    totalconsoN2 = totalconsoN2 + parseInt(kwh);
                                }
                                chartData1.push({
                                    date: date,
                                    value: kwh,
                                });
                            }
                        }
                    }
                }
                for(var year = 2015 ; year< 2018;year++){
                    for( var i = 0 ; i<= degresju.split("|").length -1;i++){
                        date = degresju.split("|")[i].split(",")[0];
                        deg = degresju.split("|")[i].split(",")[1];
                        année = date.split(" ")[0].split("-")[0];
                        mois = date.split(" ")[0].split("-")[1];
                        jour = date.split(" ")[0].split("-")[2];
                        date = année + "-" + mois + "-" + jour
                        if (année >= fyear){
                            if (année == year){
                                chartData3.push({
                                    date: date,
                                    degres: deg
                                });
                            }
                        }
                    }
                }
                // insert prediction data on the chart
                var modifiso = (1-isolation)
                for(var year = 2018 ; year< 2020;year++){
                    for( var i = 0 ; i< myDatapred.split("|").length ;i++){
                        site_id = myDatapred.split("|")[i].split(",")[0];
                        parametre = myDatapred.split("|")[i].split(",")[1];
                        date = myDatapred.split("|")[i].split(",")[2];
                        pred = myDatapred.split("|")[i].split(",")[3]*modifiso;
                        if((site_id == location.hash.split("#")[1]) && (paramettre_id == parametre)){
                            année = date.split(" ")[0].split("-")[0];
                            mois = date.split(" ")[0].split("-")[1];
                            jour = date.split(" ")[0].split("-")[2];
                            date = année + "-" + mois + "-" + jour;
                            if (année == year){
                                if (année == thisyear)
                                {
                                    prediction_totale = prediction_totale + parseInt(pred);
                                }
                                
                                chartData2.push({
                                    date: date,
                                    prediction: pred 
                                    });
                            }
                        }
                    }
                }
                chartData = chartData1.concat(chartData2.concat(chartData3));
                coutN_value_r1=document.getElementById('r1').innerHTML ;
                coutN_value_r2=document.getElementById('r2').innerHTML ;
                perc_r1=document.getElementById('knob_r1').value ;
                perc_r2=document.getElementById('knob_r2').value ;
                coutN_value = 0+parseInt(coutN_value_r1)+parseInt(coutN_value_r2);

                coutN_value_pred = 0 + parseInt(coutN_value_r1) 
                                     + parseInt(coutN_value_r2) 
                                     + (parseInt(coutN_value_r1)*parseInt(perc_r1)/100) 
                                     + (parseInt(coutN_value_r2)*parseInt(perc_r2)/100);

                document.getElementById('cout').innerHTML = new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(parseInt(coutN_value_pred*prediction_totale / totalconsoN));
                document.getElementById('totalcoutN').innerHTML = new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(coutN_value);
                document.getElementById('totalconsoN').innerHTML = totalconsoN/1000 + " MWh";
                document.getElementById('totalprediction').innerHTML = prediction_totale/1000 + " MWh";
                document.getElementById('arbre').innerHTML =  parseInt((prediction_totale * (0.32 - 0.13))/5000);
                document.getElementById('vol').innerHTML =  parseInt((prediction_totale * (0.28 - 0.13))/1000);



                document.getElementById('temp_iso').innerHTML =  document.getElementById('knob_iso').value;
                document.getElementById('temp_r1').innerHTML =  document.getElementById('knob_r1').value;
                document.getElementById('temp_r2').innerHTML = document.getElementById('knob_r2').value;

                return chartData;
            }
            // update de la temperature
            function updatetemp(valeur){
                if(valeur==1){
                    var temp = "Froide";
                }
                if(valeur==2){
                    var temp = "Stable";
                }
                if(valeur==3){
                    var temp = "Chaude";
                }
                iso = document.getElementById('isolation').innerHTML;
                createchart(temp,power,iso);
            }
            function updateR1R2(){
                var R1 = document.getElementById('inputSuccess1').innerHTML;
                var R2 = document.getElementById('inputSuccess2').innerHTML;;
                var coutN_value = parseInt(R1+R2);
                console.log(coutN_value);
                document.getElementById('r1r2').innerHTML=parseInt(coutN_value);
                temp = document.getElementById('temperature').innerHTML;
                iso = document.getElementById('isolation').innerHTML;
                createchart(temp,power,iso);

            }
            // update de l'isolation
            function updateiso(valeur)
            {
                temp = document.getElementById('temperature').innerHTML;
                var iso = valeur/100;
                createchart(temp,power,iso);
            }
            // update du power
            function updatepower(valeur)
            {
                var power = valeur;
                createchart(temp,power,iso);
            }
            function parametterfunction(value){ 
                if((document.getElementById('temp_iso').innerHTML != value) && (document.getElementById('temp_r1').innerHTML == document.getElementById('knob_r1').value) 
                                                                            && (document.getElementById('temp_r2').innerHTML == document.getElementById('knob_r2').value)){
                    console.log("iso");
                    updateiso(value);    
                }else if((document.getElementById('temp_r1').innerHTML != value) && (document.getElementById('temp_iso').innerHTML == document.getElementById('knob_iso').value) 
                                                                                 && (document.getElementById('temp_r2').innerHTML == document.getElementById('knob_r2').value)){
                    console.log("r1");
                    updateiso(document.getElementById('knob_iso').value); 
                }else if((document.getElementById('temp_r2').innerHTML != value) && (document.getElementById('temp_r1').innerHTML == document.getElementById('knob_r1').value) 
                                                                                 && (document.getElementById('temp_iso').innerHTML == document.getElementById('knob_iso').value)){
                    console.log("r2");
                    updateiso(document.getElementById('knob_iso').value); 
                } 
                
            }
            // creation du graphique
            createchart(temp,power,iso);
        </script>
        <footer>
            <div class="pull-right">
                ©2019 All Rights Reserved to Dalkia. Privacy and Terms
            </div>
            <div class="clearfix"></div>
        </footer>
        // Library loading
        <script src="../vendors/jquery/dist/jquery.min.js"></script>
        <!-- Bootstrap -->
        <script src="../vendors/bootstrap/dist/js/bootstrap.min.js"></script>
        <!-- FastClick -->
        <script src="../vendors/fastclick/lib/fastclick.js"></script>
        <!-- NProgress -->
        <script src="../vendors/nprogress/nprogress.js"></script>
        <!-- Chart.js -->
        <script src="../vendors/Chart.js/dist/Chart.min.js"></script>
        <!-- gauge.js -->
        <script src="../vendors/gauge.js/dist/gauge.min.js"></script>
        <!-- bootstrap-progressbar -->
        <script src="../vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
        <!-- iCheck -->
        <script src="../vendors/iCheck/icheck.min.js"></script>
        <!-- Skycons -->
        <script src="../vendors/skycons/skycons.js"></script>
        <!-- Flot -->
        <script src="../vendors/Flot/jquery.flot.js"></script>
        <script src="../vendors/Flot/jquery.flot.pie.js"></script>
        <script src="../vendors/Flot/jquery.flot.time.js"></script>
        <script src="../vendors/Flot/jquery.flot.stack.js"></script>
        <script src="../vendors/Flot/jquery.flot.resize.js"></script>
        <!-- Flot plugins -->
        <script src="../vendors/flot.orderbars/js/jquery.flot.orderBars.js"></script>
        <script src="../vendors/flot-spline/js/jquery.flot.spline.min.js"></script>
        <script src="../vendors/flot.curvedlines/curvedLines.js"></script>
        <!-- DateJS -->
        <script src="../vendors/DateJS/build/date.js"></script>
        <!-- JQVMap -->
        <script src="../vendors/jqvmap/dist/jquery.vmap.js"></script>
        <script src="../vendors/jqvmap/dist/maps/jquery.vmap.world.js"></script>
        <script src="../vendors/jqvmap/examples/js/jquery.vmap.sampledata.js"></script>
        <!-- bootstrap-daterangepicker -->
        <script src="../vendors/moment/min/moment.min.js"></script>
        <script src="../vendors/bootstrap-daterangepicker/daterangepicker.js"></script>
        <!-- bootstrap-daterangepicker -->
        <script src="../vendors/moment/min/moment.min.js"></script>
        <script src="../vendors/bootstrap-daterangepicker/daterangepicker.js"></script>
        <!-- bootstrap-datetimepicker -->
        <script src="../vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
        <!-- Ion.RangeSlider -->
        <script src="../vendors/ion.rangeSlider/js/ion.rangeSlider.min.js"></script>
        <!-- Bootstrap Colorpicker -->
        <script src="../vendors/mjolnic-bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js"></script>
        <!-- jquery.inputmask -->
        <script src="../vendors/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>
        <!-- jQuery Knob -->
        <script src="../vendors/jquery-knob/dist/jquery.knob.min.js"></script>
        <!-- Cropper -->
        <script src="../vendors/cropper/dist/cropper.min.js"></script>
        <!-- Morris JS-->
        <script src="../vendors/raphael/raphael.min.js"></script>
        <script src="../vendors/morris.js/morris.min.js"></script>
        <!-- Custom Theme Scripts -->
        <script src="../build/js/custom.min.js"></script>




        <!-- Initialize datetimepicker -->
        <script>
            $('#myDatepicker').datetimepicker();
            $('#myDatepicker2').datetimepicker({
                format: 'DD.MM.YYYY'
            });
            $('#myDatepicker3').datetimepicker({
                format: 'hh:mm A'
            });
            $('#myDatepicker4').datetimepicker({
                ignoreReadonly: true,
                allowInputToggle: true
            });
            $('#datetimepicker6').datetimepicker();
            $('#datetimepicker7').datetimepicker({
                useCurrent: false
            });
            $("#datetimepicker6").on("dp.change", function(e) {
                $('#datetimepicker7').data("DateTimePicker").minDate(e.date)
            });
            $("#datetimepicker7").on("dp.change", function(e) {
                $('#datetimepicker6').data("DateTimePicker").maxDate(e.date)
            });
        </script>
    </body>
</html>