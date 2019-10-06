<?php
$URL_HOME = APP_ROOT;
if (Session::isLogged()) {
    $URL_HOME = APP_ROOT . 'Home/';
}
?>
<section id="section-banner" class="section-banner font-white mb-2">
    <div class="container navbar-container">
        <div class="row navbar-row">
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container">
                    <a class="navbar-brand font-white navbar-brand" href="<?php echo $URL_HOME; ?>">Co.P.P.S. Discovery</a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse font-white" id="navbarSupportedContent">
                        <ul class="navbar-nav ml-auto">
                            <?php if (!Session::isLogged()) { ?>
                                <li class="nav-item">
                                    <a class="nav-link font-white" href="<?php echo APP_ROOT; ?>">About</a>
                                </li>
                            <?php } ?>
                            <li class="nav-item active">
                                <a class="nav-link font-white" href="<?php echo APP_ROOT . 'Home/'; ?>">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link font-white" href="<?php echo APP_ROOT . 'Pathology/'; ?>">Pathologies</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link font-white" href="<?php echo APP_ROOT . 'Diagnosis/'; ?>">Diagnosis</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link font-white" href="<?php echo APP_ROOT . 'PollutionSrc/'; ?>">Pollution sources</a>
                            </li>
                            <li class="nav-item font-white">
                                <a class="nav-link font-white" href="<?php echo APP_ROOT . 'Query/'; ?>">Query</a>
                            </li>
                            <li class="nav-item font-white">
                                <a class="nav-link font-white" href="<?php echo APP_ROOT . 'Tools/'; ?>">Tools</a>
                            </li>
                            <?php if (Session::isLogged()) { ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle font-white" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Hi, <?php echo ucfirst(USERNAME); ?>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="<?php echo APP_ROOT . 'Users/logout.php'; ?>">Log out</a>
                                    </div>
                                </li>
                            <?php } else { ?>
                                <li class="nav-item font-white li-login ml-3">
                                    <a class="nav-link font-white" href="<?php echo APP_ROOT . 'Users/login.php'; ?>">Log in</a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</section>