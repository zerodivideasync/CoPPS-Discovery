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
                <h1>Tools <i class="fa fa-spinner fa-spin hidden"></i></h1> 
            </div>	
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3">
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link active blue-nav-link m-3" id="v-pills-import-pathologies-tab" data-toggle="pill" href="#v-pills-import-pathologies" role="tab" aria-controls="v-pills-import-pathologies" aria-selected="false">Import Pathologies</a>
                    <a class="nav-link blue-nav-link m-3" id="v-pills-export-pathologies-tab" data-toggle="pill" href="#v-pills-export-pathologies" role="tab" aria-controls="v-pills-export-pathologies" aria-selected="false">Export Pathologies</a>
                </div>
            </div>
            <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9">
                <div class="tab-content" id="v-pills-tabContent">
                    <div class="tab-pane fade show active" id="v-pills-import-pathologies" role="tabpanel" aria-labelledby="v-pills-import-pathologies-tab">
                        <h1 id="try-it-out">Import CSV Pathologies <i class="fas fa-file-import"></i><i class="fa fa-spinner fa-spin hidden"></i></h1>
                        <div class="dropzone-custom">
                            <form action="upload.php" method="POST" id="pathologies-form" class="needsclick dz-clickable">
                                <div class="dz-message needsclick">
                                    Drop files here or click to upload.<br>
                                    <span class="note needsclick">(Only <strong>well-formed</strong> csv.)</span>
                                </div>
                            </form>
                        </div>
                        <div class="row pt-3">
                            <div class="col-12">
                                <table id="table_pathologies" class="table table-responsive hidden " style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th class="w-75">Name</th>
                                            <th class="w-25">status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="v-pills-export-pathologies" role="tabpanel" aria-labelledby="v-pills-export-pathologies-tab">
                        <h1 id="try-it-out">Export CSV Pathologies</h1>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                <p class="text-center m-5">You can backup all pathologies stored in the database and save them in a csv file.</p>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                <div class="card text-white bg-secondary mx-auto" style="max-width: 18rem;">
                                    <div class="card-body">
                                        <p class="card-text text-center"><i class="fas fa-cloud-download-alt icon-card-custom"></i></p>
                                        <a href="<?php echo APP_ROOT . 'Tools/_export.php'; ?>" class="btn btn-primary btn-primary-home w-100">CSV</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>	
    </div>
</section>

<div id="result_search">

</div>
<script type="text/javascript" src="<?php echo APP_ROOT; ?>js/tools.js"></script>

<?php
include TPL_PARTS . 'footer.php';

