<?php
require_once APP_ROOT_ABS . '/templates/helpers/HtmlHelper.php';
require_once APP_ROOT_ABS . '/components/FlashMessageProvider.php';
include TPL_PARTS . 'header.php';
include TPL_PARTS . 'navbar.php';
?>

<div class="container">
    <?php echo FlashMessageProvider::show(); //show flash messages, if any ?>
</div>

<section id="home" class="section-bottom-padding">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <h1>Homepage</h1>
                <p>Select a category</p>
            </div>	
        </div>
        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-4 ml-auto mb-4">
                <div class="card text-white bg-info mx-auto" style="max-width: 18rem;">
                    <div class="card-header text-center">Pathologies</div>
                    <div class="card-body">
                        <p class="card-text text-center"><i class="fas fa-dna icon-card-custom"></i></p>
                        <a href="<?php echo APP_ROOT . 'Pathology/'; ?>" class="btn btn-primary btn-primary-home w-100">Manage Pathologies</a>
                    </div>
                </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-4 mr-auto mb-4">
                <div class="card text-white bg-secondary mx-auto" style="max-width: 18rem;">
                    <div class="card-header text-center">Diagnoses</div>
                    <div class="card-body">
                        <p class="card-text text-center"><i class="fas fa-user-md icon-card-custom"></i></p>
                        <a href="<?php echo APP_ROOT . 'Diagnosis/'; ?>" class="btn btn-primary btn-primary-home w-100">Manage Diagnoses</a>
                    </div>
                </div>
            </div>
        </div>		
        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-4 ml-auto mb-4">
                <div class="card text-white bg-dark mx-auto" style="max-width: 18rem;">
                    <div class="card-header text-center">Pollution sources</div>
                    <div class="card-body">
                        <p class="card-text text-center"><i class="fas fa-industry icon-card-custom"></i></p>
                        <a href="<?php echo APP_ROOT . 'PollutionSrc/'; ?>" class="btn btn-primary btn-primary-home w-100">Manage Sources</a>
                    </div>
                </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-4 mr-auto mb-4">
                <div class="card text-white bg-primary mx-auto" style="max-width: 18rem;">
                    <div class="card-header text-center">Query</div>
                    <div class="card-body">
                        <p class="card-text text-center"><i class="fas fa-search icon-card-custom"></i></p>
                        <a href="<?php echo APP_ROOT . 'Query/'; ?>" class="btn btn-primary btn-primary-home w-100">Discover correlations</a>
                    </div>
                </div>
            </div>
        </div>				
    </div>
</section>

<?php
include TPL_PARTS . 'footer.php';
