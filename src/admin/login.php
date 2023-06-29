<?php
require_once(__DIR__.'/../includes/init.php');
// Start sessions
session_start();

// Check if user sessions is set
if (isset($_SESSION['user_email'])) {
    header("Location: index.php");
}
?>
<!doctype html>
<html lang="en" data-bs-theme="auto" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login | Clean Air Stars</title>

    <!-- favicon -->
    <link rel="icon" type="image/png" href="https://cleanairstars.com/wp-content/uploads/2021/11/cropped-wind.png" />

    <meta name="description" content="This tool helps recommend how many of the available models of portable air filters at different fan speeds will be required to meet current recommendations to reduce the risk of transmission of respiratory viruses like SARS-CoV-2" />
    <meta name="robots" content="max-image-preview:large" />
    <link rel="canonical" href="<?php echo 'https://'.$_SERVER['SERVER_NAME'];?>" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:site_name" content="Air Filter Recommendation Tool" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Air Filter Recommendation Tool" />
    <meta property="og:description" content="This tool helps recommend how many of the available models of portable air filters at different fan speeds will be required to meet current recommendations to reduce the risk of transmission of respiratory viruses like SARS-CoV-2" />
    <meta property="og:url" content="<?php echo 'https://'.$_SERVER['SERVER_NAME'];?>" />
    <meta property="og:image" content="https://cleanairstars.com/wp-content/uploads/2021/11/cropped-wind.png" />
    <meta property="og:image:secure_url" content="https://cleanairstars.com/wp-content/uploads/2021/11/cropped-wind.png" />
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:domain" content="<?php echo 'https://'.$_SERVER['SERVER_NAME'];?>" />
    <meta name="twitter:title" content="Air Filter Recommendation Tool" />
    <meta name="twitter:description" content="This tool helps recommend how many of the available models of portable air filters at different fan speeds will be required to meet current recommendations to reduce the risk of transmission of respiratory viruses like SARS-CoV-2" />
    <meta name="twitter:image" content="https://cleanairstars.com/wp-content/uploads/2021/11/cropped-wind.png" />

    <!-- Bootstrap CSS v5.3.0 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

</head>
<body class="d-flex h-100 text-center">
    <div class="d-flex w-100 h-100 p-3 mx-auto flex-column">
    <header class="mb-auto">
        <div class="text-center">
          <h3 class="fs-4 fw-light">Admin login page</h3>
        </div>
    </header>
    <main>
        <h3 class="fs-4 mb-3 fw-normal">Please sign in</h3>
  
        <div class="d-flex flex-column flex-wrap justify-content-center align-items-center">
          <!-- Google Identity authentication start-->
          <div id="g_id_onload"
              data-client_id="<?=CLIENT_ID;?>"
              data-context="signin"
              data-ux_mode="popup"
              data-login_uri="<?=REDIRECT_URL.'/auth.php';?>"
              data-nonce=""
              data-auto_prompt="false">
          </div>
    
          <div class="g_id_signin"
              data-type="standard"
              data-shape="rectangular"
              data-theme="filled_blue"
              data-text="signin_with"
              data-size="large"
              data-logo_alignment="left">
          </div>
          <!-- Google Identity authentication end-->
      </div>

    </main>

    <footer class="mt-auto text-muted text-center text-small">
        <p class="mb-1">&copy; 2021-<?php echo date("Y"); ?> Clean Air Stars</p>
    </footer>

    </div>

    <!-- Bootstrap JS v5.3.0 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>

    <!-- Google Identity authentication JS -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>

    <?php if(isset($_SESSION['signout_message']) || isset($_SESSION['error'])){ ?>
    <!-- Sweetalert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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

        // Toast sign out
    <?php if(isset($_SESSION['signout_message'])){
        echo "
        Toast.fire({
            icon: 'info',
            title: '".$_SESSION['signout_message']."'
        })";
        session_destroy();
    }?>

        // Toast error sign in
    <?php if(isset($_SESSION['error'])){
        echo "
        Toast.fire({
            icon: 'error',
            title: '".$_SESSION['error']."'
        })";
        session_destroy();
    }?>
    </script>
    <?php } ?>
</body>
</html>