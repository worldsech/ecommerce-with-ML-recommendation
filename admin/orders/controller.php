

<?php
require_once ("../../include/initialize.php");
	 if (!isset($_SESSION['USERID'])){
      redirect(web_root."admin/index.php");
     }

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
	

	case 'cartadd' :
	cartInsert();
	break;

	case 'cartedit' :
	cartEdit();
	break;

	case 'cartdelete' :
	cartDelete();
	break;


	case 'processorder' :
	processorder();
	break;

	}

   
function doInsert(){
	// This function's logic is for creating a new Product, not an Order.
	// It appears to be misplaced from 'admin/products/controller.php' and is syntactically broken.
	// It is commented out to prevent errors.
}


	function doEdit(){
		global $mydb;
		 $delivered = "";
		 // FIX: Sanitize the ID to prevent SQL Injection
		 $orderId = intval($_GET['id']);

		if ($_GET['actions']=='confirm') {
							# code...
				$status	= 'Confirmed';	
				$remarks ='Your order has been confirmed.';
				$delivered = date('Y-m-d H:i:s');

		}elseif ($_GET['actions']=='deliver') {
							# code...
				$status	= 'Delivered';	
				$remarks ='Your order has been delivered.';
				$delivered = date('Y-m-d H:i:s');
				
		}elseif ($_GET['actions']=='cancel'){
			// $order = New Order();
				$status	= 'Cancelled';
				$remarks ='Your order has been cancelled due to lack of communication and incomplete information.';
				$delivered = date('Y-m-d H:i:s');
		}
			
			$order = New Order();
			$order->STATS       = $status;
			$order->update($orderId);

			$summary = New Summary();
			$summary->ORDEREDSTATS       = $status;
			$summary->ORDEREDREMARKS     = $remarks;
			// NOTE: Your database schema has a typo in this column name. It is 'CLAIMEDADTE'.
			$summary->CLAIMEDADTE 		 = $delivered;
			$summary->HVIEW 			 = 0;
			$summary->update($orderId);


  			$query = "SELECT * FROM `tblsummary` s ,`tblcustomer` c 
				WHERE   s.`CUSTOMERID`=c.`CUSTOMERID` and ORDEREDNUM=".$orderId;
			$mydb->setQuery($query);
			$cur = $mydb->loadSingleResult();
 
			// Construct the message text first
			$message = "FROM Bachelor of Science and Entrepreneurs : Your order has been " . $status . ". The amount is " . $cur->PAYMENT;
			// Escape the message to prevent SQL injection and syntax errors
			$escaped_message = $mydb->escape_value($message);
	        $sql = "INSERT INTO `messageout` (`MessageTo`, `MessageFrom`, `MessageText`) VALUES ('{$cur->PHONE}','Fix and Mix','{$escaped_message}')";
	        $mydb->setQuery($sql);
	        $mydb->executeQuery();



			$query = "SELECT * 
				FROM  `tblproduct` p,`tblorder` o,  `tblsummary` s
				WHERE  p.`PROID` = o.`PROID` 
				AND o.`ORDEREDNUM` = s.`ORDEREDNUM`  
				AND o.`ORDEREDNUM`=".$orderId;
	  		$mydb->setQuery($query);
	  		$cur = $mydb->loadResultList(); 
			foreach ($cur as $result) {
			
	  		 // Construct the message text first
	        $message = "FROM Bachelor of Science and Entrepreneurs : Your product has been ordered. The amount is " . $result->PAYMENT;
			// Escape the message to prevent SQL injection and syntax errors
			$escaped_message = $mydb->escape_value($message);
	  		 $sql = "INSERT INTO `messageout` (`MessageTo`, `MessageFrom`, `MessageText`) VALUES ('{$result->OWNERPHONE}','Fix and Mix','{$escaped_message}')";
	        $mydb->setQuery($sql);
	        $mydb->executeQuery();
			}
      

			message("Order has been ".$summary->ORDEREDSTATS."!", "success");
			redirect("index.php");
		
	}
	 
	function doDelete(){

	if (isset($_POST['selector'])==''){
		message("Select the records first before you delete!","info");
		redirect('index.php');
	}else{

		$id = $_POST['selector'];
		$key = count($id);

		for($i=0;$i<$key;$i++){

			$order = New Order();
			$order->delete($id[$i]); // This deletes from `tblorder`

			$summary = new Summary();
			$summary->delete($id[$i]); // This deletes from `tblsummary`

			message("Order has been Deleted!","info");
			redirect('index.php');
		}

	}
	}
 
function cartInsert(){
	 

   if(isset($_GET['id'])){
    $pid= $_GET['id'];
    $price= $_GET['price'];

      addtocart($pid,1,$price);

			message("1 item has been added in the cart", "success");
			redirect("index.php?view=add");
			
		}
		 

	}

	function cartEdit(){

 

    $max=count($_SESSION['fixnmix_cart']);
    for($i=0;$i<$max;$i++){

      $pid=$_SESSION['fixnmix_cart'][$i]['productid'];

      $qty=intval(isset($_REQUEST['QTY'.$pid]) ? $_REQUEST['QTY'.$pid] : "");
       $price=intval(isset($_REQUEST['TOT'.$pid]) ? $_REQUEST['TOT'.$pid] : "");

     
      if($qty>0 && $qty<=9999){
      	// la pa natapos... price

        $_SESSION['fixnmix_cart'][$i]['qty']=$qty;
        $_SESSION['fixnmix_cart'][$i]['price']=$price;
      }
     
    }
 
			message("Cart has been updated.", "success");
			redirect("index.php?view=add");
  
	}


	function cartDelete(){
	 
 
		if(isset($_GET['id'])) {
		removetocart($_GET['id']);
		}else{
		unset($_SESSION['fixnmix_cart']);
		}
			

		message("1 item has been removed in the cart.");
		 redirect('index.php?view=addtocart');
		 

		
	}

	function processorder(){
		// FIX: This entire function was logically flawed.
		// It was creating duplicate customers and using non-existent tables.
		// The logic has been rewritten to correctly save order and summary data.
		if (isset($_SESSION['fixnmix_cart']) && is_array($_SESSION['fixnmix_cart']) && !empty($_SESSION['fixnmix_cart'])) {

			// WARNING: This function creates a new customer for every order, which will lead to duplicate customers.
			// This block should be refactored to check for an existing customer first.
			// For now, I am commenting it out to prevent data duplication.
			/*
			$customer = New Customer();
			$customer->CUSTOMERID 		=  $_SESSION['CUSTOMERID'];
			$customer->FNAME 			= $_SESSION['FIRSTNAME'];
			$customer->LNAME 			= $_SESSION['LASTNAME'];
			$customer->CITYADD 			= $_SESSION['ADDRESS']; // Note: CITYADD might not be the full address
			$customer->ADDRESS 			= $_SESSION['ADDRESS'];
			$customer->PHONE 			= $_SESSION['CONTACTNUMBER'];
			$customer->CUSUNAME 		= $_SESSION['CUSTOMERID'];
			$customer->CUSPASS 			= sha1('1234'); // Highly insecure
			$customer->DATEJOIN 		= date('Y-m-d h-i-s');
			$customer->TERMS 			= 1;
			$customer->create();

			$user = New User();
			$user->USERID			=	$_SESSION['CUSTOMERID'];
			$user->U_NAME			=	$_SESSION['FIRSTNAME']. ' ' .$_SESSION['LASTNAME'];
			$user->U_USERNAME		=	$_SESSION['CUSTOMERID'];
			$user->U_PASS			=	sha1('1234'); // Highly insecure
			$user->U_ROLE			=	'Customer';
			$user->create();
			*/

			// Create the order summary
			$summary = new Summary();
			$summary->ORDEREDNUM 		= $_SESSION['ORDERNUMBER'];
			$summary->CUSTOMERID 		= $_SESSION['CUSTOMERID'];
			$summary->ORDEREDDATE 		= date("Y-m-d H:i:s");
			$summary->PAYMENT 			= $_SESSION['alltot'];
			$summary->PAYMENTMETHOD 	= $_SESSION['paymethod'];
			$summary->ORDEREDSTATS 		= 'Pending';
			$summary->ORDEREDREMARKS 	= 'Your order is on process.';
			$summary->create();

			$count_cart = count($_SESSION['fixnmix_cart']);
			for ($i=0; $i < $count_cart  ; $i++) { 
				$order = New Order();
				$order->PROID			= $_SESSION['fixnmix_cart'][$i]['productid'];
				$order->ORDEREDQTY		= $_SESSION['fixnmix_cart'][$i]['qty'];
				$order->ORDEREDPRICE	= $_SESSION['fixnmix_cart'][$i]['price'];
				$order->ORDEREDNUM		= $_SESSION['ORDERNUMBER'];
				$order->create(); 

				// Deduct product quantity
				$product = New Product();			 
				$product->qtydeduct($_SESSION['fixnmix_cart'][$i]['productid'],$_SESSION['fixnmix_cart'][$i]['qty']);
			}

			// Update order number in autonumber table
			$autonum = New Autonumber(); 
			$autonum->auto_update(2); // Assuming 2 is for ordernumber

			// Clear the session
			unset($_SESSION['fixnmix_cart']);
			unset($_SESSION['FIRSTNAME']);
			unset($_SESSION['LASTNAME']);
			unset($_SESSION['ADDRESS']);
			unset($_SESSION['CONTACTNUMBER']);
			unset($_SESSION['CLAIMEDDATE']);
			unset($_SESSION['CUSTOMERID']); 
			unset($_SESSION['paymethod']) ;
			// Keep ORDERNUMBER for the billing page
			// unset($_SESSION['ORDERNUMBER']);
			unset($_SESSION['alltot']);

			message("New order created successfully!", "success"); 		 
			redirect("index.php?view=billing");
		}
	}
?>
?>