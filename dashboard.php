<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    session_start();
    include('includes/config.php');

    if(isset($_GET['rfid'])){
        $rfid = htmlspecialchars($_GET['rfid']);

        $sql = "SELECT * FROM tblstudents WHERE Card_id = :card_id";
        $query= $dbh -> prepare($sql);
        $query-> bindParam(':card_id', $rfid, PDO::PARAM_STR);
        $query-> execute();
        $results=$query->fetchAll(PDO::FETCH_OBJ);

        if($query->rowCount() > 0){
            foreach ($results as $result) {
                $_SESSION['stdid']=$result->StudentId;
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central Lib | DashBoard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

    <style>
    .app-form-group {
        margin-bottom: 20px;
    }

    .app-form-group.message {
        margin-top: 20px;
    }

    .app-form-group.buttons {
        margin-bottom: 0;
        text-align: right;
    }

    .app-form-control {
        width: 100%;
        padding: 10px 0;
        background: none;
        border: none;
        border-bottom: 1px solid #ddd;
        color: black;
        font-size: 14px;
        outline: none;
        transition: border-color .2s;
    }

    .app-form-control::placeholder {
        color: #666;
    }

    .app-form-control:focus {
        border-bottom-color: #666;
        outline: none;
    }
    </style>
    <script>
    function redirectToIssuePage() {
        window.location.href = "issue.php";
    }

    function redirectToReturnPage() {
        window.location.href = "return.php";
    }
    </script>
</head>

<body class="bg-[#f0f1f0]">
    <div class=" w-full h-[100%] flex">
        <!-- Menu Bar  -->
        <div
            class="w-[15%] bg-gradient-to-b from-indigo-500 via-purple-800 to-purple-900 text-white py-5 rounded-tr-[1rem]">
            <div class="flex justify-center gap-2 " style="align-items: center;">
                <div><i class="fa-solid fa-book fa-lg" style="color: #ffffff;"></i></div>
                <div class="font-medium text-[1.25rem]"> Central Library</div>
            </div>
            <div class="pt-[4rem]">
                <a href="#" class="flex pl-10 gap-5 pb-[2rem]" style="align-items: center;">
                    <div><i class="fa-solid fa-house"></i></div>
                    <div>Home</div>
                </a>
                <a href="issue.php" class="flex pl-10 gap-5 pb-[2rem]" style="align-items: center;">
                    <div><i class="fa-solid fa-book-open-reader"></i></div>
                    <div>Issue</div>
                </a>
                <a href="return.php" class="flex pl-10 gap-5 pb-[2rem]" style="align-items: center;">
                    <div><i class="fa-solid fa-receipt"></i></i></div>
                    <div>Return</div>
                </a>
            </div>
            <div class="border-t-[1px] border-[#b3b2b3] mt-[14vw] py-5 flex flex-col" style="align-items: center;">
                <div class="w-14 h-14 border-2 rounded-full overflow-hidden">
                    <img class="w-full h-full object-cover inset-0"
                        src="https://www.shutterstock.com/shutterstock/photos/2071252046/display_1500/stock-photo-portrait-of-cheerful-male-international-indian-student-with-backpack-learning-accessories-standing-2071252046.jpg"
                        alt="">
                </div>

                <?php
                        $sid = $_SESSION['stdid'];
                        $sql0 ="SELECT id , FullName from tblstudents where StudentID=:sid";
                        $query0 = $dbh -> prepare($sql0);
                        $query0->bindParam(':sid',$sid,PDO::PARAM_STR);
                        $query0->execute();
                        $result0 = $query0->fetch(PDO::FETCH_ASSOC);
                        $full_name = $result0['FullName'];
                    ?>

                <div class="p-2"><?php echo $full_name ?></div>
                <div class="flex justify-center gap-5" style="align-items: center;">
                    <div><i class="fa-solid fa-pen"></i></div>
                    <div><i class="fa-solid fa-gear"></i></div>
                </div>
            </div>
        </div>
        <div class="w-[85%] px-5 py-5">
            <div>
                <div class="text-[1.5rem] mb-10">Welcome <?php echo $full_name ?></div>

                <!-- 4 cards  -->
                <div class="flex w-full h-[10vw] justify-center gap-10" style="align-items: center;">
                    <div
                        class="w-1/4 h-full bg-white border rounded-[1rem] p-5 hover:drop-shadow-xl transition-all duration-300">
                        <div class="flex item-center justify-between">
                            <?php 
                    $sid=$_SESSION['stdid'];
                    $sql1 ="SELECT id from tblissuedbookdetails where StudentID=:sid";
                    $query1 = $dbh -> prepare($sql1);
                    $query1->bindParam(':sid',$sid,PDO::PARAM_STR);
                    $query1->execute();
                    $results1=$query1->fetchAll(PDO::FETCH_OBJ);
                    $issuedbooks=$query1->rowCount();
                    $jsonIssue = json_encode($issuedbooks);
                ?>
                            <div class="text-[1.25rem]">Issued Books</div>
                            <div><i class="fa-solid fa-book-open-reader fa-2xl" style="color:rgb(99 102 241);"></i>
                            </div>
                        </div>
                        <div class="mt-10 text-zinc-500 text-sm">
                            <span class="text-[#6366F1]" id="issue">
                                <?php echo htmlentities($issuedbooks);?>
                            </span>
                            <?php 
                    if($issuedbooks <= 1) echo htmlentities(" book is ");
                    else echo htmlentities(" books are ");
                ?>issued to you!
                        </div>

                    </div>

                    <div
                        class="w-1/4 h-full bg-white border rounded-[1rem] p-5 hover:drop-shadow-xl transition-all duration-300">
                        <div class="flex item-center justify-between">
                            <?php 
                    $rsts=0;
                    $sql2 ="SELECT id from tblissuedbookdetails where StudentID=:sid and RetrunStatus=:rsts";
                    $query2 = $dbh -> prepare($sql2);
                    $query2->bindParam(':sid',$sid,PDO::PARAM_STR);
                    $query2->bindParam(':rsts',$rsts,PDO::PARAM_STR);
                    $query2->execute();
                    $results2=$query2->fetchAll(PDO::FETCH_OBJ);
                    $pendingBooks=$query2->rowCount();
                    $jsonPending  = json_encode($pendingBooks);
                    $jsonReturned = json_encode($issuedbooks - $pendingBooks);
                ?>
                            <div class="text-[1.25rem]">Pending Books</div>
                            <div><i class="fa-solid fa-hourglass-half fa-2xl" style="color:rgb(99 102 241);"></i></div>
                        </div>
                        <div class="mt-10 text-zinc-500 text-sm">
                            <span class="text-[#6366F1]">
                                <?php echo htmlentities($pendingBooks);?>
                            </span>
                            <?php 
                    if($pendingBooks <= 1) echo htmlentities(" book is ");
                    else echo htmlentities(" books are ");
                ?>still pending!
                        </div>
                    </div>
                    <div
                        class="w-1/4 h-full bg-white border rounded-[1rem] p-5 hover:drop-shadow-xl transition-all duration-300">
                        <div class="flex item-center justify-between">
                            <div class="text-[1.25rem]">Returned Books</div>
                            <div><i class="fa-solid fa-receipt fa-2xl" style="color:rgb(99 102 241);"></i></div>
                        </div>
                        <div class="mt-10 text-zinc-500 text-sm">
                            <span class="text-[#6366F1]">
                                <?php echo htmlentities($issuedbooks - $pendingBooks);?>
                            </span>
                            <?php 
                    if($issuedbooks <= 1) echo htmlentities(" book is ");
                    else echo htmlentities(" books are ");
                ?>returned by you!
                        </div>
                    </div>
                    <div
                        class="w-1/4 h-full bg-white border rounded-[1rem] p-5 hover:drop-shadow-xl transition-all duration-300">
                        <div class="flex item-center justify-between">
                            <div class="text-[1.25rem]">Lost Books</div>
                            <div><i class="fa-solid fa-book-open-reader fa-2xl" style="color:rgb(99 102 241);"></i>
                            </div>
                        </div>
                        <div class="mt-10 text-zinc-500 text-sm">
                            <span class="text-[#6366F1]">
                                0
                            </span> books are lost by you!
                        </div>
                    </div>

                </div>



                <!-- feedback & chart -->
                <div class="w-full h-[50vw] mt-10 flex justify-center gap-10">
                    <div class="w-1/2">
                        <!-- Notifications  -->
                        <div class="w-full h-full border rounded-[1rem] bg-white overflow-hidden">
                            <div class="pt-5 px-5">
                                <div class="text-[1.25rem] font-medium">Notifications</div>
                            </div>
                            <div class="flex justify-around mt-7 text-[#6366F1] font-medium">
                                <button onclick="filterPeriod(this , 'today')" class="btn btn-today">Today</button>
                                <button onclick="filterPeriod(this , 'week')" class="btn btn-week">One Week</button>
                                <button onclick="filterPeriod(this , 'month')" class="btn btn-month">One Month</button>
                            </div>
                            <div class="flex mt-3 items-center justify-center line-container">
                                <div class="w-1/3 h-[1.5px] bg-[#ddd] line line-today"></div>
                                <div class="w-1/3 h-[1.5px] bg-[#ddd] line line-week"></div>
                                <div class="w-1/3 h-[1.5px] bg-[#ddd] line line-month"></div>
                            </div>
                            <div class="data overflow-auto w-full h-[36vw] mt-2">
                                <div id="notifications" class="px-10">
                                    <!-- Notifications will be inserted here dynamically -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="w-1/2 flex flex-col gap-5">
                        <!-- Chart  -->
                        <div class="w-full h-[60%] bg-white border rounded-[1rem] overflow-hidden">
                            <div class="pt-5 px-5 text-[1.25rem] font-medium">Total Books Reports</div>
                            <div class="flex justify-around items-center p-5 pt-9">
                                <div class="flex flex-col justify-center items-center">
                                    <div class="" id="issuePercentage">44.4%</div>
                                    <div class="text-[14px] text-zinc-500">Issued</div>
                                </div>
                                <div class="flex flex-col justify-center items-center">
                                    <div class="" id="pendingPercentage">44.4%</div>
                                    <div class="text-[14px] text-zinc-500">Pending</div>
                                </div>
                                <div class="flex flex-col justify-center items-center">
                                    <div class="" id="returnedPercentage">44.4%</div>
                                    <div class="text-[14px] text-zinc-500">Returned</div>
                                </div>
                                <div class="flex flex-col justify-center items-center">
                                    <div class="" id="lostPercentage">44.4%</div>
                                    <div class="text-[14px] text-zinc-500">Lost</div>
                                </div>
                            </div>
                            <div class="pb-10 h-[1px]">
                                <canvas id="myChart_book" style="padding-bottom: 10px;"></canvas>
                            </div>
                        </div>
                        <!-- feedback  -->
                        <div class="h-[40%] border rounded-[1rem] bg-white overflow-hidden">
                            <div class="flex justify-between bar px-4 py-1 bg-slate-300 w-full h-[2vw] ">
                                <div class="flex gap-1" style="align-items: center;">
                                    <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                    <div class="w-2 h-2 rounded-full bg-yellow-500"></div>
                                    <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                </div>
                                <div class="flex gap-[2px]" style="align-items: center;">
                                    <div class="w-1 h-1 rounded-full bg-black"></div>
                                    <div class="w-1 h-1 rounded-full bg-black"></div>
                                    <div class="w-1 h-1 rounded-full bg-black"></div>
                                </div>
                            </div>
                            <div class="text h-[18vw] flex ">
                                <div class="w-1/2 h-full flex flex-col py-10 px-10">
                                    <div class="text-[1.5rem] font-medium">Say Something</div>
                                    <div class="text-zinc-500 text-sm">we are hearing you!</div>
                                    <div class="w-16 h-1 mt-2 bg-[#6366F1]"></div>
                                </div>
                                <div class="w-1/2 h-full pr-5 pt-5">
                                    <div class="app-form">
                                        <div class="app-form-group">
                                            <input class="app-form-control" placeholder="your mail id">
                                        </div>
                                        <div class="app-form-group message">
                                            <input class="app-form-control" placeholder="your message for us">
                                        </div>
                                        <div class="app-form-group buttons">
                                            <button
                                                class="app-form-button rounded-full px-3 py-2 text-sm hover:bg-[#f0f1f0] border-2">Send
                                                to us</button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <!-- Book Data  -->
                <div class="flex justify-center items-center">
                    <div
                        class="w-full mt-10 bg-white rounded-[1rem] p-5 mb-10 hover:drop-shadow-xl transition-all duration-300">
                        <div class="w-full h-[10%] pb-5 border-b-[1.5px] border-[#ddd]">
                            <div class="text-[1.25rem] font-medium">Book History</div>
                            <div class="text-zinc-500 text-sm mt-2">All the issued or returned books</div>
                        </div>
                        <div class="w-full">
                            <div class="flex flex-col mt-6">
                                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                    <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                                        <div class="overflow-auto border border-gray-200 md:rounded-lg">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-[#f0f1f0]">
                                                    <tr>
                                                        <th scope="col"
                                                            class="py-3.5 px-4 text-sm font-normal text-center rtl:text-right text-gray-500">
                                                            <button
                                                                class="flex items-center gap-x-3 focus:outline-none">
                                                                <span>Book</span>

                                                                <svg class="h-3" viewBox="0 0 10 11" fill="none"
                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                    <path
                                                                        d="M2.13347 0.0999756H2.98516L5.01902 4.79058H3.86226L3.45549 3.79907H1.63772L1.24366 4.79058H0.0996094L2.13347 0.0999756ZM2.54025 1.46012L1.96822 2.92196H3.11227L2.54025 1.46012Z"
                                                                        fill="currentColor" stroke="currentColor"
                                                                        stroke-width="0.1" />
                                                                    <path
                                                                        d="M0.722656 9.60832L3.09974 6.78633H0.811638V5.87109H4.35819V6.78633L2.01925 9.60832H4.43446V10.5617H0.722656V9.60832Z"
                                                                        fill="currentColor" stroke="currentColor"
                                                                        stroke-width="0.1" />
                                                                    <path
                                                                        d="M8.45558 7.25664V7.40664H8.60558H9.66065C9.72481 7.40664 9.74667 7.42274 9.75141 7.42691C9.75148 7.42808 9.75146 7.42993 9.75116 7.43262C9.75001 7.44265 9.74458 7.46304 9.72525 7.49314C9.72522 7.4932 9.72518 7.49326 9.72514 7.49332L7.86959 10.3529L7.86924 10.3534C7.83227 10.4109 7.79863 10.418 7.78568 10.418C7.77272 10.418 7.73908 10.4109 7.70211 10.3534L7.70177 10.3529L5.84621 7.49332C5.84617 7.49325 5.84612 7.49318 5.84608 7.49311C5.82677 7.46302 5.82135 7.44264 5.8202 7.43262C5.81989 7.42993 5.81987 7.42808 5.81994 7.42691C5.82469 7.42274 5.84655 7.40664 5.91071 7.40664H6.96578H7.11578V7.25664V0.633865C7.11578 0.42434 7.29014 0.249976 7.49967 0.249976H8.07169C8.28121 0.249976 8.45558 0.42434 8.45558 0.633865V7.25664Z"
                                                                        fill="currentColor" stroke="currentColor"
                                                                        stroke-width="0.3" />
                                                                </svg>
                                                            </button>
                                                        </th>
                                                        <th scope="col"
                                                            class="px-4 py-3.5 text-sm font-normal text-center rtl:text-right text-gray-500">
                                                            ISBN Number
                                                        </th>
                                                        <th scope="col"
                                                            class="px-12 py-3.5 text-sm font-normal text-center rtl:text-right text-gray-500">
                                                            Date of Issue
                                                        </th>

                                                        <th scope="col"
                                                            class="px-4 py-3.5 text-sm font-normal text-center rtl:text-right text-gray-500">
                                                            Status
                                                        </th>
                                                        <th scope="col" class="relative py-3.5 px-4">
                                                            <span class="sr-only">Edit</span>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">


                                                    <!-- Table body content remains the same -->
                                                    <?php
                                            $sid = $_SESSION['stdid'];
                                            $sql = "SELECT tblbooks.BookName,tblbooks.ISBNNumber,tblissuedbookdetails.IssuesDate,tblissuedbookdetails.BookId as rid, tblissuedbookdetails.RetrunStatus from  tblissuedbookdetails join tblstudents on tblstudents.StudentId=tblissuedbookdetails.StudentID join tblbooks on tblbooks.id=tblissuedbookdetails.BookId where tblstudents.StudentId=:sid";
                                            $query = $dbh->prepare($sql);
                                            $query->bindParam(':sid', $sid, PDO::PARAM_STR); // Bind the parameter
                                            $query->execute();

                                            // Check for errors during execution
                                            if ($query) {
                                                // Fetch the results
                                                $results = $query->fetchAll(PDO::FETCH_OBJ);
                                                
                                                // Process the results...
                                            } else {
                                                // Handle any errors
                                                echo "Error executing SQL query: " . $dbh->errorInfo()[2];
                                            }

                                            if ($query->rowCount() > 0) {
                                                
                                                $output = ""; // Initialize an empty string to store the HTML output

                                                foreach ($results as $result) {
                                                    $returnStatus = ($result->RetrunStatus == 1) ? 'Returned' : 'Pending';
                                                    $statusClass = ($result->RetrunStatus == 1) ? 'px-3 text-emerald-500 bg-emerald-100/60' : 'px-3 text-gray-500 bg-gray-100';

                                                    $timestamp = strtotime($result->IssuesDate);

                                                    // Format the Unix timestamp into "21st Feb 2024" format
                                                    $formattedDate = date('jS M Y', $timestamp);
                                                    $output .= <<<HTML

                                                    <tr>
                                                        <td class="px-4 py-4 text-sm font-medium whitespace-nowrap">
                                                            <div class="font-medium text-gray-800"> {$result->BookName}</div>
                                                        </td>
                                                        <td class="px-4 py-4 text-sm whitespace-nowrap text-center">
                                                            <div class="text-gray-700 "> {$result->ISBNNumber} </div>
                                                        </td>
                                                        <td class="px-4 py-4 text-sm whitespace-nowrap text-center">
                                                            <div class="text-gray-700 ">{$formattedDate}</div>
                                                        </td>
                                                        <td class="px-12 py-4 text-sm font-medium whitespace-nowrap text-center">
                                                            <div class="inline py-1 text-sm font-normal rounded-full {$statusClass}gap-x-2 dark:bg-gray-800">
                                                            {$returnStatus}
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-4 text-sm whitespace-nowrap text-center">
                                                            <button class="px-1 py-1 text-gray-500 transition-colors duration-200 rounded-lg dark:text-gray-300 hover:bg-gray-100">
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" />
                                                                </svg>
                                                            </button>
                                                        </td>
                                                    </tr>
                                            HTML;
                                                }

                                                echo $output; // Output the HTML after the loop
                                            }

                                            

                                            
                                        ?>


                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- Page Title  -->

        </div>
    </div>


    <script src="https://kit.fontawesome.com/7679c1475f.js" crossorigin="anonymous"></script>
    <script>
    function filterPeriod(button, period) {
        const lineToday = document.querySelector('.line-today');
        const lineWeek = document.querySelector('.line-week');
        const lineMonth = document.querySelector('.line-month');
        const lines = document.querySelectorAll('.line');

        lines.forEach(line => {
            line.classList.remove('bg-[#6366F1]', 'h-[3.5px]')
            line.classList.add('bg-[#ddd]', 'h-[1.5px]')
        })
        if (period == 'today') {
            lineToday.classList.remove('bg-[#ddd]', 'h-[1.5px]')
            lineToday.classList.add('bg-[#6366F1]', 'h-[3.5px]')
        } else if (period == 'week') {
            lineWeek.classList.remove('bg-[#ddd]', 'h-[1.5px]')
            lineWeek.classList.add('bg-[#6366F1]', 'h-[3.5px]')
        } else if (period == 'month') {
            lineMonth.classList.remove('bg-[#ddd]', 'h-[1.5px]')
            lineMonth.classList.add('bg-[#6366F1]', 'h-[3.5px]')
        }
    }

    function displayNotifications(notifications) {
        const notificationsContainer = document.getElementById('notifications');
        notificationsContainer.innerHTML = '';
        notifications.forEach(notification => {
            const notificationElement = document.createElement('div');
            notificationElement.classList.add('pb-5', 'pt-6', 'border-b-[1.5px]', 'border-[#ddd]', 'pt-2',
                'text-sm', 'font-medium');
            notificationElement.textContent = notification.message;
            notificationsContainer.appendChild(notificationElement);
        });
    }

    const notifications = [{
            message: 'Notification 1'
        },
        {
            message: 'Notification 2'
        },
        {
            message: 'Notification 3'
        },
        {
            message: 'Notification 4'
        },
        {
            message: 'Notification 5'
        },
        {
            message: 'Notification 6'
        },
        {
            message: 'Notification 7'
        },
        {
            message: 'Notification 8'
        },
        {
            message: 'Notification 9'
        },
        {
            message: 'Notification 10'
        },
        {
            message: 'Notification 11'
        },
        {
            message: 'Notification 12'
        },
        // Add more notifications as needed
    ];

    displayNotifications(notifications);
    var issue = <?php echo $jsonIssue ?>;
    var pending = <?php echo $jsonPending ?>;
    var returned = <?php echo $jsonReturned ?>;
    var lost = 0;

    const xValues = ["Issued", "Pending", "Returned", "Lost"];
    const yValues = [issue, pending, returned, lost];


    total = yValues.reduce((sum, value) => sum + value, 0);
    percentages = yValues.map(value => ((value / total) * 100).toFixed(2));

    var issuePer = document.getElementById('issuePercentage');
    issuePer.innerHTML = percentages[0] + "%";

    var pendPer = document.getElementById('pendingPercentage');
    pendPer.innerHTML = percentages[1] + "%";

    var retunPer = document.getElementById('returnedPercentage');
    retunPer.innerHTML = percentages[2] + "%";

    var lostPer = document.getElementById('lostPercentage');
    lostPer.innerHTML = percentages[3] + "%";

    const barColors = [
        "#71e38f",
        "#546de3",
        "#d85656",
        "#e2de54"
    ];

    new Chart("myChart_book", {
        type: "doughnut",
        data: {
            labels: xValues,
            datasets: [{
                backgroundColor: barColors,
                data: yValues,
            }]
        },
        options: {
            cutoutPercentage: 80, // Optional: Adjust the size of the hole in the center of the doughnut
            legend: {
                position: 'right', // Position the legend to the right side
                labels: {
                    usePointStyle: true, // Use point style for legend items
                    padding: 20, // Set padding between legend items
                }
            },
            layout: {
                padding: {
                    left: 20,
                    right: 20,
                    top: 20,
                    bottom: 20
                }
            }
        }
    });
    </script>
</body>

</html>