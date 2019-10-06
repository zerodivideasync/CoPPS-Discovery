<?php
/**
 * Header template part.
 */
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Cache-control" content="no-cache">
        <meta name="description" content="<?php echo SITENAME; ?>" />
        <!--<link rel="icon" href="images/favicon.ico">-->
        <!--<link rel="shortcut icon" href="URL_XXXXXXXXXXXXXXXX" type="image/x-icon" />-->
        <!--<link rel="image_src" href="URL_XXXXXXXXXXXXXXXX"/>-->

        <title><?php echo SITENAME; ?></title>

        <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans|Raleway|Candal">

        <!-- Bootstrap -->
        <link rel="stylesheet" href="<?php echo APP_ROOT ?>vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
        <!-- FontAwesome -->
        <link href="<?php echo APP_ROOT ?>vendor/font-awesome/fontawesome/fontawesome-all.min.css" rel="stylesheet">
        <!-- jQuery UI -->
        <!--<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">-->
        <link rel="stylesheet" href="<?php echo APP_ROOT ?>vendor/components/jqueryui/themes/base/jquery-ui.min.css">
        <!-- Datatables -->
        <link rel="stylesheet" type="text/css" href="<?php echo APP_ROOT ?>vendor/components/datatables/css/jquery.dataTables.1.10.16.min.css"/>
        <!-- Our custom CSS file -->
        <link href="<?php echo APP_ROOT ?>css/custom.css" rel="stylesheet">

        <!-- DropzoneJS CSS -->
        <link rel="stylesheet" href="<?php echo APP_ROOT ?>vendor/components/dropzone/css/dropzone.css">
        
        <!-- jQuery --> 
        <script src="<?php echo APP_ROOT ?>vendor/components/jquery/jquery.min.js"></script>
        <!-- jQuery UI -->
        <script src="<?php echo APP_ROOT ?>vendor/components/jqueryui/jquery-ui.min.js"></script>
        <!-- PopperJS -->
        <script type="text/javascript" src="https://unpkg.com/popper.js/dist/umd/popper.min.js"></script>
        <!-- Bootstrap js -->
        <script src="<?php echo APP_ROOT ?>vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
        <!-- Datatables -->
        <script type="text/javascript" src="<?php echo APP_ROOT ?>vendor/components/datatables/js/jquery.dataTables-1.10.16.min.js"></script>

        <!-- Select2 (Autocomplete combobox jQuery Plugin) -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.full.min.js"></script>

        <!-- Dropzone JS -->
        <script type="text/javascript" src="<?php echo APP_ROOT ?>vendor/components/dropzone/js/dropzone.js"></script>
        <!-- Chart JS -->
        <script type="text/javascript" src="<?php echo APP_ROOT ?>vendor/nnnick/chartjs/dist/Chart.bundle.min.js"></script>

        <script type="text/javascript" src="<?php echo APP_ROOT ?>js/cookie.js"></script>
        <script type="text/javascript" src="<?php echo APP_ROOT ?>js/map_colors.js"></script>
        <script type="text/javascript" src="<?php echo APP_ROOT ?>js/custom_js.js"></script>
    </head>