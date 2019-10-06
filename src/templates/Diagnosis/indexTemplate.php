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
                <h1>
                    Diagnoses <i class="fa fa-spinner fa-spin hidden"></i>
                    <?php if ($name_pathology) { ?>
                        of <i class="pos-relative"><?php echo $name_pathology; ?> <i class="fas fa-window-close delete-cross-button-diagnosis" onclick="window.open(window.location.href.replace(/[\?#].*|$/, ''), '_self')"></i></i>
                    <?php } ?>
                </h1> 
            </div>	
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 mt-3">
                <div id="map"></div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 mt-3">
                <form action="" class="form text-center" method="POST" onsubmit="return prepBeforeSubmit();">
                    <input type="hidden" name="shapes_to_insert" id="shapes_to_insert" value="">
                    <input type="hidden" name="shapes_to_delete" id="shapes_to_delete" value="">
                    <input type="hidden" name="shapes_to_edit" id="shapes_to_edit" value="">
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
                            <a href="" class="btn btn-warning w-100" onclick="return confirm('Are you sure? All pending data will be lost.');"><i class="fas fa-undo-alt"></i> Undo all</a>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 col-xl-2 pb-2">
                            <button type="submit" class="btn btn-success w-100"><i class="far fa-save"></i> Save changes</button>												
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 col-xl-2 pb-2">
                            <button onclick="return false;" data-toggle="modal" data-target="#myModal" id="help_button" name="help_button" class="btn btn-outline-success w-100"><i class="fas fa-info mr-3"></i> Help</button>
                        </div>
                    </div>
                </form>
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
                        <p><i class="fas fa-globe"></i> - Click on the marker icon on the map and place it somewhere to <strong>insert</strong> a new diagnosis (remember to Save the Changes!).</p>
                        <p><i class="fas fa-pencil-alt"></i> - Click on a diagnosis on the map to <strong>edit</strong> its values.</p>
                        <p><i class="fas fa-search"></i> - The <strong>Search enabled</strong> button provides you all diagnoses in the current map position.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript" src="<?php echo APP_ROOT; ?>js/diagnosis.js"></script>
<?php
if ($pathologies_list) {
    $pathologies_as_objects = array();
    foreach ($pathologies_list as $p) {
        $pathologies_as_objects[] = "{id: $p[id], name: \"$p[name]\"}";
    }
    ?>
    <script type="text/javascript"><?php echo "pathologiesList = [" . implode(",", $pathologies_as_objects) . "]"; ?></script>
    <?php
}
if ($id_pathology) {
    ?>
    <script type="text/javascript"><?php echo "id_pathology = $id_pathology"; ?></script>
    <?php
}
?>
<script src="https://maps.googleapis.com/maps/api/js?key=YOURGMAPSKEY&libraries=drawing&callback=initMap"
async defer></script>

<?php
include TPL_PARTS . 'footer.php';

