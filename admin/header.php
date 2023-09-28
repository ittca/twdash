<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="img/ittca_logo.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/<?php echo $_SESSION['css'];?>.css">
    <title><?php echo $_SESSION['page_name']; ?></title> <?php 
    unset($_SESSION['page_name']);unset($_SESSION['css']);?> 
</head>
<body>
    <header class="flex">
        <p id="logoname" class="menutext" style="font-size: 24px;">tw Dash</p>
        <div class="flex">
            <p class="menutext">Dashboard</p>
            <p class="menutext">Sites</p>
        </div>
        <div id="menuright" class="flex">
            <p class="menutext">dark</p>
            <p class="menutext">Admin Area</p>
            <p class="menutext">user</p>
        </div>
    </header>
    
