<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    session_start();
    include('includes/config.php');


    if(isset($_GET['rfid'])){
        $card = $_GET['rfid'];
        // echo "Card id is ". $card;
        $sql = "SELECT * FROM tblbooks WHERE Book_CardID = '$card'";
        $query = $dbh->query($sql);
        $results = $query->fetchAll(PDO::FETCH_OBJ);
        $book_id = '';
        if ($query->rowCount() > 0) {
            foreach ($results as $result) {
                $book_id = $result->id ;

            }
        } else {
            echo "No books found for the provided card ID.";
        }


        $sid = $_SESSION['stdid'];
        // echo $sid;

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
                // echo "Student ID: " . $result0['StudentId'] . "<br>";
            }
        } else {
            echo "No student found for the provided card ID.";
        }
        // echo "Student ID: " . $student_id. "<br>";
        

        
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
            $_SESSION['msg'] = "Book Returned successfully";
        } 

    }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Confirmation</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col gap-10 justify-center items-center">
        <div class="text-center">
            <?php
                if($_SESSION['msg'] != '') {
            ?>
                    <h1 class="text-4xl font-bold">Congratulations, Book has been Returned!</h1>
            <?php
                } else {
            ?>
                    <h1 class="text-4xl font-bold">Sorry, Try Again!</h1>
            <?php
                }
            ?>
        </div>
        <img src="home-morning.svg" alt="photu">
        <a href="dashboard.php" class="text-[#6366F1] underline font-medium">Go Back to Dashboard</a>
        
    </div>
</body>
</html>