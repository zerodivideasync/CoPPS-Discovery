<?php
/**
 * This is the template file for the Login/index view (the login form).
 */
include TPL_PARTS . 'header.php';
include TPL_PARTS . 'navbar.php';
?>
<div class="container">
    <?php echo FlashMessageProvider::show(); //show flash messages, if any ?>
</div>
<section class="section-bottom-padding">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-8 col-lg-5 col-xl-5 pt-5 mx-auto">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Authentication</h3>
                    </div>
                    <div class="panel-body">
                        <form id="signupForm" method="POST" class="form-horizontal" action="<?php echo APP_ROOT . 'Users/login.php' ?>" autocomplete="on">
                            <div class="form-group">
                                <label class="col-sm-4 control-label" for="username">Username</label>
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-9">
                                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" value="<?php (isset($username) ? $username : '') ?>"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label" for="password">Password</label>
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-9">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" />
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-9 col-sm-offset-4">
                                    <button type="submit" class="btn btn-primary" name="signup" value="1">Login</button>
                                </div>
                            </div>
                        </form>   
                    </div> <!-- Panel body -->    
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-9 alert alert-success">
                        <p class="text-left">Credentials: guest/guest</p>
                    </div>            
                </div>
            </div>
        </div>
    </div>
</section>
<?php
include TPL_PARTS . 'footer.php';
