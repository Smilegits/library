function filterPeriod(button , period) {
    const lineToday = document.querySelector('.line-today');
    const lineWeek = document.querySelector('.line-week');
    const lineMonth = document.querySelector('.line-month');
    const lines = document.querySelectorAll('.line');

    lines.forEach(line=>{
        line.classList.remove('bg-[#6366F1]' , 'h-[3.5px]')
        line.classList.add('bg-[#ddd]' , 'h-[1.5px]')
    })
    if(period == 'today'){
        lineToday.classList.remove('bg-[#ddd]' , 'h-[1.5px]')
        lineToday.classList.add('bg-[#6366F1]' , 'h-[3.5px]')
    }
    else if(period == 'week'){
        lineWeek.classList.remove('bg-[#ddd]' , 'h-[1.5px]')
        lineWeek.classList.add('bg-[#6366F1]' , 'h-[3.5px]')
    }
    else if(period == 'month'){
        lineMonth.classList.remove('bg-[#ddd]' , 'h-[1.5px]')
        lineMonth.classList.add('bg-[#6366F1]' , 'h-[3.5px]')
    }
}

function displayNotifications(notifications) {
    const notificationsContainer = document.getElementById('notifications');
    notificationsContainer.innerHTML = '';
    notifications.forEach(notification => {
        const notificationElement = document.createElement('div');
        notificationElement.classList.add('pb-5', 'pt-6' ,'border-b-[1.5px]' , 'border-[#ddd]' , 'pt-2' , 'text-sm' , 'font-medium');
        notificationElement.textContent = notification.message;
        notificationsContainer.appendChild(notificationElement);
    });
}

const notifications = [
{ message: 'Notification 1' },
{ message: 'Notification 2' },
{ message: 'Notification 3' },
{ message: 'Notification 4' },
{ message: 'Notification 5' },
{ message: 'Notification 6' },
{ message: 'Notification 7' },
{ message: 'Notification 8' },
{ message: 'Notification 9' },
{ message: 'Notification 10' },
{ message: 'Notification 11' },
{ message: 'Notification 12' },
// Add more notifications as needed
];

displayNotifications(notifications);
// var issue = document.getElementById("issue");
// console.log(issue);

const xValues = ["Issued", "Pending", "Returned", "Lost"];
const yValues = [6, 6, 1, 0];
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