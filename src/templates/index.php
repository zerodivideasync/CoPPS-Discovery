<?php
include TPL_PARTS . 'header.php';
include TPL_PARTS . 'navbar_index.php';
?>

<section id="section-index-description" class="index-description">
    <div class="container container-index-description">
        <div class="row text-center">
            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                <h2 class="ser-title">Our Service</h2>
                <hr class="botm-line">
                <p>CoPPS Discovery lets you manage worldwide informations about Diagnoses, Pathologies and Pollution Sources using a spatial database, then it helps you find a correlation between them.</p>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                        <img class="img-responsive img-index-description" alt="PHP 7" title="PHP 7" src="<?php echo APP_ROOT ?>img/php.png">
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                        <img class="img-responsive img-index-description" alt="PostgreSQL" title="PostgreSQL" src="<?php echo APP_ROOT ?>img/postgresql.png">
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                        <img class="img-responsive img-index-description" alt="PostGIS" title="PostGIS" src="<?php echo APP_ROOT ?>img/postgis.png">
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                        <img class="img-responsive img-index-description" alt="Bootstrap 4" title="Bootstrap 4" src="<?php echo APP_ROOT ?>img/bootstrap4.png">
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                        <img class="img-responsive img-index-description" alt="jQuery" title="jQuery" src="<?php echo APP_ROOT ?>img/jquery.png">
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                        <img class="img-responsive img-index-description" alt="Google Maps API" title="Google Maps API" src="<?php echo APP_ROOT ?>img/google-maps.png">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
include TPL_PARTS . 'footer_index.php';
?>