<?php
// This script should be run by a cron job (e.g., once per night).

require_once(dirname(__FILE__) . '/../include/initialize.php');

global $mydb;

echo "Starting recommendation generation...\n";

// 1. Fetch all confirmed purchase data: user -> list of products
$mydb->setQuery("SELECT s.CUSTOMERID, o.PROID 
                 FROM tblsummary s 
                 JOIN tblorder o ON s.ORDEREDNUM=o.ORDEREDNUM 
                 WHERE s.ORDEREDSTATS='Confirmed'");
$purchases = $mydb->loadResultList();

if (!$purchases) {
    die("No purchase data found. Exiting.\n");
}

// 2. Build a user-item matrix (which user bought which items)
$user_items = [];
foreach ($purchases as $purchase) {
    if (!isset($user_items[$purchase->CUSTOMERID])) {
        $user_items[$purchase->CUSTOMERID] = [];
    }
    // Ensure unique products per user
    if (!in_array($purchase->PROID, $user_items[$purchase->CUSTOMERID])) {
        $user_items[$purchase->CUSTOMERID][] = $purchase->PROID;
    }
}

// 3. For each user, find similar users and generate recommendations
$recommendations = [];
$all_users = array_keys($user_items);

foreach ($all_users as $target_user_id) {
    // Don't generate recommendations if the user has no purchase history
    if (empty($user_items[$target_user_id])) {
        continue;
    }

    $target_items = $user_items[$target_user_id];
    $scores = [];

    // Compare with every other user
    foreach ($all_users as $other_user_id) {
        if ($target_user_id == $other_user_id) continue;
        if (empty($user_items[$other_user_id])) continue;

        $other_items = $user_items[$other_user_id];
        
        // Find common items purchased by both users
        $common_items = array_intersect($target_items, $other_items);
        $similarity = count($common_items); // Our simple similarity score

        // If they have items in common, they are similar
        if ($similarity > 0) {
            // Find items the other user bought but the target user hasn't
            $new_items_to_recommend = array_diff($other_items, $target_items);

            foreach ($new_items_to_recommend as $item_id) {
                if (!isset($scores[$item_id])) {
                    $scores[$item_id] = 0;
                }
                // Add the similarity score to the item's recommendation score
                $scores[$item_id] += $similarity;
            }
        }
    }

    // Sort recommendations by the highest score
    arsort($scores); 
    $recommendations[$target_user_id] = $scores;
}


// 4. Clear old recommendations and store the new ones in the database
echo "Storing new recommendations...\n";
$mydb->setQuery("TRUNCATE TABLE `tblproduct_recommendations`");
$mydb->executeQuery();

foreach ($recommendations as $cusid => $rec_list) {
    // Only store the top 10 recommendations per user
    $rec_list_limited = array_slice($rec_list, 0, 10, true);

    foreach ($rec_list_limited as $proid => $score) {
        $sql = "INSERT INTO `tblproduct_recommendations` (CUSID, RECOMMENDED_PROID, SCORE) 
                VALUES ({$cusid}, {$proid}, {$score})";
        $mydb->setQuery($sql);
        $mydb->executeQuery();
    }
}

echo "Recommendation generation complete.\n";

?>