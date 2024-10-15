<?php
require_once(__DIR__.'/includes/init.php');

if (!file_exists(CONFIG)) {
    header("Location: admin/login.php");
    die();
}


// Set data for Countries dropdown
$countries  = countries(config('sheet_id'));

// Set default variables value
$submitted = false;
$country = $countries[0];
$max_an = 0;
$wifi = $VALUE_NO;
$schedule = $VALUE_NO;
$ach = 6;
$room_size = 60;
$rms_type = 'm3';
$no_of_occ = 1;
$prefltr = $VALUE_NO;
$tariff = '0.22';
$diy = 'No';
$max_units = 10;
$frs = 12;
$lifetime = 4;

// If form is submitted
if(isset($_GET['submit'])){

    $submitted = true;

    // Set data from POST
    $country = $_GET['country'] ?? $country;
    $max_an = $_GET['max-an'] ?? $max_an;
    $wifi = $_GET['wifi'] ?? $wifi;
    $schedule = $_GET['schedule'] ?? $schedule;
    $ach  = $_GET['ach'] ?? $ach;
    $room_size = $_GET['room-size'] ?? $room_size;
    $rms_type = $_GET['m3-or-cu'] ?? $rms_type;
    $no_of_occ = $_GET['no-of-occ'] ?? $no_of_occ;
    $prefltr = $_GET['prefilter'] ?? $prefltr;
    $tariff = $_GET['tariff'] ?? $tariff;
    $diy = $_GET['diy'] ?? $diy;
    $max_units =  $_GET['max_units'] ?? $max_units;
    $frs =  $_GET['filter-rs'] ?? $frs;
    $lifetime =  $_GET['lifetime'] ?? $lifetime;

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
        global $VALUE_NO;
        if($wifi != $VALUE_NO){
            return (strtolower($item['Wifi']) == strtolower($wifi));
        } else {
            return true;
        }
    });

    // Filter by schedule
    $filter_result = array_filter($filter_result, function($item) use ($schedule){
        global $VALUE_NO;
        if($schedule != $VALUE_NO){
            return (strtolower($item['Schedulable']) == strtolower($schedule));
        } else {
            return true;
        }
    });

    // Filter by prefilter
    //$filter_result = array_filter($filter_result, function($item) use ($prefltr){
    //    if($prefltr != $VALUE_NO){
    //        return (strtolower($item['Prefilter']) == strtolower($prefltr));
    //    } else {
    //        return true;
    //    }
    // });

    // Filter by DIY
    $filter_result = array_filter($filter_result, function($item) use ($diy){
        global $VALUE_YES;
        if($diy != $VALUE_YES){
            return (strtolower($item['DIY']) != strtolower($VALUE_YES));
        } else {
            return true;
        }
    });

    // Calculate ACH and Total Cost
    $types = array(
        'cadrm3'    => $cadr_m3,
        'cadrcubic' => $cadr_cubic,
        'cadrlitre' => $cadr_litre,
        'cost'      => $cost,
        'noisedba'  => $noise_dBA,
    );
    $achs = array(
        'room_size' => $room_size,
        'room_type' => $rms_type,
        'no_off_occ'=> $no_of_occ
    );
    $hepa_result = calculateACH($filter_result, $ach, $max_units, $types, $achs);

    // Filter items that doesn't have Watts and Filter cost value
    // $hepa_result = array_filter(
    //     $hepa_result,
    //     function($item){
    //         if($item['Watts'] != '' && $item['Filter cost'] != ''){
    //             return true;
    //         }
    //     }
    // );

    // Filter total dBA by max acceptable noise
    //$hepa_result = array_filter($hepa_result, function($item) use ($max_an){
    //    if($max_an != 0){
    //        return ($item['Total dBA'] <= $max_an);
    //    } else {
    //        return true;
    //    }
    //});

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
	
    <link rel="stylesheet" href="includes/share.css">

    <style>
        .fa-solid.fa-circle-info {
            color: blue;
        }
        .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
        }
		
		.horizontal-divider {
		  border: none; /* Remove default hr border */
		  border-top: 1px solid grey; /* Add a 1px solid border at the top */
		  margin: 40px 0px 0px 0px; /* Optional: Add spacing above and below the hr */
		}

        @media (min-width: 768px) {
        .bd-placeholder-img-lg {
            font-size: 3.5rem;
        }
        }
    </style>
	<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=G-PJE4FN2C9M"></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());

		  gtag('config', 'G-PJE4FN2C9M');
		</script>
		  
	    <script type="text/javascript">
	        function googleTranslateElementInit() {
	            new google.translate.TranslateElement(
	                {pageLanguage: 'en'},
	                'google_translate_element'
	            );
	        }
	    </script>
 
	    <script type="text/javascript"
	            src=
	"https://translate.google.com/translate_a/element.js?
	cb=googleTranslateElementInit">
	    </script>
		  
</head>
<body class="bg-light">
    <div id="google_translate_element"></div>
    <div class="container">
    <main>
        <div class="py-5 text-center">
            <h2>Air Filter Recommendation tool</h2>
        </div>
	<div>
	   <p>
                This tool helps recommend <strong><i>how many</i></strong> of a particular model of portable air cleaner <strong><i>at what fan speed</strong></i> will be required to meet current recommendations for <strong><i>total airflow</strong></i> for a given room size or occupancy to reduce the risk of transmission of respiratory viruses like SARS-CoV-2 in poorly ventilated indoor spaces. 
	   </p>
	   <p>
	      	<strong>This is a non-profit public service that receives no commission for sales of any goods or services</strong>
	   </p>
	   <p>
		Choosing the right device(s) depends on the amount of noise that would be comfortably tolerated given the room's existing ambient noise level and space available to fit multiple devices. For example, in quiet rooms where additional noise is less tolerable, multiple devices operating at quieter, low speeds may be preferable to a single device running at a louder, high speed. Additionally, multiple devices spaced around a room will likely result in better distribution of filtration.
           </p> 
	   <p>
                The database includes air cleaners using HEPA 13 filters. If reliable data or estimates on the clean air delivery rate
                (CADR) for small particles (Smoke, 0.1-1 microns) is available for devices using filters below HEPA 13 efficiency, these
                have been included. Devices that have electronic cleaning features such as ionisation, plasma, ultraviolet light, and photocatalytic
                oxidisation have been excluded or their presence made apparent.
           </p>
	   <p>
                No guarantees can be provide on the accuracy of manufacturer claims on features or performance, nor provide reliable estimates of annual filter costs that rely on characteristics on the filter and environment in which it will be used. Improving indoor air quality with better ventilation and filtration helps reduce the risk of transmission, but cannot eliminate risk completely. The use of well-fitted masks, distancing, and limiting exposure time in poorly ventilated environments are important components of risk reduction.
           </p>
	   <p>
                Full dataset is available <a href="https://docs.google.com/spreadsheets/d/17j6FZwvqHRFkGoH5996u5JdR7tk4_7fNuTxAK7kc4Fk/edit?usp=sharing" target="_blank">here</a>.
				Github repository <a href="https://github.com/ppeach/airfilterrecommendation" target="_blank">here</a>.
                Australian data source <a rel="me" href="https://mastodon.social/@pieterpeach">Pieter Peach</a>,
                initial US data source <a href="https://twitter.com/marwa_zaatari" target="_blank">Marwa Zaatari</a>,
                initial UK data source <a href="https://twitter.com/PlasticFull" target="_blank">Stefan Stojanovic</a>
            </p>
	    <p>
                Direct link to the tool is <a href="https://filters.cleanairstars.com" target="_blank">filters.cleanairstars.com</a>
            </p>
	    <p>
                Contact and follow <a rel="me" href="https://mas.to/@cleanairstars" target="_blank">Cleanairstars</a> for feedback and updates.
            </p>
	    <p>
		Supported by a grant from the <a rel="me" href="https://balvi.io" target="_blank">Balvi Foundation</a> 
	     </p>
            <p>&nbsp;</p>
	</div>

        <div class="row g-5">

        <form action="." method="get" class="needs-validation" novalidate>
            <div class="row g-5">
                <!-- HEPA Form -->
                <div class="col-md-6">
                    <label for="country" class="form-label">Country</label>
                    <select class="form-select" id="country" name="country" required>
                        <option <?php if(!$submitted) {echo 'selected';} ?> disabled value="">Choose...</option>
                        <?php foreach ($countries as $key => $value) {
                            if ($country == $value) {
                                echo '<option selected value="'.$value.'">'.$value.'</option>';
                            } else {
                                echo '<option value="'.$value.'">'.$value.'</option>';
                            }
                        } ?>
                    </select>
                    <div class="invalid-feedback">
                        Please select a Country.
                    </div>
                </div>
                <div class="col-md-6">
                	<label for="max-an" class="form-label">Acceptable Noise Level <a data-bs-trigger="hover focus" data-bs-toggle="popover" title="Decibel (dBA) limit guide" data-bs-content="Tolerability of noise from air filters depends greatly on existing ambient noise levels. The below recommendations are a guide only. Some devices may have less tolerable noise characteristics even at low dBA. <ul class='list-group list-group-flush'>
                                                <li class='list-group-item'>30-40dBA Sleep</li>
                                                <li class='list-group-item'>40-45dBA Classroom, Quiet Restaurant & Office</li>
                                                <li class='list-group-item'>40-50dBA Loud Office & Childcare</li>
                                                <li class='list-group-item'><60dBA Loud Restaurant, Gym</li>
                                                <li class='list-group-item'>>60dBA - Acceptable for loud environments </li>
                                                </ul>" data-bs-html="true">
                        <?=$SVG_INFO;?>
                    </a>
                	</label>
    			<select class="form-select" id="max-an" name="max-an" required>
        		<option disabled value="">Choose...</option>
      			  <?php 
        		// Set default selection to 45 if no value has been submitted
      			  $max_an = $max_an ?? '45';  // Assuming $max_an is the submitted value variable

    			    foreach ($maxANoise as $key => $value) {
     		       // Check if the current loop value is equal to the submitted or default value
       		     $selected = ($max_an == $value) ? 'selected' : '';
      		      echo '<option ' . $selected . ' value="' . $value . '">' . $value . ' dBA</option>';
   		     } ?>
    			</select>
   		  <div class="invalid-feedback">
     		   Please select Max Acceptable Noise.
  		  </div>
		</div>

		<div class="col-md-6">
	        <label for="ach" class="form-label">Clean air delivery target <a data-bs-trigger="hover focus" data-bs-toggle="popover" title="What is L/p/s and ACH?" data-bs-content="The number of times the air in a space is exchanged per hour is the Air Changes per Hour (ACH). The World Health Organisation recommends a minimum of 6 ACH. This may not be appropriate for larger spaces where WHO's recommendation of minimum 10 L/person/second (NOTE: use the rated people capacity for the space) may be a more appropriate an realistic target. See https://itsairborne.com/ashrae-241-control-of-infectious-aerosols-part-2-equivalent-clean-airflow-rates-76a511769d4d for further information" data-bs-html="true">
	                <?=$SVG_INFO;?>  <a href="https://itsairborne.com/ashrae-241-control-of-infectious-aerosols-part-2-equivalent-clean-airflow-rates-76a511769d4d"> more info</a>
	            </a></label>
	        <div>
				<div class="btn-group" style="width: 100%;" role="group" aria-label="Target Selection">
				    <button type="button" class="btn btn-outline-primary" id="occupancy-btn">Room occupancy</button>
				    <button type="button" class="btn btn-outline-primary" id="room-size-btn">Room size</button>
				</div>
	            <a data-bs-trigger="hover focus" data-bs-toggle="popover" title="Minumum Airflow" data-bs-content="The World Health Organisation recommends a minimum of 160 liters per second of clean air per infectious occupant. See link to 'Infection prevention and control in the context of COVID-19: a guideline, 21 December 2023' in more info" data-bs-html="true">
	                <div class="small-notice-box">Note: Minimum total of 160L/sec (576m3/hr) will be recommended as per WHO <?=$SVG_INFO;?> <a href="https://www.who.int/publications/i/item/WHO-2019-nCoV-IPC-guideline-2023.4">more info</a></div>
	            </a>
	        </div>
			<select name="ach" class="form-select" id="ach" required>
			    <option disabled>
			        <!-- disabled header for readability -->
			        --Based on room size--
			    </option>
			    <?php foreach($ACH_OPTIONS as $key => $value) { ?>
			    <option value="<?= $key ?>" <?php if($key == $ach) {echo 'selected';} ?> data-mode="ach"><?= $value ?></option>
			    <?php } ?>
			    <option disabled></option><option disabled>
			        <!-- just a separator between ACH amd LPS for readability -->
			        --Based on occupancy (WHO recommendation)--
			    </option>
			    <!-- Check if the form has been submitted by checking if $_GET['ach'] is set -->
			    <option value="10" <?php if(!isset($_GET['ach'])) { echo 'selected'; } else if($_GET['ach'] == '10') { echo 'selected'; } ?> data-mode="lps">10 L/p/s (Minimum, WHO Recommendation)</option>
			    <option disabled></option><option disabled>
			        --Based on occupancy (US ASHRAE 241)--
			    </option>
			    <?php foreach($LPS_OPTIONS as $key => $value) { ?>
			        <option value="<?= $key ?>" <?php if($key == $ach) {echo 'selected';} ?> data-mode="lps"><?= $value ?></option>
			    <?php } ?>
			</select>
	        
	        <div class="invalid-feedback">
	            Select L/person/second or 6 Air Changes per Hour (ACH)
	        </div>
	    </div>

                <div class="col-md-4" id="rms">
                    <label for="room-size" class="form-label">Room Volume (Width x Length x Height) in m or feet <a data-bs-trigger="hover focus" data-bs-toggle="popover" title="Consider using a standard ceiling height" data-bs-content="For spaces with very high ceilings it may be appropriate to use a standard ceiling height of 2.5m or 9ft" data-bs-html="true">
                        <?=$SVG_INFO;?>
                    </a>
				</label>
                    <input
                        type="number"
                        class="form-control"
                        id="room-size"
                        name="room-size"
                        placeholder="Cubic room Volume (eg 100)"
                        <?= ($submitted) ? 'value="'.$room_size.'"': ''; ?>
                    >
                    <div class="invalid-feedback">
                        Please enter your room volume as a single number (eg 200).
                    </div>
                </div>
                <div class="col-md-2" id="rms-type">
                    <label for="m3-or-cu" class="form-label">m3 or cubic feet</label>
                    <select class="form-select" id="m3-or-cu" name="m3-or-cu">
                        <?php foreach($MEASUREMENT_OPTIONS as $key => $value) { ?>
                        <option value="<?= $key ?>" <?php if($key == $rms_type) {echo 'selected';} ?> ><?= $value ?></option>
                        <?php } ?>
                    </select>
                    <div class="invalid-feedback">
                        Please select m3 or cubic feet.
                    </div>
                </div>
                <div class="col-md-6" id="noc">
                    <label for="no-of-occ" class="form-label">Rated occupant capacity for the space.</label>
                    <input
                        type="text"
                        class="form-control"
                        id="no-of-occ"
                        name="no-of-occ"
                        placeholder="Number of occupants"
                        <?= ($submitted) ? 'value="'.$no_of_occ.'"': ''; ?>
                    >
                    <div class="invalid-feedback">
                    Please enter the rated occupant capacity for the space.
                    </div>
                </div>

                <details class="mt-5">
                <summary class="text-primary fs-5">Advanced</summary>

                <div class="row g-5 pt-5">
                    <div class="col-md-4">
                        <label
                            for="max-units"
                            class="form-label"
                            data-bs-trigger="hover focus"
                            data-bs-toggle="popover"
                            title="Maximum Units"
                            data-bs-content="Enter the desired maximum number of units to use to treat the room.<br />
                            This will prevent undesirable suggestions, such as using 15 low-speed units
                            where there is space for only 5 high-speed units."
                            data-bs-html="true"
                        >
                            Maximum units Allowed
                            <i class="fa-solid fa-circle-info"></i>
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            name="max_units"
                            id="max-units"
                            <?= ($submitted) ? 'value="'.$max_units.'"' : 'value="10"'; ?>
                        >
                    </div>
                    <?php /*
                    <div class="col-md-6">
                        <label for="wifi" class="form-label">Wifi Requirement</label>
                        <div>
                            <a data-bs-trigger="hover focus" data-bs-toggle="popover" title="Do I need the filter to have Wifi?" data-bs-content="If you need to be able to control your filter remotely or schedule the filter you will usually require Wifi connectivity. Some filters that turn on and resume at their previous setting if turned on at the power plug can simply be connected to a power plug timer if they don't have Wifi." data-bs-html="true">
                                <p>Do I need the filter to have Wifi? <?=$SVG_INFO;?></p>
                            </a>
                        </div>
                        <select class="form-select" id="wifi" name="wifi" required>
                            <option value="<?= $VALUE_NO ?>" <?php if($wifi == $VALUE_NO || !$submitted) {echo 'selected';} ?>><?= $DISPLAY_WIFI_NO ?></option>
                            <option value="Yes" <?php if($wifi == $VALUE_YES) {echo 'selected';} ?> >Yes</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a Wifi Requirement.
                        </div>
                    </div>
                    */ ?>
                    <div class="col-md-4">
                        <label for="schedule" class="form-label">Scheduling ability <a data-bs-trigger="hover focus" data-bs-toggle="popover" title="Do I need to be able to schedule the device?" data-bs-content="If you need to be able to schedule the portable filter (so you don't forget to turn it on or off) you will usually either require Wifi connectivity, or use portable filters that turn on and resume at their previous setting if turned on at the power plug (a smart power plug or plug timer will be required to do this). Devices that meet these criteria will be included." data-bs-html="true">
                                <?=$SVG_INFO;?>
                            </a>
                        </label>
                        <select class="form-select" id="schedule" name="schedule" required>
                            <option value="<?= $VALUE_NO ?>" <?php if($schedule == $VALUE_NO || !$submitted) {echo 'selected';} ?>><?= $DISPLAY_SCHEDULE_NO ?></option>
                            <option value="<?= $VALUE_YES ?>" <?php if($schedule == $VALUE_YES) {echo 'selected';} ?> ><?= $DISPLAY_SCHEDULE_YES ?></option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a scheduling requirement.
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="diy" class="form-label">Include DIY devices?</label>
                            <a data-bs-trigger="hover focus" data-bs-toggle="popover" title="What is a DIY device?" data-bs-content="Do it yourself air filters are alternatives to commercially available devices that, when properly constructed, can be an economical and effective alternative. Clean air delivery rates (CADR) for these devices in a way that can be compared directly to AHAM CADR Smoke tested devices are currently estimated only." data-bs-html="true">
                                <?=$SVG_INFO;?>
                            </a>
                        <select class="form-select" id="diy" name="diy" required>
                            <option value="<?= $VALUE_NO ?>" <?php if($diy == $VALUE_NO || !$submitted) {echo 'selected';} ?> ><?= $DISPLAY_DIY_NO ?></option>
                            <option value="<?= $VALUE_YES ?>" <?php if($diy == $VALUE_YES) {echo 'selected';} ?> ><?= $DISPLAY_DIY_YES ?></option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a DIY filters.
                        </div>
                    </div>
                    <?php
                    /*
                    <div class="col-md-6">
                        <label for="prefilter" class="form-label">Vacuumable/Washable Prefilter</label>
                        <div>
                            <a data-bs-trigger="hover focus" data-bs-toggle="popover" title="When do I need a washable/vacuumable prefilter?" data-bs-content="Prefilters are a thin filter in front of the main filter that captures large dust and particles. It is useful in dusty environments with partial natural ventilation where the dust can be kept off the main filter and vacuumed/washed regularly, prolonging the life and airflow of the main filter." data-bs-html="true">
                                <p>When do I need a washable/vacuumable prefilter? <?=$SVG_INFO;?></p>
                            </a>
                        </div>
                        <select class="form-select" id="prefilter" name="prefilter" required>
                            <option value="<?= $VALUE_NO ?>"><?= $DISPLAY_PREFILTER_NO ?>/option>
                            <option value="<?= $VALUE_YES ?>"><?= $DISPLAY_PREFILTER_YES ?></option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a Prefilter Requirement.
                        </div>
                    </div>
                    */
                    ?>
                    <div class="col-md-4">
                        <label
                            for="tariff"
                            class="form-label"
                            data-bs-trigger="hover focus"
                            data-bs-toggle="popover"
                            title="Electricity Tariff"
                            data-bs-content="Enter your per-kWh electricity tariff (eg 0.22) and an estimated annual electricity running cost will be calculated in your results"
                            data-bs-html="true"
                        >
                            Electricity Tariff
                            <i class="fa-solid fa-circle-info"></i>
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            name="tariff"
                            id="tariff"
                            <?= ($submitted) ? 'value="'.$tariff.'"' : 'value="0.22"'; ?>
                        >
                    </div>
                    <div class="col-md-4" id="frs">
                        <label 
                            for="filter-rs" 
                            class="form-label"
                            data-bs-trigger="hover focus"
                            data-bs-toggle="popover"
                            title="Filter replacement schedule"
                            data-bs-content="The frequency of filter replacement will depend on how many hours per week and under what the conditions the filter are used in. This will impact the total cost of ownership calculations."
                            data-bs-html="true"
                        >
                            Filter replacement schedule
                            <i class="fa-solid fa-circle-info"></i>
                        </label>
                        <select class="form-select" id="filter-rs" name="filter-rs">
                            <?php foreach($FRS_OPTIONS as $key => $value) { ?>
                            <option value="<?= $key ?>" <?php if($key == $frs) {echo 'selected';} ?> ><?= $value ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback">
                            Please select filter replacement schedule.
                        </div>
                    </div>
                    <div class="col-md-4" id="frs">
                        <label 
                            for="lifetime" 
                            class="form-label"
                            data-bs-trigger="hover focus"
                            data-bs-toggle="popover"
                            title="Assumed device lifetime"
                            data-bs-content="How long a device is expected to last before replacement is required. This will impact the total cost of ownership calculations."
                            data-bs-html="true"
                        >
                            Assumed device lifetime
                            <i class="fa-solid fa-circle-info"></i>
                        </label>
                        <select class="form-select" id="lifetime" name="lifetime">
                            <?php foreach($AFL_OPTIONS as $key => $value) { ?>
                            <option value="<?= $key ?>" <?php if($key == $lifetime) {echo 'selected';} ?> ><?= $value ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback">
                            Please select Assumed device lifetime.
                        </div>
                    </div>

                </div>    
                </details>
                
                <div class="col-md-12">
                    <button class="w-100 btn btn-primary btn-lg" name="submit" type="submit" value="submit">Find Your Filters</button>
                </div>
                <!-- End HEPA Form -->
            </div>
        </form>

        </div>

        <?php if(isset($hepa_result)) {
            (array_key_exists($ach, $ACH_OPTIONS)) ? $type_ach = 'ACH_'.$ach : $type_ach = 'LPS_'.$ach; ?>

        <!-- Result(s) -->       
        <div class="container mt-5 mb-5" id="result-page">
            <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-primary">Result(s)</span>
                <div class="d-flex align-items-center">
                    <!--h6 class="mb-0"><span class="badge bg-primary rounded-pill"><?php echo $total; ?></span></h6-->
					<h5>  Sort by:</h5>
                    <div class="col-md ms-2">
                        <select class="form-select form-select-sm" id="sort-options">
                            <option value="upc">Upfront Cost</option>
                            <option value="tco">Total Cost of Ownership</option>
                        </select>
                    </div>
                    <!-- <button class="btn btn-primary btn-sm ms-2" id="sort-results">Sort Ascending</button> -->
                </div>
            </h4>
            <!--ul class="list-inline">
                <?php echo (isset($max_an)) ? '<li class="list-inline-item">Max Acceptable Noise: '.$max_an.' dBA.</li>' : ''; ?>
                <?php echo (isset($wifi)) ? '<li class="list-inline-item">Wifi: '.$wifi.'.</li>' : ''; ?>
		        <?php echo (isset($schedule)) ? '<li class="list-inline-item">Schedulable: '.$schedule.'.</li>' : ''; ?>
                <?php echo (isset($ach)) ? '<li class="list-inline-item">Type: '.$type_ach.'.</li>' : ''; ?>
                <?php echo (array_key_exists($ach, $ACH_OPTIONS)) ? '<li class="list-inline-item">Room Size: '.$room_size.'.</li>' : ''; ?>
                <?php echo (array_key_exists($ach, $ACH_OPTIONS)) ? '<li class="list-inline-item">Room Type: '.$rms_type.'.</li>' : ''; ?>
                <?php echo (!array_key_exists($ach, $ACH_OPTIONS)) ? '<li class="list-inline-item">No. Occupants: '.$no_of_occ.'.</li>' : ''; ?>
                <?php echo (isset($prefltr)) ? '<li class="list-inline-item">Prefilter: '.$prefltr.'.</li>' : ''; ?>
                <?php echo (isset($diy)) ? '<li class="list-inline-item">DIY: '.$diy.'.</li>' : ''; ?>
            </ul-->

            <div class="d-flex justify-content-center row">
                <div class="col-md-12">

                <?php
                if(!empty($hepa_result)) {
                    foreach ($hepa_result as $key => $value) { 
                    
                        $ach_needs = $value['ACH needs'];
                        $ach_needs_minone = $ach_needs - 1;
                        $filter_Cost = $value['currency_format'].$value[$filterCost];
                        $link_filter = ($value[$buyfilter] !== '') ? '<a href="'.$value[$buyfilter].'" target="_blank">'.$filter_Cost.'</a>' : $filter_Cost;
                        $totaldBA = $value['Total dBA'];
                        $tco_normal = 0;
                        if(!!$value[$watts] && !!$value[$filterCost]){
                            $filter_replacement_cost = calculateFRC($value[$filterCost], $ach_needs, $frs, $lifetime);
                            $energyCost = calculateEC($tariff, $value[$watts], $ach_needs);
                            $tco_normal = round(calculateTCO($value['Total Cost'], $filter_replacement_cost, $energyCost['normal'] * $lifetime) / $lifetime);
                            $tco_school = round(calculateTCO($value['Total Cost'], $filter_replacement_cost, $energyCost['school'] * $lifetime) / $lifetime);
                            $tco_office = round(calculateTCO($value['Total Cost'], $filter_replacement_cost, $energyCost['office'] * $lifetime) / $lifetime);
                        }
                ?>

                    <div class="row p-2 bg-white border rounded mt-2 product-item" data-tco="<?=$tco_normal;?>" data-upc="<?=$value['Total Cost'];?>">
                        <div class="col-md-3 mt-2">
                            <div class="d-flex position-relative">
                            <?php if(!empty($value['EnergyStar']) || !empty($value['AHAM'])){ ?>
                                <div class="d-flex position-absolute" style="height:30px;z-index:2">
                                <?php if($value['EnergyStar'] == 'Yes'){ ?>
                                    <img class="img-fluid" src="https://www.ahamdir.com/wp-content/uploads/2019/04/energy_star.png" alt="Energystar Verified">
                                <?php }; if($value['AHAM'] == 'Yes'){ ?>
                                    <img class="img-fluid" src="https://www.ahamdir.com/wp-content/uploads/2019/02/Aham_verifide.jpg" alt="AHAM verfied">
                                <?php } ?>
                                </div>
                            <?php } ?>
                                <img class="img-fluid rounded mx-auto d-block position-relative mt-4" style="max-height:200px;" src="<?php echo $value[$image]; ?>" alt="<?php echo $value[$model]; ?>">
                            </div>
                        </div>
                        <div class="col-md-6 mt-1">
                            <h5><?php echo $value[$model]; ?></h5>
                            <div class="d-flex flex-row">
                                <small class="text-muted">
                                    <ul class="list-group list-group-flush">
                                    <?php if(array_key_exists($ach, $ACH_OPTIONS)){ ?>
                                        <li class="list-group-item"><i><strong><?php echo $ach_needs; ?> units at above fan setting </strong> required for approximately <?= preg_replace('~\D~', '', $ach) ?> air changes per hr</i></li>
                                        <li class="list-group-item"><i><?php echo $value['ACH']; ?> ACH (<?php echo(round($ach_needs*$value[$cadr_m3],0)); ?>m3/hr total) for <?php echo $ach_needs; ?> devices</i></li>
                                        <!--?php echo ($ach_needs >= 2) ? '<li class="list-group-item"><i>'.$value['ACH -1'].' ACH for '.$ach_needs_minone.' devices for total <b>'.$value['currency_format'].$ach_needs_minone * $value[$cost].'</b></i></li>' : ''; ?-->
                                    <?php } else { ?>
                                        <li class="list-group-item"><i><?php echo $ach_needs; ?> devices required for approximately <?php echo trim($ach, '_lps');?>L/p/s</i></li>
                                        <li class="list-group-item"><i><?php echo $value['ACH']; ?> L/p/s for <?php echo $ach_needs; ?> devices</i></li>
                                        <!--?php echo ($ach_needs >= 2) ? '<li class="list-group-item"><i>'.$value['ACH -1'].' L/p/s for '.$ach_needs_minone.' devices</i></li>' : ''; ?-->
                                    <?php } ?>
                                        <li class="list-group-item"><a href="#details-<?php echo $key; ?>" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="details-<?php echo $key; ?>"><i>See more details</i></a></li>
                                    </ul>
                                </small>
                            </div>
                            <div class="mt-1 mb-1 spec-1 collapse" id="details-<?php echo $key; ?>">
                                <ul class="list-group list-group-horizontal-md">
                                    <?php echo (isset($value[$cost])) ? '<li class="list-group-item flex-fill">Cost : '.$value['currency_format'].$value[$cost].' each</li>' : ''; ?>
                                    <?php echo (isset($value[$filterCost])) ? '<li class="list-group-item flex-fill">Filter cost: '.$link_filter.'</li>' : $filter_Cost; ?>
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
                                    <?php echo '<li class="list-group-item flex-fill">Total Noise (dBA): '.$totaldBA.' <a href="#" data-bs-trigger="hover focus" data-bs-toggle="popover" title="What is total noise dB(A)?" data-bs-content="Total noise level is the noise level of multiple devices added together assuming they are placed together with sound level measured at 1m distance. In reality devices will be spaced around a room and noise level experienced at various points around the room will be less than this." data-bs-html="true">'.$SVG_INFO.'</a></li>'; ?>
                                </ul>
                                <?php if(!!$value[$watts]) { ?>
                                    <ul class="list-group list-group-horizontal-md">
                                        <li class="list-group-item flex-fill">
                                            Power consumption: <?php echo ($ach_needs * $value[$watts]); ?>W <?php if($ach_needs != 1) { echo '('.$value[$watts].' W per unit)'; } ?>
                                            <br />
                                            <?php echo ($ach_needs* ($value[$watts] / 1000 )); ?> kWh (1 hour)
                                            &middot;
                                            <?php echo ($ach_needs * ($value[$watts] / 1000)  * 24); ?> kWh (24 hours)
                                        </li>
                                    </ul>
                                <?php } ?>
                                <?php if(isset($value[$notes]) && $value[$notes] != ''){ ?>
                                <div class="alert alert-info"><strong>Notes:</strong> <cite><?php echo $value[$notes];?></cite></div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="align-items-center align-content-center col-md-3 border-left mt-1">
                            <h6 class="text-success">Total Upfront Cost</h6>
							<div class="d-flex flex-row align-items-center">
                                <h4 class="mr-1"><?php echo $value['currency_format'].$value['Total Cost']; ?></h4>
                                <!--span>&nbsp;<?php echo $value['currency']; ?></span-->
                            </div>
							<h6 class="text-success">Total Filter Replacement Cost</h6>
                            <?php if(!!$value[$filterCost]) { ?>
                                <div class="d-flex flex-row align-items-center">
                                    <h4 class="mr-1"><?php echo $value['currency_format'].($value[$filterCost] * $ach_needs) ; ?></h4>
                                    <!--span>&nbsp;<?php echo $value['currency']; ?></span-->
                                </div>
                            <?php } else { ?>
                                <h6 class="text-danger">Filter cost unknown</h6>
                            <?php } ?>

                            

                            <?php if(!!$value[$watts]) { ?>
                                <a href="#electricity-<?php echo $key; ?>" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="electricity-<?php echo $key; ?>"><small><i>See more costs</i></small></a>
								<div class="card card-body mt-3 collapse" id="electricity-<?php echo $key; ?>">
		                            <?php if(!!$value[$watts] && !!$value[$filterCost]){ ?>
		                            <h6 class="text-success">Yearly Total Cost of Ownership</h6>
		                            <div class="d-flex flex-row align-items-center">
		                                <h5 class="mr-1"><?=$value['currency_format'].$tco_normal;?>
		                                    <a data-bs-trigger="hover focus" data-bs-toggle="popover" title="Yearly TCO for 24/7 operation" data-bs-content="24 hrs per day, 7 days per week, 365 days per year assuming <?php echo trim($frs);?> monthly filter replacement schedule and <?php echo trim($lifetime);?> year device lifetime." data-bs-html="true"><?=$SVG_INFO;?>
		                                    </a>
		                                </h5>
									</div>
									<h6>(24/7 operation)</h6>
									<div class="d-flex flex-row align-items-center">
		                                <h5 class="mr-1"><?=$value['currency_format'].$tco_school;?></h5>
									</div>
                                    <h6>(School) <a data-bs-trigger="hover focus" data-bs-toggle="popover" title="Yearly TCO for School" data-bs-content="8 hrs per day, 5 days per week and 39 weeks per year (1560 hrs per year assuming <?php echo trim($frs);?> monthly filter replacement schedule and <?php echo trim($lifetime);?> year device lifetime." data-bs-html="true"><?=$SVG_INFO;?>
		                                    </a>
                                        </a></h6>
									<div class="d-flex flex-row align-items-center">
		                                <h5 class="mr-1"><?=$value['currency_format'].$tco_office;?>
		                                </h5>
		                            </div>
                                    <h6>(Office) <a data-bs-trigger="hover focus" data-bs-toggle="popover" title="Office use" data-bs-content="8 hrs per day, 5 days per week, 52 weeks per year assuming <?php echo trim($frs);?> monthly filter replacement schedule and <?php echo trim($lifetime);?> year device lifetime." data-bs-html="true"><?=$SVG_INFO;?>
                                        </a></h6>
		                            <?php } ?>
									<br>
                                    <h6 class="text-success">Yearly electricity cost</h6>
                                    <div class="d-flex flex-row align-items-center">
                                        <h4 class="mr-1"><?php echo $value['currency_format'].round($energyCost['normal']) ; ?></h4>
                                        <!--span>&nbsp;<?php echo $value['currency']; ?></span-->
                                    </div>
                                    <h6>(24/7 operation)</h6>
                                    <div class="d-flex flex-row align-items-center">
                                        <h4 class="mr-1"><?php echo $value['currency_format'].round($energyCost['school']) ; ?></h4>
                                        <!--span>&nbsp;<?php echo $value['currency']; ?></span-->
                                    </div>
                                    <h6>(School) <a data-bs-trigger="hover focus" data-bs-toggle="popover" title="School use" data-bs-content="Assumes 8 hrs per day, 5 days per week, 39 weeks per year." data-bs-html="true"><?=$SVG_INFO;?>
                                        </a></h6>
                                    <div class="d-flex flex-row align-items-center">
                                        <h4 class="mr-1"><?php echo $value['currency_format'].round($energyCost['office']) ; ?></h4>
                                        <!--span>&nbsp;<?php echo $value['currency']; ?></span-->
                                    </div>
                                    <h6>(Office) <a data-bs-trigger="hover focus" data-bs-toggle="popover" title="Office use" data-bs-content="Assumes 8 hrs per day, 5 days per week, 52 weeks per year." data-bs-html="true"><?=$SVG_INFO;?>
                                        </a></h6>
                                    <small>Electricity cost based on your entered tariff of <?php echo $value['currency_format'].((($tariff))) ; ?> per kWh</small>
                                </div>
                            <?php } ?>
                            <div class="d-flex flex-column mt-4">
                                <?php echo (isset($value[$details])) ? '<a class="btn btn-outline-primary btn-sm" href="'.$value[$details].'" target="_blank">Details</a>' : ''; ?>
                                <?php echo (isset($value[$buy])) ? '<a data-country="'.$country.'" data-product="'.$value[$model].'" data-link="'.$value[$buy].'" class="btn btn-primary btn-sm mt-2" href="'.$value[$buy].'" target="_blank" onclick="sendClick(this);">Buy Now</a>' : ''; ?>
                            </div>
                        </div>
                    </div>

                    <?php } ?>
                <?php } else { ?>

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

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <!-- Bootstrap JS -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://getbootstrap.com/docs/5.1/examples/checkout/form-validation.js"></script>

    <!-- Fontawesome JS -->
    <script src="https://kit.fontawesome.com/53b8e4bb73.js" crossorigin="anonymous"></script>

    <script>
        const showHideRoomSizeOccupants = function(mode) {
            if (mode === 'ach') {
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
        }

        const ready = function (fn) {
            // wrapper that takes a function to execute once page loaded
            // replacement for $(document).ready() without needing jQuery
            if (typeof fn !== 'function') {
                throw new Error('Argument passed to ready should be a function');
            }
            if (document.readyState != 'loading') {
                fn();
            } else if (document.addEventListener) {
                document.addEventListener('DOMContentLoaded', fn, {
                once: true // A boolean value indicating that the listener should be invoked at most once after being added. If true, the listener would be automatically removed when invoked.
                });
            } else {
                document.attachEvent('onreadystatechange', function() {
                if (document.readyState != 'loading')
                    fn();
                });
            }
        }

        ready(function() {
            const achSelector = window.document.querySelector('#ach');
            achSelector.addEventListener('change', function(event) {
                showHideRoomSizeOccupants(event.target.options[event.target.selectedIndex].dataset.mode);
            });
            showHideRoomSizeOccupants(achSelector.options[achSelector.selectedIndex].dataset.mode);
        });
        
		// Popover
		const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
		const popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
		    return new bootstrap.Popover(popoverTriggerEl)
		});

        // Scroll to result page
        <?php echo (isset($scroll)) ? $scroll : null; ?>

        // Sort result by selected options
        // const sortResult = document.getElementById('sort-results');
        const product = document.querySelectorAll('.product-item');
        const sortOptions = document.getElementById('sort-options');
        let switching = true;
        // if(sortResult){
        //     sortResult.addEventListener('click', function() {
        //         if(switching){
        //             Array.from(product).sort(function(a, b){
        //                 return +b.dataset[sortOptions.value] - +a.dataset[sortOptions.value];
        //             })
        //             .forEach(el => el.parentNode.appendChild(el));
        //             console.log(`Sorted ascending by ${sortOptions.options[sortOptions.selectedIndex].text}`);
        //             switching = false;
        //             sortResult.innerHTML = "Sort Descending";
        //         } else {
        //             Array.from(product).sort(function(a, b){
        //                 return +a.dataset[sortOptions.value] - +b.dataset[sortOptions.value];
        //             })
        //             .forEach(el => el.parentNode.appendChild(el));
        //             console.log(`Sorted descendingby ${sortOptions.options[sortOptions.selectedIndex].text}`);
        //             switching = true;
        //             sortResult.innerHTML = "Sort &nbsp;Ascending";
        //         }
        //     });
        // }
        if(sortOptions){
            sortOptions.addEventListener('change', function() {
                if(switching){
                    Array.from(product).sort(function(a, b){
                        return +a.dataset[sortOptions.value] - +b.dataset[sortOptions.value];
                    })
                    .forEach(el => {
                        el.parentNode.appendChild(el);
                        if(el.dataset.tco == 0){
                            el.style.display = "none";
                        }
                    });
                    console.log(`Sorted ascending by ${sortOptions.options[sortOptions.selectedIndex].text}`);
                    switching = false;
                } else {
                    Array.from(product).sort(function(a, b){
                        return +a.dataset[sortOptions.value] - +b.dataset[sortOptions.value];
                    })
                    .forEach(el => {
                        el.parentNode.appendChild(el);
                        if(el.dataset.tco == 0){
                            el.removeAttribute('style');
                        }
                    });
                    console.log(`Sorted descendingby ${sortOptions.options[sortOptions.selectedIndex].text}`);
                    switching = true;
                }
            });
        }

        // Send click event to analytics json
        async function sendClick(d){
            const myHeaders = new Headers();
            myHeaders.append('Content-Type', 'application/x-www-form-urlencoded');

            const data = new URLSearchParams();
            data.append('click', 'true');
            data.append('country', d.dataset.country);
            data.append('product', d.dataset.product);
            data.append('link', d.dataset.link);

            var requestOptions = {
                method: 'POST',
                headers: myHeaders,
                body: data,
                redirect: 'follow'
            };

            fetch('admin/analytics.php', requestOptions)
            .then(response => response.json())
            .then(result => console.log(result))
            .catch(error => console.log('error', error));
        }        
    </script>

</body>
</html>
