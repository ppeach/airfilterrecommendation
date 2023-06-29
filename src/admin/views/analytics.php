<?php
require_once(__DIR__.'/../../includes/init.php');
// Start sessions
session_start();

// Redirect to login page if there's no sessions
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
}

// All Countries analytics
$data = getAnalytics();
$show = false;

// Countries analytics data
if (isset($_GET['country'])) {
    $country = $_GET['country'];
    $data = getAnalyticCountry($country);
    $show = true;
}
?>
<!doctype html>
<html lang="en">
<?php include_once(__DIR__.'/header.php'); ?>
<body class="bg-light">
    <div class="container">
        <?php include_once(__DIR__.'/navbar.php'); ?>
        <main>

        <?php if(!$show){ ?>
            <h4 class="mb-3">Buy now button analytics</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                    <th scope="col">#</th>
                    <th scope="col">Contries</th>
                    <th scope="col">Total</th>
                    <th scope="col">Today</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data as $key => $analytic){?>
                    <tr>
                    <th scope="row"><?=$key;?></th>
                    <td><a href="analytics.php?country=<?=$analytic['countries']['name'];?>"><?=$analytic['countries']['name'];?></a></td>
                    <td><?=$analytic['countries']['total'];?></td>
                    <td><?=$analytic['countries']['today'];?></td>
                    </tr>
                    <tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>
        <?php if($show){ ?>
            <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span>Buy now button analytics for <?=$country;?></span>
                <a href="analytics.php" class="btn btn-primary btn-sm ms-2">Back to Analytics</a>
            </h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                    <th scope="col">#</th>
                    <th scope="col">Product</th>
                    <th scope="col">Link</th>
                    <th scope="col">Clicks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data as $key => $analytic){?>
                    <tr>
                    <th scope="row"><?=$key;?></th>
                    <td><?=$analytic['product'];?></td>
                    <td><a href="<?=$analytic['link'];?>" target="_blank" rel="noopener noreferrer"><?=$analytic['link'];?></a></td>
                    <td><?=$analytic['view'];?></td>
                    </tr>
                    <tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>

        </main>
        <footer class="my-5 pt-5 text-muted text-center text-small">
            <p class="mb-1">&copy; 2021-<?php echo date("Y"); ?> Clean Air Stars</p>
        </footer>
    </div>

    <?php include_once(__DIR__.'/footer_js.php'); ?>

</body>
</html>