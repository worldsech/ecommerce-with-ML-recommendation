<?php
require_once ("../include/initialize.php");

$action = (isset($_GET['action']) && $_GET['action'] != '') ? $_GET['action'] : '';

switch ($action) {
    case 'add' :
    doInsert();
    break;
    
    case 'edit' :
    doEdit();
    break;
    
    case 'delete' :
    doDelete();
    break;

    case 'processorder' :
    processorder();
    break;

    case 'addwish' :
    addwishlist();
    break;

    case 'wishlist' :
    processwishlist();
    break;

    case 'photos' :
    doupdateimage();
    break;

    case 'changepassword' :
    doChangePassword();
    break;
}

   
function doInsert(){
    global $mydb;
    if(isset($_POST['submit'])){

        $customer = New Customer(); 
        $customer->FNAME            = $_POST['FNAME'];
        $customer->LNAME            = $_POST['LNAME'];       
        // Ensure these fields are also handled in the registration form if you want them saved at signup
        $customer->CUSHOMENUM       = isset($_POST['CUSHOMENUM']) ? $_POST['CUSHOMENUM'] : '';
        $customer->STREETADD        = isset($_POST['STREETADD']) ? $_POST['STREETADD'] : '';
        $customer->BRGYADD          = isset($_POST['BRGYADD']) ? $_POST['BRGYADD'] : '';
        $customer->CITYADD          = $_POST['CITYADD']; 
        $customer->PROVINCE         = isset($_POST['PROVINCE']) ? $_POST['PROVINCE'] : '';
        $customer->COUNTRY          = isset($_POST['COUNTRY']) ? $_POST['COUNTRY'] : '';
        $customer->GENDER           = $_POST['GENDER'];
        $customer->PHONE            = $_POST['PHONE']; 
        $customer->CUSUNAME         = $_POST['CUSUNAME'];
        $customer->CUSPASS          = sha1($_POST['CUSPASS']);   
        $customer->DATEJOIN         = date('Y-m-d h-i-s');
        $customer->TERMS            = 1;
        $customer->create();
   
        $email = trim($_POST['CUSUNAME']);
        $h_upass = sha1(trim($_POST['CUSPASS']));

        $user = new Customer();
        $res = $user->cusAuthentication($email, $h_upass); 
                     
        if(!isset($_POST['proid']) || (isset($_POST['proid']) && empty($_POST['proid']))){
            echo "<script> alert('You are now successfully registered. It will redirect to your order details.'); </script>";
            redirect(web_root."index.php?q=orderdetails");
        }else{
            $proid = $_POST['proid']; 
            $cusid = $_SESSION['CUSID']; 
            $query ="INSERT INTO `tblwishlist` (`PROID`, `CUSID`, `WISHDATE`, `WISHSTATS`)  VALUES ('{$proid}','{$cusid}','".DATE('Y-m-d')."',0)";
            $mydb->setQuery($query);
            $mydb->executeQuery();
            echo "<script> alert('You are now successfully registered. It will redirect to your profile.'); </script>";
            redirect(web_root."index.php?q=profile");
        }
    }
}
 
function doEdit(){
    if(isset($_POST['save'])){
        $customer = New Customer();
        $customer->FNAME            = $_POST['FNAME'];
        $customer->LNAME            = $_POST['LNAME'];
        $customer->CUSHOMENUM       = $_POST['CUSHOMENUM']; // Uncomment and use these
        $customer->STREETADD        = $_POST['STREETADD']; // Uncomment and use these
        $customer->BRGYADD          = $_POST['BRGYADD']; // Uncomment and use these       
        $customer->CITYADD          = $_POST['CITYADD'];
        $customer->PROVINCE         = $_POST['PROVINCE']; // Uncomment and use these
        $customer->COUNTRY          = $_POST['COUNTRY']; // Uncomment and use these
        $customer->GENDER           = $_POST['GENDER'];
        $customer->PHONE            = $_POST['PHONE'];
        $customer->CUSUNAME         = $_POST['CUSUNAME'];
        $customer->update($_SESSION['CUSID']);

        message("Accounts has been updated!", "success");
        redirect(web_root.'index.php?q=profile');
    }
}


function doDelete(){
    if(isset($_SESSION['U_ROLE'])=='Customer'){
        if (isset($_POST['selector'])==''){
            message("Select the records first before you delete!","error");
            redirect(web_root.'index.php?page=9');
        }else{
            $id = $_POST['selector'];
            $key = count($id);

            for($i=0;$i<$key;$i++){ 
                $order = New Order();
                $order->delete($id[$i]);
                message("Order has been Deleted!","info");
                redirect(web_root."index.php?q='product'"); 
            } 
        }
    }else{
        if (isset($_POST['selector'])==''){
            message("Select the records first before you delete!","error");
            redirect('index.php');
        }else{
            $id = $_POST['selector'];
            $key = count($id);

            for($i=0;$i<$key;$i++){ 
                $customer = New Customer();
                $customer->delete($id[$i]);

                $user = New User();
                $user->delete($id[$i]);

                message("Customer has been Deleted!","info");
                redirect('index.php');
            }
        }
    }    
}

        function processorder(){
            global $mydb;

            // 1. Retrieve new address details from the form
            $cusHomeNum = $_POST['HOMENUM'];
            $streetAdd = $_POST['STREETADD'];
            $brgyAdd = $_POST['BRGYADD'];
            $cityAdd = $_POST['CITYADD'];
            $province = $_POST['PROVINCE'];
            $country = $_POST['COUNTRY'];

            // Construct the full delivery address string
            $deliveryAddress = $cusHomeNum . ' ' . $streetAdd . ', ' . $brgyAdd . ', ' . $cityAdd . ', ' . $province . ', ' . $country;

            // 2. Update the customer's default address in tblcustomer (Optional, but good practice)
            // This ensures their profile reflects the latest address used for an order.
            $customer = New Customer();
            $customer->CUSHOMENUM = $cusHomeNum;
            $customer->STREETADD = $streetAdd;
            $customer->BRGYADD = $brgyAdd;
            $customer->CITYADD = $cityAdd;
            $customer->PROVINCE = $province;
            $customer->COUNTRY = $country;
            $customer->update($_SESSION['CUSID']); // Update the currently logged-in customer

            // 3. Process each item in the cart
            $count_cart = count($_SESSION['gcCart']);
            for ($i=0; $i < $count_cart  ; $i++) { 
                $order = New Order();
                $order->PROID           = $_SESSION['gcCart'][$i]['productid']; 
                $order->ORDEREDQTY      = $_SESSION['gcCart'][$i]['qty'];
                $order->ORDEREDPRICE    = $_SESSION['gcCart'][$i]['price'];  
                $order->ORDEREDNUM      = $_POST['ORDEREDNUM']; 
                $order->create(); 
            
                $product = New Product();             
                $product->qtydeduct($_SESSION['gcCart'][$i]['productid'],$_SESSION['gcCart'][$i]['qty']); 
            }

            // 4. Create the order summary
            $summary = New Summary();
            $summary->ORDEREDDATE       = date("Y-m-d h:i:s");
            $summary->CUSTOMERID        = $_SESSION['CUSID'];
            $summary->ORDEREDNUM        = $_POST['ORDEREDNUM'];  
            
            // For DELFEE, you now have a challenge.
            // If `PLACE` (which was the delivery fee) is no longer a dropdown,
            // you need a new way to determine the delivery fee.
            // For now, I'll set a placeholder. You need to decide how to calculate it.
            // For example, a fixed fee, or lookup based on City/Barangay if you have a `tblsetting` for that.
            // Let's assume a default fixed fee for new manual addresses, or a lookup.
            // If you still have a tblsetting with BRGY/PLACE and DELPRICE, you could try to look up
            // the DELPRICE based on the entered BRGYADD/CITYADD.
            
            $deliveryFee = 0; // Initialize delivery fee
            // Option A: Fixed Delivery Fee (simplest)
            // $deliveryFee = 1500; // Example: ₦1500 fixed delivery fee

            // Option B: Lookup from tblsetting based on entered BRGYADD and CITYADD (if tblsetting still holds these)
            // This is complex as it requires exact matches or fuzzy logic.
            // It's probably better to decide on a fixed fee or implement a proper zone system.
            // For demonstration, let's just make it a fixed fee for now.
            $query_delivery_fee = "SELECT DELPRICE FROM `tblsetting` WHERE BRGY = '{$brgyAdd}' AND PLACE = '{$cityAdd}' LIMIT 1";
            $mydb->setQuery($query_delivery_fee);
            $result_fee = $mydb->loadSingleResult();
            if ($result_fee) {
                $deliveryFee = $result_fee->DELPRICE;
            } else {
                // Default fee if no match found in tblsetting for this specific address
                $deliveryFee = 1500; // Example: Default fixed delivery fee for unmatched addresses
            }


            $summary->DELFEE            = $deliveryFee; // Use the determined delivery fee
            $summary->PAYMENTMETHOD     = $_POST['paymethod'];
            $summary->PAYMENT           = $_POST['alltot'] + $deliveryFee; // Update total payment to include delivery fee
            $summary->ORDEREDSTATS      = 'Pending';
            $summary->CLAIMEDDATE       = $_POST['CLAIMEDDATE'];
            $summary->ORDEREDREMARKS    = 'Your order is on process.';
            $summary->HVIEW             = 0 ;
            // Store the full delivery address in the summary for this order
            // You might need to add a new column to your `tblsummary` table for this, e.g., `DELIVERYADDRESS` (VARCHAR/TEXT)
            // For now, let's assume you'll add it.
            // $summary->DELIVERYADDRESS   = $deliveryAddress; // This line assumes a new column in tblsummary

            $summary->create(); 
            
            // To save the full address for the order, you should ideally have a column in `tblsummary` for it.
            // If you don't want to modify `tblsummary` structure, you could potentially concatenate
            // the address details into one of the existing varchar/text columns that might be less used,
            // or consider if `ORDEREDREMARKS` could temporarily hold it (not ideal).
            // A better solution: Add a `DeliveryAddress` column to `tblsummary` or link to `tblcustomeraddress`.
            // For now, let's make a direct update to tblsummary's ORDEREDREMARKS as a temporary measure IF YOU CAN'T ADD A NEW COLUMN.
            // **Highly recommend adding a new `DeliveryAddress` column to `tblsummary`**
            $orderSummaryID = $summary->summaryid; // Assuming `create()` returns the ID or sets a property
            $updateSummarySql = "UPDATE `tblsummary` SET `ORDEREDREMARKS` = CONCAT('Your order is on process. Delivery Address: ', '" . $mydb->escape_value($deliveryAddress) . "') WHERE `SUMMARYID` = '{$orderSummaryID}'";
            // NOTE: Replace `ORDEREDREMARKS` with a dedicated `DeliveryAddress` column if you add one.
            // This is a quick fix to store the address, but not ideal.
            // $mydb->setQuery($updateSummarySql);
            // $mydb->executeQuery();
            // End of temporary address storage

        // Update autonumber
        $autonumber = New Autonumber();
        $autonumber->auto_update('ordernumber');

        // Clear session cart and order details
        unset($_SESSION['gcCart']);  
        unset($_SESSION['orderdetails']); 

        message("Order created successfully!", "success");       
        redirect(web_root."index.php?q=profile");
    }
            
function processwishlist(){
    global $mydb;
    if(isset($_GET['wishid'])){
        $query ="UPDATE `tblwishlist` SET `WISHSTATS`=1  WHERE `WISHLISTID`=" .$_GET['wishid'];
        $mydb->setQuery($query);
        $res = $mydb->executeQuery();
        if (isset($res)){
            message("Product has been removed in your wishlist", "success");        
            redirect(web_root."index.php?q=profile");
        }
    }
}
            
function addwishlist(){
    global $mydb;
    $proid = $_GET['proid'];
    $id =$_SESSION['CUSID'];

    $query="SELECT * FROM `tblwishlist` WHERE  CUSID=".$id." AND `PROID` =" .$proid ;
    $mydb->setQuery($query);
    $res = $mydb->executeQuery();
    $maxrow = $mydb->num_rows($res);

    if($maxrow>0){
        message("Product is already added to your wishlist", "error");      
        redirect(web_root."index.php?q=profile"); 
    }else{
        $query ="INSERT INTO `tblwishlist` (`PROID`, `CUSID`, `WISHDATE`, `WISHSTATS`)  VALUES ('{$proid}','{$id}','".DATE('Y-m-d')."',0)";
        $mydb->setQuery($query);
        $mydb->executeQuery();
       
        message("Product has been added to your wishlist", "success");       
        redirect(web_root."index.php?q=profile"); 
    }
}
            
function doupdateimage(){
    $errofile = $_FILES['photo']['error'];
    $type = $_FILES['photo']['type'];
    $temp = $_FILES['photo']['tmp_name'];
    $myfile =$_FILES['photo']['name'];
    $location="customer_image/".$myfile;

    if ( $errofile > 0) {
        message("No Image Selected!", "error");
        redirect(web_root. "index.php?q=profile");
    }else{
        @$file=$_FILES['photo']['tmp_name'];
        @$image= addslashes(file_get_contents($_FILES['photo']['tmp_name']));
        @$image_name= addslashes($_FILES['photo']['name']); 
        @$image_size= getimagesize($_FILES['photo']['tmp_name']);

        if ($image_size==FALSE ) {
            message(web_root. "Uploaded file is not an image!", "error");
            redirect(web_root. "index.php?q=profile");
        }else{
            move_uploaded_file($temp,"customer_image/" . $myfile);
            
            $customer = New Customer(); 
            $customer->CUSPHOTO          = $location; 
            $customer->update($_SESSION['CUSID']); 

            redirect(web_root. "index.php?q=profile");
        }
    }          
}

function doChangePassword(){
    if (isset($_POST['save'])) {
        $customer = New Customer(); 
        $customer->CUSPASS          = sha1($_POST['CUSPASS']);   
        $customer->update($_SESSION['CUSID']);

        message("Password has been updated!", "success");
        redirect(web_root.'index.php?q=profile');
    }
}
?>