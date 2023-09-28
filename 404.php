<?php require_once('admin/functions.php');?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <style>
    .contcontainer {
      display: flex;
      justify-content: center;
      align-items: center;
      height: calc(100vh - 50px);
    }
    .container {text-align: center;}
    .error-code {
      font-size: 4rem;
      color: #ff6347;
    }

    .error-message {
      font-size: 1.5rem;
      color: #333;
      margin-bottom:40px;
    }

    .home-link {
      margin-top:20px;
      color: #007bff;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <div class="contcontainer">
      <div class="container">
          <div class="error-code">404</div>
          <div class="error-message">Page Not Found</div>
          <p>Sorry, url not available!</p>
          <p><a class="home-link" href="<?php echo home_url() ?>">Go to Home Page</a></p>
      </div>
  </div>
<?php 
require_once("admin/footer.php");
