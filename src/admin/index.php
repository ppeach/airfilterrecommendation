<?php
require_once(__DIR__.'/../includes/init.php');
// Start sessions
session_start();

// Redirect to login page if there's no sessions
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
}

// Countries table
$countries = __DIR__."/../data/config/countries.json";
$hide = true;
$btn_data = "Generate Database";
if(file_exists($countries)){
    $tabel_data = json_decode(file_get_contents($countries), true);
    $hide = false;
    $btn_data = "Re-generate Database";
}
?>
<!doctype html>
<html lang="en">
<?php include_once(__DIR__.'/views/header.php'); ?>
<body class="bg-light">
    <div class="container">
        <?php include_once(__DIR__.'/views/navbar.php'); ?>
        <main>

            <div class="row g-3">
                <div class="col-md-3">
                    <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                        <img class="rounded-circle" width="150px" src="<?=$_SESSION['user_picture'];?>" alt="<?=$_SESSION['user_name'];?>">
                        <span class="fw-bold mt-2"><?=$_SESSION['user_name'];?></span>
                        <span class="fw-light mb-2"><?=$_SESSION['user_email'];?></span>
                        <!-- <button class="btn btn-danger btn-sm" name="signout" id="signOut">Sign Out</button> -->
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="row">
                        <h4 class="mb-3">Google Sheets config</h4>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="sheetID" class="form-label">Spreadsheet ID / URL</label>
                                <input type="text" class="form-control" id="sheetID" name="sheetID" placeholder="17j6FZwvqHRFkGoH5996u5JdR7tk4_7fNuTxAK7kc4Fk" value="<?=config('sheet_id');?>" required>
                            </div>

                            <div class="col-md-4 d-flex align-items-end">
                                <button class="w-100 btn btn-primary" name="saveSheetID" id="saveSheetID">Save Sheets ID</button>
                            </div>
                        </div>

                        <hr class="my-4">

                        <button class="w-100 btn btn-success" name="generateDB" id="generateDB"><?=$btn_data;?></button>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <?php if($hide == false){?>
            <h4 class="mb-3">Google Sheets data</h4>
            <table class="table table-striped" id="gsheetdata">
                <thead>
                    <tr>
                    <th scope="col">#</th>
                    <th scope="col">Contries</th>
                    <th scope="col">Total rows</th>
                    <th scope="col">Total columns</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($tabel_data as $key => $country){?>
                    <tr>
                    <th scope="row"><?=$key;?></th>
                    <td><?=$country['properties']['title'];?></td>
                    <td><?=$country['properties']['gridProperties']['rowCount'];?></td>
                    <td><?=$country['properties']['gridProperties']['columnCount'];?></td>
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

    <?php include_once(__DIR__.'/views/footer_js.php'); ?>

    <script>
        // Spinner
        const spinner = "<div class='spinner-border' role='status'><span class='visually-hidden'>Loading...</span></div>";

        // Save Sheets ID
        const saveSheetID = document.querySelector('#saveSheetID');
        saveSheetID.addEventListener("click", function() {
            saveSheetID.innerHTML = spinner;
            saveSheetID.disabled = true;
            const sheetID = document.querySelector('#sheetID').value;
            const data = "";
            const xhr = new XMLHttpRequest();
            xhr.withCredentials = true;
            xhr.addEventListener("readystatechange", function() {
            if(this.readyState === 4) {
                saveSheetID.innerHTML = "Save Sheets ID";
                saveSheetID.disabled = false;
                const results = JSON.parse(this.responseText);
                Swal.fire({
                    icon: results.status,
                    title: results.message,
                    text: `New Sheet ID: ${results.data}`,
                    footer: 'Please re-generate the database after changing sheet id'
                })
                console.log(this.responseText);
            }
            });
            xhr.open("GET", `update.php?id=sheet_id&sheetsid=${sheetID}`);
            xhr.send(data);
        });
        // Generate Database
        const gsheetData = document.querySelector('#gsheetdata');
        const generateDB = document.querySelector('#generateDB');
        generateDB.addEventListener("click", function() {
            generateDB.innerHTML = spinner;
            generateDB.disabled = true;
            gsheetData.remove();
            const data = "";
            const xhr = new XMLHttpRequest();
            xhr.addEventListener("readystatechange", function() {
                if(this.readyState === 4 && this.status === 200) {
                    const results = JSON.parse(this.responseText);
                    const countries = results.data.map(country => {
                        return `<span class="badge bg-primary mx-1">${country.country}</span>`;
                    });
                    generateDB.innerHTML = "<?=$btn_data;?>";
                    generateDB.disabled = false;
                    console.log(this.responseText);
                    Swal.fire({
                        icon: results.status,
                        title: results.message,
                        html: countries,
                        // showConfirmButton: false,
                        // timer: 2000
                    }).then((result) => {
                        if(result.isConfirmed){
                            location.reload();
                        }
                    });
                }
            });
            xhr.open("POST", "update.php");
            xhr.send(data);
        });

        // Sign out
        const signOut = document.querySelector('#signOut');
        signOut.addEventListener("click", function() {
            fetch("auth.php?signout=true")
            .then(response => response.json())
            .then(result => console.log(result))
            .then(window.location.href = "login.php")
            .catch(error => console.log('error', error));
        });
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
    <?php if(isset($_SESSION['signin_message'])){ ?>
        // Toast sign in
        Toast.fire({
            icon: 'success',
            title: '<?=$_SESSION['signin_message']?>'
        })
    <?php unset($_SESSION['signin_message']);} ?>
    <?php } ?>
    </script>
</body>
</html>