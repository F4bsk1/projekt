<?php
session_start();
// Kontrollera om användaren är inloggad
if (isset($_SESSION["logged_in_user"])) {
    // Användaren är inloggad, omdirigera till profilesidan
    header("Location: menu.php");
    exit();
}
?>


<!DOCTYPE html> 
<html lang="en">
  <head>
    <title>Please sign up</title>
    <link
      rel="stylesheet"
      href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
      integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
      crossorigin="anonymous"
    />
    <link
      href="https://getbootstrap.com/docs/4.0/examples/signin/signin.css"
      rel="stylesheet"
      crossorigin="anonymous"
      />
    <!--script>
      function checkConfirm(form){
	  const pwd=document.getElementById('password').value;
	  const cnfrm=document.getElementById('confirm').value;
	  if (pwd === cnfrm){
	     alert(pwd);
	     alert(cnfrm);
	     return true;
	  }
	  //	 	    alert("no cnfrm");
	     return false;   
      }
      </script-->
  </head>
  <body>
    <div class="container">
      <div class="row">
        <div class="col-12 text-center">
          <a
            href="index.php"
            class="btn btn-secondary btn-sm active"
            role="button"
            aria-pressed="true"
          >
            Login
          </a>
        </div>
      </div>
      <div class="row">
        <div class="col-12 text-center mt-4">
          <form class="form-signin" method="post" action="register.php" onsubmit="return checkConfirm()" >

	    <?php
	    if (isset($_SESSION['message'])) {
	        echo $_SESSION['message'];
		
    		unset($_SESSION['message']); // Ta bort meddelandet från sessionen så det inte visas igen
}
	    ?>
	                <h2 class="form-signin-heading">Please sign up</h2>
            <div id="error" class="alert alert-danger" role="alert"></div>
            <div id="success" class="alert alert-success" role="alert"></div>
            <p>
              <label for="username" class="sr-only">Username</label>
              <input
                type="text"
                id="username"
                name="username"
                class="form-control"
                placeholder="Username"
                required
                autofocus
              />
            </p>
            <p>
              <label for="password" class="sr-only">Password</label>
              <input
                type="password"
                id="password"
                name="password"
                class="form-control"
                placeholder="Password"
                required
              />
            </p>
            <p>
              <label for="confirm" class="sr-only">Confirm</label>
              <input
                type="password"
                id="confirm"
                name="confirm"
                class="form-control"
                placeholder="Confirm"
                required
              />
            </p>
            <button
              class="btn btn-lg btn-outline-primary btn-block"
              type="submit"
            >
              Sign up
            </button>
          </form>
        </div>
      </div>
    </div>
    <script type="module" src="../public/js/index.js"></script>
  </body>
</html>
