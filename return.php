<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    session_start();
    include('includes/config.php');

    // if ($_SESSION['return_book'] != '') {
    //     $_SESSION['return_book'] = '';
    // }

    if(isset($_POST['login'])){
        $card = $_POST['card_id'];
        echo "Card id is ". $card;
        $sql = "SELECT * FROM tblbooks WHERE Book_CardID = '$card'";
        $query = $dbh->query($sql);
        $results = $query->fetchAll(PDO::FETCH_OBJ);
        $book_id = '';
        if ($query->rowCount() > 0) {
            foreach ($results as $result) {
                $book_id = $result->id ;
                echo $book_id;
                echo "<hr>"; // Add a horizontal line to separate each book

            }
        } else {
            echo "No books found for the provided card ID.";
        }


        $sid = $_SESSION['stdid'];
        echo $sid;

        // Fetching Students Details
        $sql0 = "SELECT * FROM tblstudents WHERE StudentID=:sid";
        $query0 = $dbh->prepare($sql0);
        $query0->bindParam(':sid', $sid, PDO::PARAM_STR);
        $query0->execute();
        $student_id = '';
        $student_card_id = '' ;

        if ($query0->rowCount() > 0) {
            while ($result0 = $query0->fetch(PDO::FETCH_ASSOC)) {
                $student_id = $result0['StudentId'] ;
                $student_card_id = $result0['Card_id'] ;
                echo "Student ID: " . $result0['StudentId'] . "<br>";
            }
        } else {
            echo "No student found for the provided card ID.";
        }
        echo "Student ID: " . $student_id. "<br>";
        //Inserting into issue DataBase

        // $sql = "UPDATE tblissuedbookdetails
        //     SET RetrunStatus = 1, ReturnDate = NOW()
        //     WHERE  Student_CardID = :student_card_id
        //     AND Book_CardID = :BookCard";

        // $query = $dbh->prepare($sql);
        // $query->bindParam(':student_card_id', $student_card_id, PDO::PARAM_STR);
        // $query->bindParam(':BookCard', $card, PDO::PARAM_STR);
        // $query->execute();

        
        $sql = "UPDATE tblissuedbookdetails
            SET RetrunStatus = 1,
                ReturnDate = NOW(),
                fine = CASE
                        WHEN DATEDIFF(NOW(), IssuesDate) > 30 THEN (DATEDIFF(NOW(), IssuesDate) - 30) * 5
                        ELSE 0
                    END
            WHERE  Student_CardID = :student_card_id
            AND Book_CardID = :BookCard";

        $query = $dbh->prepare($sql);
        $query->bindParam(':student_card_id', $student_card_id, PDO::PARAM_STR);
        $query->bindParam(':BookCard', $card, PDO::PARAM_STR);
        $query->execute();


        if ($query->rowCount() > 0) {
            echo "<script>alert('Book Returned successfully');</script>";
            echo "<script type='text/javascript'> document.location ='./dashboard.php'; </script>";
            
        } else {
            echo "<script>alert('Something went wrong. Please try again');</script>";
            echo "<script type='text/javascript'> document.location ='dashboard.php'; </script>";
        }

    }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f0f1f0]">
    <a href="return_confirm.php?rfid=123">Click here!</a>  
    <div>
        <form method="post">
            <h2>login</h2>
            <div>
                <input type="text" name="card_id" required/>
                <i class="fas fa-user"></i>
                <label for="">Book_id</label>
            </div>
            <button type="submit" name="login" class="btn">login</button>
        </form>
    </div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
        <dotlottie-player src="https://lottie.host/9187d494-342a-47ac-8a3a-07a16515a0fb/oIt9U4dNiT.json" background="transparent" speed="0.5" style="width: 300px; height: 300px;" loop autoplay></dotlottie-player>
        <div class="text-[2vw]">Please Tap Your Card!</div>
    </div>
    
    <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script> 
</body>
</html>