<?php 

function retrieveFinanceReviews() {
    require_once('./back/php/connect.php');
    $connect = connect();

    $userId = $_SESSION['user']['code'];
    $currentDate = date('Y-m-d');

    // Fetch completed reviews for today
    $stmtCompletedToday = $connect->prepare("SELECT id_code, code, amount, review_status, review_time FROM finance_review WHERE id_code = ? AND review_status = 'Completed' AND DATE(review_time) = ?");
    $stmtCompletedToday->bind_param("is", $userId, $currentDate);
    $stmtCompletedToday->execute();
    $resultCompletedToday = $stmtCompletedToday->get_result();
    $completedToday = $resultCompletedToday->fetch_all(MYSQLI_ASSOC);
    $stmtCompletedToday->close();

    // Fetch pending reviews
    $stmtPending = $connect->prepare("SELECT id_code, code, amount, review_status, review_time FROM finance_review WHERE id_code = ? AND review_status = 'Pending'");
    $stmtPending->bind_param("i", $userId);
    $stmtPending->execute();
    $resultPending = $stmtPending->get_result();
    $pending = $resultPending->fetch_all(MYSQLI_ASSOC);
    $stmtPending->close();

    // Fetch old completed reviews
    $stmtOldCompleted = $connect->prepare("SELECT id_code, code, amount, review_status, review_time FROM finance_review WHERE id_code = ? AND review_status = 'Completed' AND DATE(review_time) < ?");
    $stmtOldCompleted->bind_param("is", $userId, $currentDate);
    $stmtOldCompleted->execute();
    $resultOldCompleted = $stmtOldCompleted->get_result();
    $oldCompleted = $resultOldCompleted->fetch_all(MYSQLI_ASSOC);
    $stmtOldCompleted->close();

    // Display the results with Bootstrap styling
    echo '<div class="container mt-4">';
    
    // Completed Reviews Today
    echo '<div class="card mb-3 shadow-sm">';
    echo '<div class="card-header bg-success text-white">Completed Reviews Today</div>';
    echo '<div class="card-body">';
    if (!empty($completedToday)) {
        echo '<ul class="list-group list-group-flush">';
        foreach ($completedToday as $review) {
            echo '<li class="list-group-item">Code: '.$review['code'].' | Amount: '.$review['amount'].' birr | Review Time: '.$review['review_time'].'</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No completed reviews for today.</p>';
    }
    echo '</div>';
    echo '</div>';
    
    // Pending Reviews
    echo '<div class="card mb-3 shadow-sm">';
    echo '<div class="card-header bg-warning text-white">Pending Reviews</div>';
    echo '<div class="card-body">';
    if (!empty($pending)) {
        echo '<ul class="list-group list-group-flush">';
        foreach ($pending as $review) {
            echo '<li class="list-group-item">Code: '.$review['code'].' | Amount: '.$review['amount'].' birr | Review Time: '.$review['review_time'].'</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No pending reviews.</p>';
    }
    echo '</div>';
    echo '</div>';
    
    // Old Completed Reviews
    echo '<div class="card mb-3 shadow-sm">';
    echo '<div class="card-header bg-secondary text-white">Old Completed Reviews</div>';
    echo '<div class="card-body">';
    if (!empty($oldCompleted)) {
        echo '<ul class="list-group list-group-flush">';
        foreach ($oldCompleted as $review) {
            echo '<li class="list-group-item">Code: '.$review['code'].' | Amount: '.$review['amount'].' birr | Review Time: '.$review['review_time'].'</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No old completed reviews.</p>';
    }
    echo '</div>';
    echo '</div>';
    
    echo '</div>'; // Close container
}
retrieveFinanceReviews();

?>