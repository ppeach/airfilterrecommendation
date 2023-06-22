<?php
require_once('includes/config.php');

if(isset($_POST['submit'])){
    // Set data from POST
    $data = array(
        'sheet_id'        => $_POST['sheetID'],
        'sheet_range'     => $_POST['sheetRange'],
        'client_id'       => $_POST['clientID'],
        'client_secret'   => $_POST['clientSecret'],
        'refresh_token'   => $_POST['refreshToken'],
        'update_key'      => $_POST['authKey'],
    );

    if (!is_dir('data')) {
      mkdir('data');
    }

    if (!is_dir('data/config')) {
      mkdir('data/config');
    }

    $update = file_put_contents("data/config/config.json", json_encode($data));

    if($update) {
        $message = array('status' => 'success', 'message' => 'Configurations updated successfully');
    } else {
        $message = array('status' => 'error', 'message' => 'Error updating Configurations');
    }
    header('Location: '.parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH).'?key='.$_POST['authKey'], true, 302);
}

if ((!config('update_key')) || (isset($_GET['key']) && $_GET['key'] === config('update_key'))) {
  $access_permitted = true;
} else {
  $access_permitted = false;
}
?>
<?php if($access_permitted) { ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Air Filter Recommendation Tool</title>

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

    <!-- Bootstrap core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://getbootstrap.com/docs/5.1/examples/checkout/form-validation.css">

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>
  </head>
  <body class="bg-light">
    
<div class="container">
  <main>
    <div class="py-5 text-center">
      <!-- <img class="d-block mx-auto mb-4" src="/docs/5.1/assets/brand/bootstrap-logo.svg" alt="" width="72" height="57"> -->
      <h2>Configurations</h2>
      <p class="lead">Below you can edit configuration for the scripts</p>
    </div>

    <div class="row g-5">

      <div class="col">
        <h4 class="mb-3">Google Sheets config</h4>
        <form action="" method="post" class="needs-validation" novalidate>
          <div class="row g-3">
            <div class="col-12">
              <label for="sheetID" class="form-label">Sheet ID</label>
              <input type="text" class="form-control" id="sheetID" name="sheetID" placeholder="17j6FZwvqHRFkGoH5996u5JdR7tk4_7fNuTxAK7kc4Fk" value="<?=config('sheet_id');?>" required>
              <small>You can paste a full Google Sheets URL here and the sheet ID will be extracted</small>
              <div class="invalid-feedback">
                Sheet ID is required.
              </div>
            </div>

            <div class="col-12">
              <label for="sheetRange" class="form-label">Sheet Range (Optional)</label>
              <input type="text" class="form-control" id="sheetRange" name="sheetRange" placeholder="A1:L1000" value="<?=config('sheet_range');?>">
              <div class="invalid-feedback">
                Sheet Range is optional.
              </div>
            </div>

        </div>

        <hr class="my-4">

          <h4 class="mb-3">Google OAuth</h4>

          <div class="row g-3">

            <div class="col-12">
              <label for="clientID" class="form-label">Client ID</label>
              <input type="text" class="form-control" id="clientID" name="clientID" placeholder="Client ID" value="<?=config('client_id');?>" required>
              <div class="invalid-feedback">
                Client ID is required.
              </div>
            </div>

            <div class="col-12">
              <label for="clientSecret" class="form-label">Client Secret</label>
              <input type="text" class="form-control" id="clientSecret" name="clientSecret" placeholder="Client Secret" value="<?=config('client_secret');?>" required>
              <div class="invalid-feedback">
                Client Secret is required.
              </div>
            </div>

            <div class="col-md-8">
                <label for="refreshToken" class="form-label">Refresh Token</label>
              <input type="text" class="form-control" id="refreshToken" name="refreshToken" placeholder="Refresh Token" value="<?=config('refresh_token');?>" required>
              <div class="invalid-feedback">
                Refresh Token is required.
              </div>
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button class="w-100 btn btn-primary" name="validate" type="submit">Validate</button>
            </div>
        
            </div>

          <hr class="my-4">

          <h4 class="mb-3">Auth Key</h4>

          <div class="row gy-3">
            <div class="col-12">
              <label for="authKey" class="form-label">Auth Key</label>
              <input type="text" class="form-control" id="authKey" name="authKey" placeholder="Auth Key" value="<?=config('update_key');?>" required>
              <div class="invalid-feedback">
                Auth Key is required.
              </div>
            </div>

          </div>

          <hr class="my-4">

          <button class="w-100 btn btn-primary btn-lg" name="submit" type="submit">Update Configurations</button>


          <hr class="my-4">

          <a class="w-100 btn btn-secondary btn-lg" href="./update.php?key=<?=config('update_key');?>">Refresh Database</a>
        </form>
      </div>
    </div>
  </main>

  <footer class="my-5 pt-5 text-muted text-center text-small">
        <p class="mb-1">&copy; 2021-<?php echo date("Y"); ?> Clean Air Stars</p>
    </footer>
</div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <script src="https://getbootstrap.com/docs/5.1/examples/checkout/form-validation.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if(isset($message)){ ?>
      <script>
          Swal.fire({
              icon: '<?=$message['status'];?>',
              text: '<?=$message['message'];?>',
              showConfirmButton: false,
              timer: 1500
          })
      </script>
    <?php } ?>
    <script>
      document.querySelector('#sheetID').addEventListener('paste', (ev) => {
        event.preventDefault();
        let text = (event.clipboardData || window.clipboardData).getData('text');

        if (text.search('https://docs.google.com/spreadsheets/d/') !== -1) {
          text = text.replace('https://docs.google.com/spreadsheets/d/', '');
          text = text.split('/')[0];
        }

        ev.target.value = text;
      });
    </script>
  </body>
</html>
<?php } else { echo '<h1>Public access is not allowed</h1>'; } ?>
