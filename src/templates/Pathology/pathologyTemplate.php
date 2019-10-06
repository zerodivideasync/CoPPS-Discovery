<?php
require_once APP_ROOT_ABS . '/templates/helpers/HtmlHelper.php';
require_once APP_ROOT_ABS . '/components/FlashMessageProvider.php';
include TPL_PARTS . 'header.php';
include TPL_PARTS . 'navbar.php';
?>
<div id="msg" class="container">
    <?php echo FlashMessageProvider::show(); //show flash messages, if any ?>
</div>

<section id="pathologies">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <h1>Pathologies</h1>
            </div>	
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-8 col-lg-6 mt-3">
                <h3 id="label-table">List</h3>
                <table id="table_pathologies" class="table table-responsive display" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:5%">Id</th>
                            <th style="width:55%">Name</th>
                            <th style="width:40%">Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($pathologies && !empty($pathologies)) {
                            foreach ($pathologies as $p) {
                                ?>
                                <tr id="<?php echo $p['id']; ?>">
                                    <td class="text-center id-pathologies-table"><?php echo $p['id']; ?></td>
                                    <td class="name-pathologies-table"><?php echo $p['name']; ?></td>
                                    <td class="options-table">
                                        <button type="button" class="btn btn-warning btn-pathologies-table" onclick="editPathology(<?php echo $p['id']; ?>, '<?php echo $p['name']; ?>')">Edit</button>
                                        <button type="button" class="btn btn-info btn-pathologies-table" onclick="viewPathology(<?php echo $p['id']; ?>, '<?php echo APP_ROOT; ?>Diagnosis')">View</button>
                                        <button type="button" class="btn btn-danger btn-pathologies-table">Delete</button>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-6 mt-3">
                <h3 id="label-form">Insert new pathology</h3>
                <form action="" class="form" method="POST" onsubmit="return submitButton();">
                    <input id="op" name="op" value="insert" type="hidden">
                    <div class="form-group">
                        <label for="id_pathology">Id:</label>
                        <input class="form-control" placeholder="Auto" id="id_pathology" name="id_pathology" value="" disabled="" type="text">
                    </div>
                    <div class="form-group">
                        <label for="name_pathology">Name:</label>
                        <input class="form-control" placeholder="Name" id="name_pathology" name="name_pathology" value="" maxlength="64" type="text" required>
                    </div>
                    <button type="reset" class="btn btn-outline-primary" onclick="resetButton(this);">Reset</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>					
    </div>
</section>
<script type="text/javascript" src="<?php echo APP_ROOT; ?>js/pathology.js"></script>
<?php
include TPL_PARTS . 'footer.php';

