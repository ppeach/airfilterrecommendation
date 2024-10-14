<?php
require_once(__DIR__.'/../../includes/init.php');
// Start sessions
session_start();

// Redirect to login page if there's no sessions
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
}

// Check if refresh_token already exists
$token = checkRefreshToken();
$disabled = false;
if($token['status'] == 'ok'){
    $_SESSION['refresh_token'] = $token['refresh_token'];
    $_SESSION['scope'] = $token['scope'];
    $disabled = true;
}

?>
<!doctype html>
<html lang="en">
<?php include_once(__DIR__.'/header.php'); ?>
<body class="bg-light">
    <div class="container">
        <?php include_once(__DIR__.'/navbar.php'); ?>
        <main>

            <div class="py-5 text-center">
                <p class="lead">Below you can configure the application auth (<a href="https://developers.google.com/identity/authorization" target="_blank">Google Identity Services</a>) and database (<a href="https://sheets.google.com" target="_blank">Google Sheets</a>)</p>
                <p class="small">To configure Google Client ID and Secret, please go to <a href="https://console.developers.google.com" target="_blank">Google Console</a> and change the variable client_id and client_secret in data/config/config.json file</p>
            </div>
            <div class="row g-3">
                <div class="col-md">
                    <div class="row">
                        <h4 class="mb-3">Google Identity Services config</h4>
                        <div class="col-md-12">
                            <label for="clientID" class="form-label">Client ID</label>
                            <input type="text" class="form-control" id="clientID" name="clientID" placeholder="Client ID" value="<?=$_SESSION['client_id'];?>" disabled>
                        </div>
        
                        <div class="col-md-12">
                            <label for="clientSecret" class="form-label">Client Secret</label>
                            <input type="text" class="form-control" id="clientSecret" name="clientSecret" placeholder="Client Secret" value="<?=CLIENT_SECRET;?>" disabled>
                        </div>
        
                        <div class="col-md-8">
                            <label for="refreshToken" class="form-label">Refresh Token</label>
                            <input type="text" class="form-control" id="refreshToken" name="refreshToken" placeholder="Not configured, click on authorize button to generate" value="<?=isset($_SESSION['refresh_token'])?$_SESSION['refresh_token']:'';?>" disabled>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button class="w-100 btn btn-primary" name="authorize" onclick="client.requestCode();" <?=$disabled?'disabled':null;?>>Authorize with Google</button>
                        </div>

                        <div class="col-md-12">
                            <label for="scope" class="form-label">Scope</label>
                            <input type="text" class="form-control" id="scope" name="scope" placeholder="Scope" value="<?=isset($_SESSION['scope'])?$_SESSION['scope']:'';?>" disabled>
                        </div>

                    </div>
                </div>
            </div>

        </main>
        <footer class="my-5 pt-5 text-muted text-center text-small">
            <p class="mb-1">&copy; 2021-<?php echo date("Y"); ?> Clean Air Stars</p>
        </footer>
    </div>

    <?php include_once(__DIR__.'/footer_js.php'); ?>

    <?php if($disabled == false){?>
    <!-- Google Identity authentication JS -->
    <script src="https://accounts.google.com/gsi/client" onload="console.log('TODO: add onload function')"></script>
    <script>
        // Google Identity Authorization
        const client = google.accounts.oauth2.initCodeClient({
            client_id: '<?php echo $_SESSION['client_id']; ?>',
            scope: 'https://www.googleapis.com/auth/drive.file https://www.googleapis.com/auth/spreadsheets.readonly',
            ux_mode: 'redirect',
            redirect_uri: "<?=REDIRECT_URL.'/auth.php';?>"
        });
    </script>
    <?php } ?>

    <script>
    <?php if(isset($_SESSION['signin_message']) || isset($_SESSION['auth_message'])){ ?>
        // Toast template
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })
    <?php if(isset($_SESSION['auth_message'])){ ?>
        // Toast authorize with google
        Toast.fire({
            icon: 'success',
            title: '<?=$_SESSION['auth_message']?>'
        })
    <?php unset($_SESSION['auth_message']);} ?>
    <?php } ?>
    </script>

</body>
</html>