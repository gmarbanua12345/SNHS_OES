<?php
    
    session_start();

		if (isset($_SESSION['accessed'])) {
			dispatchUserToRespectivePages(new user);
		}
		
		function dispatchUserToRespectivePages($user)
		{
			if ($user->getutype() == 0) {
        header("location: enrollee");
    	} else if ($user->getutype() == 1) {
        header("location: faculty");
    	} else if($user->getutype() == 2) {
        header("location: admin"); 
    	}
		}

    if (isset($_POST['login'])) {
        verify();
    }

    function verify() {
        $user = new user;

        $con = connect();

        $username = $con->real_escape_string($_POST['username']);
        $password = $con->real_escape_string($_POST['password']);

        if (usernameExists($username) && passwordExists($username, $password)) {
            loginUser($username);
        } else {
            header("location: ?login&fail");
        }
    }

    function usernameExists($username) {
        $con = connect();
        $sql = "SELECT username FROM user_account WHERE username = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();
        $res->fetch_assoc();
        return $res->num_rows;
    }

    function passwordExists($username, $password) {
        $con = connect();
        $sql = "SELECT password FROM user_account WHERE username = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($passwordDB);
        $stmt->fetch();

        if ($passwordDB == $password) {
            return 1;
        } else {
            return 0;
        }

    }

    function loginUser($username) {
        
        $_SESSION["accessed"] = 0;
        
        $con = connect();

        $sql = "SELECT id FROM user_account WHERE username = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();

        $_SESSION['id'] = $row['id'];
        checkIfBlocked();
    }

    function checkIfBlocked() {
        $user = new user;
        echo $user->gstatus();

        if ($user->gstatus() == 0) {
            session_destroy();
            header("location: ?login&blocked");
        } else {
            $log = new activitylog;
            $user = new user;
            $fullName = $user->getfname();
            $log->log(' has logged in');
            header('location: '.$_SERVER['PHP_SELF']);
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="css/all.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/query.css">
    <link rel="stylesheet" href="css/fontawesome/css/all.min.css">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to BHSES</title>
</head>
<body>
    
    <nav>
        <div class="container">
            <div class="nav-wrapper">
                <div class="aues-title">
                    <div class="au-logo">
                        <img src="img/logo1.png">
                    </div>
                    <h1>BHSES</h1>
                </div>
                <div class="aues-select">
                    <a href="enroll/">Contact Us</a>
                    <a href="#about">About Us</a>
                    <a href="index2.php">Admin Login</a>
                </div>
            </div>
        </div>
    </nav>

    <?php 
        if (isset($_GET['fail'])) {
            ?>
            <h3 style="color:red; text-align: center; margin-top: 40px">Failed Login. Try again.</h3>
            <?php
        }

        if (isset($_GET['blocked'])) {
            ?>
            <h3 style="color:red; text-align: center; margin-top: 40px">Failed Login. Your account is deactivated.</h3>
            <?php
        }
    ?>

    <?php 
        if (isset($_GET['login'])) {
            ?>
                <section id="login">
                    <div class="container">
                        <div class="login-wrapper">
                            <form action="" method="post">
                                <input type="text" name="username" id="username" placeholder="Username">
                                <input type="password" name="password" id="password" placeholder="Password">
                                <input type="submit" name="login" value="Log In">
                            </form>
                        </div>
                    </div>
                </section>
            <?php
        }
    ?>

    <?php 
        if (isset($_GET['new'])) {
            ?>
                <section id="login">
                    <div class="container">
                        <div class="login-wrapper">
                            <h1>You have successfully registered.</h1>
                            <p>Please proceed to the Arellano University and bring the requirements to confirm your application. See you there!</p>
                        </div>
                    </div>
                </section>
            <?php
        }
    ?>
    
    <section id="front-ui">
        <div class="container">
            <div class="front-ui-wrapper">
                <div class="front-ui-content">
                    <div class="front-ui-image">
                        <img src="img/logo1.png">
                    </div>
                    <div class="front-ui-texters">
                        <p>SIBULAN NHS - BALUGO EXTENSION</p>
                        <h1>ENROLLMENT SYSTEM</h1>
                    </div>
                    <div class="front-ui-enrollnow">
                        <a href="enroll"> <i class="fad fa-user-plus"></i> <p>ENROLL NOW</p> </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="about">
        <div class="container">
            <div class="about-wrapper">
                <div class="about-texter">
                    <h1>ABOUT US</h1>
                </div>

                <div class="about-card-all">
                    <div class="card">
                        <div class="card-title">
                            <h1>MISSION</h1>
                        </div>
                        <div class="card-content">
                            <p>To protect and promote the right of every Filipino to quality, equitable, culture-based, and complete basic education where:</p>
							<p>Students learn in a child-friendly, gender-sensitive, safe, and motivating environment.</p>
<p>Teachers facilitate learning and constantly nurture every learner.</p>
<p>Administrators and staff, as stewards of the institution, ensure an enabling and supportive environment for effective learning to happen.</p>
<p>Family, community, and other stakeholders are actively engaged and share responsibility for developing life-long learners.</p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-title">
                            <h1>VISION</h1>
                        </div>
                        <div class="card-content">
                            <p>We dream of Filipinos
who passionately love their country
and whose values and competencies
enable them to realize their full potential
and contribute meaningfully to building the nation.

As a learner-centered public institution,
the Department of Education
continuously improves itself
to better serve its stakeholders.</p>

                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-title">
                            <h1>CORE VALUES</h1>
                        </div>
                        <div class="card-content">
                            <p>Maka-Diyos</p>
							<p>Maka-tao</p>
							<p>Makakalikasan</p>
							<p>Makabansa</p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-title">
                            <h1>JUNIOR HIGH SCHOOL</h1>
                        </div>
                        <div class="card-content">
                            <p></p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-title">
                            <h1>STEM STRAND</h1>
                        </div>
                        <div class="card-content">
                            <p></p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-title">
                            <h1>HUMSS STRAND</h1>
                        </div>
                        <div class="card-content">
                            <p></p>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>

    <section id="waywf">
        <div class="container">
            <div class="waywf-wrapper">
                <div class="waywf-texter">
                    <h1>
					Ensures Quality Education
to the least, the last and the lost
while nurturing lifelong learners.
                    </h1>
                </div>
            </div>
        </div>
    </section>

   
</body>
</html>