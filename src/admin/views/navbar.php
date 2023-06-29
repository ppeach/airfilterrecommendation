    <header class="d-flex flex-wrap align-items-center justify-content-center py-3 mb-4 border-bottom">
        <a href="<?=$ADMIN_MENU['dashboard'];?>" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <span class="fs-4">Admin Dashboard</span>
        </a>

        <ul class="nav nav-pills">
            <?php foreach($ADMIN_MENU as $menu => $link){ 
                $active = $curr = '';
                if($_SERVER['PHP_SELF'] == $link){
                    $active = 'active'; $curr = 'aria-current="page"';}?>
            <li class="nav-item"><a href="<?=$link;?>" class="nav-link <?=$active;?>" <?=$curr;?>><?=ucwords($menu);?></a></li>
            <?php } ?>
        </ul>
        <div class="flex-shrink-0 dropdown ms-2">
            <a href="#" class="d-block link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="<?=$_SESSION['user_picture'];?>" alt="<?=$_SESSION['user_name'];?>" width="32" height="32" class="rounded-circle">
            </a>
            <ul class="dropdown-menu text-small shadow" style="">
                <li><a class="dropdown-item" href="#">Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><button class="dropdown-item btn btn-danger btn-sm" name="signout" id="signOut">Sign Out</button></li>
            </ul>
        </div>
    </header>