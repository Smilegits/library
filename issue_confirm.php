<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    session_start();
    include('includes/config.php');

    

    if(isset($_GET['rfid'])){
        $card = $_GET['rfid'];
        $sql = "SELECT * FROM tblbooks WHERE Book_CardID = $card";
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

        //Find if student had already issued 2 books
        $sql3 = "SELECT id FROM tblissuedbookdetails WHERE StudentID = :student_id and RetrunStatus = 0";
        $query3 = $dbh->prepare($sql3);
        $query3->bindParam(':student_id', $student_id, PDO::PARAM_STR);
        $query3->execute();

        if($query3->rowCount() >= 2){
            $_SESSION['msg'] = "Full";
        }
        else{
            //Find if student already issued the same book
            $sql2 = "SELECT id FROM tblissuedbookdetails WHERE StudentID = :student_id and BookId = :book_id and RetrunStatus = 0";
            $query2 = $dbh->prepare($sql2);
            $query2->bindParam(':book_id', $book_id, PDO::PARAM_INT);
            $query2->bindParam(':student_id', $student_id, PDO::PARAM_STR);
            $query2->execute();


            if($query2->rowCount() > 0){
                $_SESSION['msg'] = "Already";
            }
            else{
                //Inserting into issue DataBase
                
                $sql1 = "INSERT INTO tblissuedbookdetails (BookId, StudentID, Student_CardID, Book_CardID) VALUES (:book_id, :student_id, :StudentCard, :BookCard)";
                $query1 = $dbh->prepare($sql1);
                $query1->bindParam(':book_id', $book_id, PDO::PARAM_INT);
                $query1->bindParam(':student_id', $student_id, PDO::PARAM_STR);
                $query1->bindParam(':StudentCard', $student_card_id, PDO::PARAM_STR);        
                $query1->bindParam(':BookCard', $card, PDO::PARAM_STR);
                $query1->execute();
                $lastInsertId = $dbh->lastInsertId();

                if ($lastInsertId) {
                    $_SESSION['msg'] = "Success";
                }
            }
        }

        
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col gap-10 justify-center items-center">
        <div class="text-center">
            <?php
                if($_SESSION['msg'] == "Success") {
            ?>
                    <h1 class="text-4xl font-bold">Congratulations, Book has been issued!</h1>
            <?php
                } else if($_SESSION['msg'] == "Already") {
            ?>
                    <h1 class="text-4xl font-bold">Sorry, You have already issued the same book!</h1>
            <?php
                } else if($_SESSION['msg'] == "Full") {
            ?>
                    <h1 class="text-4xl font-bold">Sorry, You have already issued 2 books!</h1>
            <?php
                } else {
            ?>
                    <h1 class="text-4xl font-bold">Sorry, Try Again!</h1>
            <?php
                }
            ?>
        </div>
        <img src="home-morning.svg" alt="Photo">
        <a href="dashboard.php" class="text-[#6366F1] underline font-medium">Go Back to Dashboard</a>
        
    </div>
</body>

</html>