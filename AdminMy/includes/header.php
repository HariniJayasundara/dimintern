<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <link rel="stylesheet" href="styles.css">
    <title>Admin panel</title>
</head>

<body>
    <div class="container">
        <div class="topbar">
            <div class="logo">
                <h2>DIM Admin</h2>
            </div>
            <div class="search">
                <input type="text" name="search" placeholder="search here">
                <label for="search"><i class="fas fa-search"></i></label>
            </div>
            <i class="fas fa-bell"></i>
            <div class="user">
                <img src="img/user.png" alt="">
            </div>
        </div>
        <div class="sidebar">
            <ul>
                <li>
                    <a href="#">
                        <i class="fas fa-th-large"></i>
                        <div>Dashboard</div>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-user-graduate"></i>
                        <div>Students</div>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-user-tie" ></i>
                        <div>Companies</div>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-users"></i>
                        <div>DIM Staff</div>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-hand-sparkles"></i>
                        <div>Preferences</div>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-clipboard-check"></i>
                        <div>Allocations</div>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-file-signature"></i>
                        <div>Documentation</div>
                    </a>
                    <ul>
                    <li>
                        <a href="#">
                            <i class="far fa-file-alt"></i>
                            <div>CV</div>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="far fa-file-alt"></i>
                            <div>Student Report</div>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="far fa-file-alt"></i>
                            <div>Company Report</div>
                    </a>
                    </li>
                    </ul>
                    </li>

            </ul>
        </div>