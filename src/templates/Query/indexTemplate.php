<?php
require_once APP_ROOT_ABS . '/templates/helpers/HtmlHelper.php';
require_once APP_ROOT_ABS . '/components/FlashMessageProvider.php';
include TPL_PARTS . 'header.php';
include TPL_PARTS . 'navbar.php';
?>
<div id="msg" class="container">
    <?php echo FlashMessageProvider::show(); //show flash messages, if any ?>
</div>

<section id="diagnoses" class="section-bottom-padding">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <h1>Query <i class="fa fa-spinner fa-spin hidden"></i></h1> 
            </div>	
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 mt-3">
                <div id="floating-panel" class="hidden">
                    <button onclick="deleteShape();" class="btn btn-danger w-100 h-100"><i class="fas fa-times"></i></button>
                </div>
                <div id="map"></div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 mt-3">
                <form action="" class="form text-center" method="POST" onsubmit="return prepBeforeSubmit();">
                    <input type="hidden" name="shape_area" id="shape_area" value="">
                    <input type="hidden" id="id_element" name="id_element" value="" placeholder="id">
                    <input type="hidden" id="range_element" name="range_element" value="" placeholder="range_element">
                    <input type="hidden" id="date_from" name="date_from" value="" placeholder="date_from">
                    <input type="hidden" id="date_to" name="date_to" value="" placeholder="date_to">
                    <input type="hidden" id="type" name="type" value="" placeholder="type">
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 col-xl-2 offset-xl-2 pb-2">
                            <div id="search_button_toggle" class="btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-outline-info w-100">
                                    <i class="fas fa-search"></i>
                                    <input type="checkbox" id="search_button" name="search_button" autocomplete="off"> <span id="text_search">Search disabled</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 col-xl-2 pb-2">
                            <a href="" class="btn btn-warning w-100" onclick="return confirm('Are you sure? All pending data will be lost.');"><i class="fas fa-undo-alt"></i> Search all</a>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 col-xl-2 pb-2">
                            <button id="retrieve_button" name="retrieve_button" class="btn btn-success w-100"><i class="fas fa-search mr-1"></i> Retrieve</button>												
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 col-xl-2 pb-2">
                            <button onclick="return false;" data-toggle="modal" data-target="#myModal" id="help_button" name="help_button" class="btn btn-outline-success w-100"><i class="fas fa-info mr-3"></i> Help</button>												
                        </div>
                    </div>
                </form>
            </div>
        </div>	
        <div class="row sm-pb-50">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <h3>Query options <span id="label_opt"></span></h3>
                <p>
                    Select a marker or insert an area.
                </p>
                <div id="options_panel">
                    <div class="form-row query-option diagnosis-option pollution-option hidden">
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-3 col-xl-3 mb-3 p-2">
                            <label for="name_element" class="query-label-option">Name: </label>
                            <input class="w-75 input-read-only" type="text" id="name_element" name="name_element" readonly value="" placeholder="Name" required>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-3 col-xl-3 mb-3 p-2">
                            <label for="date_from_element" class="query-label-option">Date: </label>
                            <input class="w-75 input-read-only" type="text" id="date_from_element" name="date_from_element" readonly value="" placeholder="Start date">
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-3 col-xl-3 mb-3 p-2 query-option pollution-option hidden">
                            <label for="date_to_element" class="query-label-option">Date: </label>
                            <input class="w-75 input-read-only" type="text" id="date_to_element" name="date_to_element" readonly value="" placeholder="End date">
                        </div>
                    </div>
                    <div class="form-row query-option diagnosis-option pollution-option circle-option polygon-option hidden">
                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3 col-xl-3 mb-3 p-2">
                            <label for="date_from_query" class="query-label-option">From: </label>
                            <input class="datepicker w-50" type="text" id="date_from_query" name="date_from_query" value="" placeholder="Start date">
                            <i class="fas fa-times clear-input-text"></i>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3 col-xl-3 mb-3 p-2">
                            <label for="date_to_query" class="query-label-option">To: </label>
                            <input class="datepicker w-50" type="text" id="date_to_query" name="date_to_query" value="" placeholder="End date">
                            <i class="fas fa-times clear-input-text"></i>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3 col-xl-3 mb-3 p-2 query-option diagnosis-option pollution-option hidden">
                            <label for="range_query">Range (km): </label>
                            <input class="fr w-50" type="number" value="1" step="1" min="1" max="1000" id="range_query" name="range_query" placeholder="1">
                        </div>
                        <div id="slider" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 mb-3 p-2 query-option circle-option hidden">
                            <div id="custom-handle" class="ui-slider-handle"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row sm-pb-50">
            <div class="col col-md-9 col-lg-6 col-xl-6 table_pathologies hidden">
                <h3 id="title_pollution_name">List</h3>
                <table id="table_pathologies" class="table table-responsive " style="width:100%">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th class="w-75">Name</th>
                            <th class="w-25">Occurences</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="col col-md-9 col-lg-6 col-xl-6 table_pathologies hidden">
                <h3 id="title_statistics">Statistics on Diagnoses found</h3>
                <div id="canvas_diagnoses_graphic_pie"><!-- GRAPH -->
                    <canvas id="diagnoses_graphic_pie"></canvas>
                </div>
                <div id="canvas_diagnoses_graphic_bar"><!-- GRAPH -->
                    <canvas id="diagnoses_graphic_bar"></canvas>
                </div>
            </div>
        </div>
        <div id="myModal" class="modal fade" role="dialog"> <!-- Help Modal -->
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Help</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p><i class="fas fa-globe"></i> - Insert a <strong>circle</strong> or a <strong>polygon</strong> (then click on Retrieve) to retrieve all diagnoses and pollution sources in their area.</p>
                        <p><i class="fas fa-industry"></i> - Click on a diagnoses (then click on Retrieve) to retrieve all <strong>pollution sources</strong> within the specified range.</p>
                        <p><i class="fas fa-dna"></i> - Click on a pollution source (then click on Retrieve) to obtain the number frequence of all <strong>pathologies</strong> within the specified range.</p>
                        <p><i class="fas fa-search"></i> - The <strong>Search enabled</strong> and the <strong>Search all</strong> buttons provide you all diagnoses and pollution sources in the current map position.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div id="result_search">

</div>
<?php require_once 'jsTemplate.php'; ?>
<script src="https://maps.googleapis.com/maps/api/js?key=YOURGMAPSKEY&libraries=drawing&callback=initMap"
async defer></script>

<?php
include TPL_PARTS . 'footer.php';

