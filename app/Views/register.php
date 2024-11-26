

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="<?=base_url()?>assets/css/style.css">
</head>
<body>
    <div style="text-align:center;color:red">
    <h2><?php echo session()->getFlashData("error")?></h2>
    </div>
    
<div class="register">
    <form action="/registerUser" method="post">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required> 
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required> 
        <label for="confirmpassword">Confirm Password:</label>
        <input type="password" id="confirmpassword" name="confirmpassword" required> 
        <button type="submit">Register</button>
        <p>Already have a account ,<a href="/">Login
            
        </a></p>
    </form>
</div>

</body>
</html>