<?php 

function displayReview() {
    require_once('./back/php/connect.php');
    $connect = connect();

    // Fetch pending reviews
    $stmtPending = $connect->prepare("SELECT id_code, code, amount, review_status, review_time FROM finance_review WHERE review_status = 'approved'");
    $stmtPending->execute();
    $resultPending = $stmtPending->get_result();
    $pending = $resultPending->fetch_all(MYSQLI_ASSOC);
    $stmtPending->close();

    // Display the results with Bootstrap styling
    echo '<div class="container mt-4">';
    
    // Pending Reviews
    echo '<div class="card mb-3 shadow-sm">';
    echo '<div class="card-header bg-warning text-white">Approved lists</div>';
    echo '<div class="card-body">';
    if (!empty($pending)) {
        echo '<ul class="list-group list-group-flush">';
        foreach ($pending as $review) {
            // Format the review time (optional, based on how you want to display it)
            $formattedTime = date('Y-m-d H:i:s', strtotime($review['review_time']));
            echo '<li class="list-group-item">Code: '.$review['code'].' | Amount: '.$review['amount'].' birr | Review Time: '.$formattedTime.'
             
                <form action="" method="post">
                    <div class="top-right-buttons">
                        <input type="hidden" name="review_id" value="'.$review['code'].'">
                        <input type="hidden" name="date" value="'.$formattedTime.'">
                    </div>
                </form>
            </li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No pending reviews.</p>';
    }
    echo '</div>';
    echo '</div>';
    
    // Close container div
    echo '</div>';
}

// Call the function to display the reviews
displayReview();

?>