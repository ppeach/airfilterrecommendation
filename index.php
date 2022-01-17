<?php
require_once('includes/config.php');

// If form is submitted
if(isset($_POST['submit'])){

    // Set data from POST
    $country = $_POST['country'];
    $max_an = $_POST['max-an'];
    $wifi = $_POST['wifi'];
    $ach  = $_POST['ach'];
    $room_size = $_POST['room-size'];
    $rms_type = $_POST['m3-or-cu'];
    $no_of_occ = $_POST['no-of-occ'];
    $prefltr = $_POST['prefilter'];

    // Get data from google sheets or json file
    $data = getHepa($country);

    // Filter data based on user input
    // Filter by max acceptable noise
    $filter_result = array_filter($data, function($item) use ($max_an){
        if($max_an != 0){
            return ($item['Noise (dBA)'] <= $max_an);
        } else {
            return true;
        }
    });

    // Filter by wifi
    $filter_result = array_filter($filter_result, function($item) use ($wifi){
        if($wifi != 'Not fussed'){
            return ($item['Wifi'] == $wifi);
        } else {
            return true;
        }
    });

    // Filter by prefilter
    $filter_result = array_filter($filter_result, function($item) use ($prefltr){
        if($prefltr != 'Not fussed'){
            return ($item['Prefilter'] == $prefltr);
        } else {
            return true;
        }
    });

    // Calculate ACH and Total Cost
    $types = array(
        'cadrm3'    => $cadr_m3,
        'cadrcubic' => $cadr_cubic,
        'cadrlitre' => $cadr_litre,
        'cost'      => $cost
    );
    $achs   = array(
        'room_size' => $room_size,
        'room_type' => $rms_type,
        'no_off_occ'=> $no_of_occ
    );
    $hepa_result = calculateACH($filter_result, $ach, $types, $achs);

    // Get total result
    $total = count($hepa_result);

    // Scroll to result page
    $scroll = '$(document).ready(function(){
            $("html, body").animate({ scrollTop: $("#result-page").offset().top }, "1000");
        });';

}
?>
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
        <h2>Air Filter Recommendation tool</h2>
        </div>
		<div>
			<p>This tool helps recommend how many of the available models of portable air filters at different fan speeds will be required to meet current recommendations to reduce the risk of transmission of respiratory viruses like SARS-CoV-2</p> 
			<p>At the moment this database only includes air filters that use HEPA 13 filters and excludes any that have additional electronic cleaning features like ionisation and plasma that have insufficient evidence behind their efficacy and may result in production of undesirable pollutants. If manufacturers of devices using lower efficiency filters can provide reliable data on the clean air delivery rate for small particles (CADR Smoke), these will considered for inclusion</p>
			<p>We cannot guarantee the accuracy of manufacturer claims on device performance, nor provide reliable estimates of annual filter costs which rely on characteristics on the filter and environment in which the filter is used.</p>
			<p>Australian data source <a href="https://twitter.com/drpieterpeach">Pieter Peach<a>, US data source <a href="https://twitter.com/marwa_zaatari">Marwa Zaatari</a>, UK data source <a href="https://twitter.com/PlasticFull">Stefan Stojanovic<a></p>
			<p>Please contact <a href="https://twitter.com/drpieterpeach">Pieter Peach<a> with any feedback and follow <a href="https://twitter.com/cleanairstars">Cleanairstars<a> for updates.</p>
		</div>

        <div class="row g-5">

        <form action="" method="post" class="needs-validation" novalidate>
            <div class="row g-5">
                <!-- HEPA Form -->
                <div class="col-md-12">
                    <label for="country" class="form-label">Country</label>
                    <select class="form-select" id="country" name="country" required>
                        <option selected disabled value="">Choose...</option>
                        <?php foreach ($countries as $key => $value) {
                        echo '<option value="'.$value.'">'.$value.'</option>';
                        } ?>
                    </select>
                    <div class="invalid-feedback">
                        Please select a Country.
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="max-an" class="form-label">Acceptable Noise Level</label>
					<div>
                        <a href="#" data-bs-trigger="hover focus" data-bs-toggle="popover" title="Decibel (dBA) limit guide" data-bs-content="Tolerability of noise from air filters depends greatly on existing ambient noise levels. The below recommendations are a guide only. <ul class='list-group list-group-flush'>
                                                    <li class='list-group-item'>30-35dBA Sleep</li>
                                                    <li class='list-group-item'>40-50dBA Classroom</li>
                                                    <li class='list-group-item'><50dBA Quiet Restaurant, Office</li>
                                                    <li class='list-group-item'><60dBA Loud Restaurant, Gym, Childcare</li>
                                                    <li class='list-group-item'>No Maximum - Acceptable for loud environments </li>
                                                    </ul>" data-bs-html="true">
                            <p>Noise limit guide <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill" viewBox="0 0 16 16"><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"></path></svg></p>
                        </a>
					</div>
                    <select class="form-select" id="max-an" name="max-an" required>
                        <option value="0">No maximum</option>
                        <?php foreach ($maxANoise as $key => $value) {
                        echo '<option value="'.$value.'">'.$value.' dBA</option>';
                        } ?>
                    </select>
                    <div class="invalid-feedback">
                        Please select Max Acceptable Noise.
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="wifi" class="form-label">Wifi Requirement</label>
					<div>
                        <a href="#" data-bs-trigger="hover focus" data-bs-toggle="popover" title="Do I need the filter to have Wifi?" data-bs-content="If you need to be able to control your filter remotely or schedule the filter you will usually require Wifi connectivity. Some filters that turn on and resume at their previous setting if turned on at the power plug can simply be connected to a power plug timer if they don't have Wifi." data-bs-html="true">
                            <p>Do I need the filter to have Wifi? <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill" viewBox="0 0 16 16"><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"></path></svg></p>
                        </a>
					</div>
                    <select class="form-select" id="wifi" name="wifi" required>
                        <option value="Not fussed">Not fussed</option>
                        <option value="Yes">Yes</option>
                    </select>
                    <div class="invalid-feedback">
                        Please select a Wifi Requirement.
                    </div>
                </div>
                <div class="col-md-12">
                    <label for="prefilter" class="form-label">Prefilter Requirement</label>
					<div>
                        <a href="#" data-bs-trigger="hover focus" data-bs-toggle="popover" title="When do I need a prefilter?" data-bs-content="Prefilters are a thin filter in front of the main filter that captures large dust and particles. It is useful in dusty environments where the dust can be kept off the main filter and vacuumed regularly, prolonging the life of the main filter" data-bs-html="true">
                            <p>When do I need a prefilter? <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill" viewBox="0 0 16 16"><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"></path></svg></p>
                        </a>
					</div>
                    <select class="form-select" id="prefilter" name="prefilter" required>
                        <option value="Not fussed">Not fussed</option>
                        <option value="Yes">Yes</option>
                    </select>
                    <div class="invalid-feedback">
                        Please select a Prefilter Requirement.
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="ach" class="form-label">Air Changes per Hour (ACH) or L/person/second (Advanced)</label>
					<div>
                        <a href="#" data-bs-trigger="hover focus" data-bs-toggle="popover" title="What is ACH?" data-bs-content="The number of times the air in a space is exchanged per hour is the Air Changes per Hour (ACH). The World Health Organisation recommends a minimum of 6 ACH. This may not be appropriate for large spaces where L/person/second may be a more appropriate target and HVAC engineering advice should be sought." data-bs-html="true">
                            <p>What is ACH? <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill" viewBox="0 0 16 16"><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"></path></svg></p>
                        </a>
					</div>
                    <select name="ach" class="form-select" id="ach" required>
                        <!--option selected disabled value="">Choose...</option-->
                        <option value="ach" selected>6 Air Changes per Hour (ACH)</option>
                        <option value="10_lps">10 L/person/second (Absolute minimum)</option>
                        <option value="20_lps">20 L/person/second (Ideal, non-vigorous activity)</option>
                        <option value="50_lps">50 L/person/second (Ideal, vigorous activity eg Exercise, Dancing)</option>
                    </select>
                    <div class="invalid-feedback">
                        Select 6 Air Changes per Hour (ACH) or L/person/second.
                    </div>
                </div>
                <div class="col-md-4" id="rms">
                    <label for="room-size" class="form-label">Room Volume = Width (m or feet) x Length (m or feet) x Height (m or feet)</label>
                    <input type="text" class="form-control" id="room-size" name="room-size" placeholder="Room Volume (eg 100)">
                    <div class="invalid-feedback">
                        Please enter your room size.
                    </div>
                </div>
                <div class="col-md-2" id="rms-type">
                    <label for="m3-or-cu" class="form-label">m3 or cubic feet</label>
                    <select class="form-select" id="m3-or-cu" name="m3-or-cu">
                        <option value="m3">m3</option>
                        <option value="cubic">cubic feet</option>
                    </select>
                    <div class="invalid-feedback">
                        Please select m3 or cubic feet.
                    </div>
                </div>
                <div class="col-md-6" id="noc">
                    <label for="no-of-occ" class="form-label">Number of occupants</label>
                    <input type="text" class="form-control" id="no-of-occ" name="no-of-occ" placeholder="Number of occupants">
                    <div class="invalid-feedback">
                    Please enter the number of occupants.
                    </div>
                </div>
                <div class="col-md-12">
                    <button class="w-100 btn btn-primary btn-lg" name="submit" type="submit">Find Your Filters</button>
                </div>
                <!-- End HEPA Form -->
            </div>
        </form>

        </div>

        <?php if(isset($hepa_result)) { ?>

        <!-- Result(s) -->       
        <div class="container mt-5 mb-5" id="result-page">
            <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-primary">Result(s)</span>
                <span class="badge bg-primary rounded-pill"><?php echo $total; ?></span>
            </h4>
            <ul class="list-inline">
                <?php echo (isset($max_an)) ? '<li class="list-inline-item">Max Acceptable Noise: '.$max_an.'.</li>' : ''; ?>
                <?php echo (isset($wifi)) ? '<li class="list-inline-item">Wifi: '.$wifi.'.</li>' : ''; ?>
                <?php echo (isset($ach)) ? '<li class="list-inline-item">Type: '.$ach.'.</li>' : ''; ?>
                <?php echo ($ach == 'ach') ? '<li class="list-inline-item">Room Size: '.$room_size.'.</li>' : ''; ?>
                <?php echo ($ach == 'ach') ? '<li class="list-inline-item">Room Type: '.$rms_type.'.</li>' : ''; ?>
                <?php echo ($ach == 'lps') ? '<li class="list-inline-item">No. Occupants: '.$no_of_occ.'.</li>' : ''; ?>
                <?php echo (isset($prefltr)) ? '<li class="list-inline-item">Prefilter: '.$prefltr.'.</li>' : ''; ?>
            </ul>

            <div class="d-flex justify-content-center row">
                <div class="col-md-12">

                <?php
                if(!empty($hepa_result)) {
                    foreach ($hepa_result as $key => $value) { 
                    
                        $ach_needs = $value['ACH needs'];
                        $ach_needs_minone = $ach_needs - 1;

                ?>

                    <div class="row p-2 bg-white border rounded mt-2">
                        <div class="col-md-3 mt-1">
                            <img class="img-fluid img-responsive rounded product-image" src="<?php echo $value[$image]; ?>" alt="<?php echo $value[$model]; ?>">
                        </div>
                        <div class="col-md-6 mt-1">
                            <h5><?php echo $value[$model]; ?></h5>
                            <div class="d-flex flex-row">
                                <small class="text-muted">
                                    <ul class="list-group list-group-flush">
                                    <?php if($ach == 'ach'){ ?>
                                        <li class="list-group-item"><i><?php echo $ach_needs; ?> units at above fan setting required for 6 air changes per hr</i></li>
                                        <li class="list-group-item"><i><?php echo $value['ACH']; ?> ACH for <?php echo $ach_needs; ?> devices</i></li>
                                        <?php echo ($ach_needs >= 2) ? '<li class="list-group-item"><i>'.$value['ACH -1'].' ACH for '.$ach_needs_minone.' devices for total <b>'.$value['currency_format'].$ach_needs_minone * $value[$cost].'</b></i></li>' : ''; ?>
                                    <?php } else { ?>
                                        <li class="list-group-item"><i><?php echo $ach_needs; ?> devices required for <?php echo trim($ach, '_lps');?>L/p/s</i></li>
                                        <li class="list-group-item"><i><?php echo $value['ACH']; ?> L/p/s for <?php echo $ach_needs; ?> devices</i></li>
                                        <?php echo ($ach_needs >= 2) ? '<li class="list-group-item"><i>'.$value['ACH -1'].' L/p/s for '.$ach_needs_minone.' devices</i></li>' : ''; ?>
                                    <?php } ?>
                                    </ul>
                                </small>
                            </div>
                            <div class="mt-1 mb-1 spec-1">
                                <ul class="list-group list-group-horizontal-md">
                                    <?php echo (isset($value[$cost])) ? '<li class="list-group-item flex-fill">Cost : '.$value['currency_format'].$value[$cost].' '.$value['currency'].' each</li>' : ''; ?>
                                    <?php echo (isset($value[$filterCost])) ? '<li class="list-group-item flex-fill">Filter cost: '.$value[$filterCost].'</li>' : ''; ?>
                                    <?php echo (isset($value[$title_wifi])) ? '<li class="list-group-item flex-fill">Wifi: '.$value[$title_wifi].'</li>' : ''; ?>
                                </ul>
                                <ul class="list-group list-group-horizontal-md">
                                    <?php echo (isset($value[$cadr_m3])) ? '<li class="list-group-item flex-fill">CADR (m3/hr): '.round($value[$cadr_m3]).'</li>' : ''; ?>
                                    <?php echo (isset($value[$cadr_cubic])) ? '<li class="list-group-item flex-fill">CADR (Cubic feet/min): '.round($value[$cadr_cubic]).'</li>' : ''; ?>
                                </ul>
                                <ul class="list-group list-group-horizontal-md">
                                    <?php echo (isset($value[$cadr_litre])) ? '<li class="list-group-item flex-fill">CADR (Litre/sec): '.round($value[$cadr_litre]).'</li>' : ''; ?>
                                    <?php echo (isset($value[$noise_dBA])) ? '<li class="list-group-item flex-fill">Noise (dBA): '.$value[$noise_dBA].'</li>' : ''; ?>                                  
                                </ul>
                                <ul class="list-group list-group-horizontal-md">
                                    <?php echo (isset($value[$prefilter])) ? '<li class="list-group-item flex-fill">Prefilter: '.$value[$prefilter].'</li>' : ''; ?>
                                </ul>
                            </div>
                        </div>
                        <div class="align-items-center align-content-center col-md-3 border-left mt-1">
                            <div class="d-flex flex-row align-items-center">
                                <h4 class="mr-1"><?php echo $value['currency_format'].$value['Total Cost']; ?></h4>
                                <span>&nbsp;<?php echo $value['currency']; ?></span>
                            </div>
                            <h6 class="text-success">Total Upfront Cost</h6>
			    <div class="d-flex flex-row align-items-center">
                                <h4 class="mr-1"><?php echo $value['currency_format'].($value[$filterCost] * $ach_needs) ; ?></h4>
                                <span>&nbsp;<?php echo $value['currency']; ?></span>
                            </div>
                            <h6 class="text-success">Total Filter Replacement Cost</h6>
                            <div class="d-flex flex-column mt-4">
                                <?php echo (isset($value[$details])) ? '<a class="btn btn-outline-primary btn-sm" href="'.$value[$details].'" target="_blank">Details</a>' : ''; ?>
                                <?php echo (isset($value[$buy])) ? '<a class="btn btn-primary btn-sm mt-2" href="'.$value[$buy].'" target="_blank">Buy Now</a>' : ''; ?>
                            </div>
                        </div>
                    </div>

                <?php }} else { ?>

                    <h3>No result found</h3>

                <?php } ?>

                </div>
            </div>
        </div>

        <?php } ?>

    </main>

    <footer class="my-5 pt-5 text-muted text-center text-small">
        <p class="mb-1">&copy; 2021-<?php echo date("Y"); ?> Clean Air Stars</p>
    </footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <script src="https://getbootstrap.com/docs/5.1/examples/checkout/form-validation.js"></script>

    <script>
        // Hiding ACH and RMS fields
        $(document).ready(function() {
            //$('#rms').hide();
            //$('#rms-type').hide();
            $('#noc').hide();
            $('#ach').change(function() {
                if ($(this).val() == 'ach') {
                    $('#rms').show();
                    $('#room-size').prop('required', true);
                    $("#rms").attr('required', '');
                    $('#rms-type').show();
                    $('#noc').hide();
                    $('#no-of-occ').prop('required', false);
                } else {
                    $('#rms').hide();
                    $('#room-size').prop('required', false);
                    $('#rms-type').hide();
                    $('#noc').show();
                    $('#no-of-occ').prop('required', true);
                }
            });
        });
		// Popover
		var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
		var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
		return new bootstrap.Popover(popoverTriggerEl)
		})

        // Scroll to result page
        <?php echo (isset($scroll)) ? $scroll : null; ?>
    </script>

</body>
</html>
