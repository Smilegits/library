<?php
    session_start();
    error_reporting(0);
    include('includes/config.php');
    if($_SESSION['login']!=''){
        $_SESSION['login']='';
    }
    if(isset($_POST['login'])) //card tap pae kya chij trigger hogi jiss mae user ko identify krr nae kae liye kuch hoga
    {
        // let it be emailid of student 
        $cardId=$_POST['card_id'];
        $sql ="SELECT EmailId,Card_id,Password,StudentId,Status FROM tblstudents WHERE Card_id=:card";
        $query= $dbh -> prepare($sql);
        $query-> bindParam(':card', $cardId, PDO::PARAM_STR);
        $query-> execute();
        $results=$query->fetchAll(PDO::FETCH_OBJ);

        if($query->rowCount() > 0)
        {
            foreach ($results as $result) {
                $_SESSION['stdid']=$result->StudentId;
                if($result->Status==1)
                {
                    $_SESSION['login']=$_POST['card_id'];
                    echo "<script type='text/javascript'> document.location ='dashboard.php'; </script>";
                } else {
                    echo "<script>alert('Your Account Has been blocked .Please contact admin');</script>";
                }
            }
        } 

        else{
        echo "<script>alert('Invalid Details');</script>";
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Your Card!</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <h1>Scan Your Card First!</h1>

    <div class="wrapper">
             
<!--LOGIN PANEL START-->    
<a href="dashboard.php?rfid=123">Click here!</a>       
<div>
            <form method="post">
                <h2>login</h2>
                <div>
                    <input type="text" name="card_id" required/>
                    <i class="fas fa-user"></i>
                    <label for="">Card_id</label>
                </div>
                <button type="submit" name="login" class="btn">login</button>
            </form>
        </div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
        <dotlottie-player src="https://lottie.host/9187d494-342a-47ac-8a3a-07a16515a0fb/oIt9U4dNiT.json" background="transparent" speed="0.5" style="width: 300px; height: 300px;" loop autoplay></dotlottie-player>
        <div class="text-[2vw]">Please Tap Your Card!</div>
    </div>
    
    <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script> 

        
<!---LOGIN PANEL END-->            
             
 
    </div>
</body>
</html>