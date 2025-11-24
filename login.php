 <?php
 require_once ("include/initialize.php"); 
 if (@$_GET['page'] <= 2 or @$_GET['page'] > 5) {
  # code...
    // unset($_SESSION['PRODUCTID']);
    // // unset($_SESSION['QTY']);
    // // unset($_SESSION['TOTAL']);
} 


 
if(isset($_POST['sidebarLogin'])){
  $email = trim($_POST['U_USERNAME']);
  $upass  = trim($_POST['U_PASS']);
  
   if ($email == '' OR $upass == '') {

      message("Invalid Username and Password!", "error");
      redirect(web_root."index.php");
         
    } else {   
        $cus = new Customer();
        $cusres = $cus::cusAuthentication($email,$upass);

        if ($cusres==true){


           redirect(web_root."index.php?q=profile");
        }else{
             message("Invalid Username and Password! Please contact administrator", "error");
             redirect(web_root."index.php");
        }
 
 }
}



 if(isset($_POST['modalLogin'])){
  $email = trim($_POST['U_USERNAME']);
  $upass  = trim($_POST['U_PASS']);
  
   if ($email == '' OR $upass == '') { 
      message("Invalid Username and Password!", "error");
       redirect(web_root."index.php?page=6");
         
    } else {   
        $cus = new Customer();
        $cusres = $cus::cusAuthentication($email,$upass);

        if ($cusres==true){

           if($_POST['proid']==''){
            redirect(web_root."index.php?q=orderdetails");
           }else{
              $proid = $_POST['proid'];
              $query ="INSERT INTO `tblwishlist` (`PROID`, `CUSID`, `WISHDATE`, `WISHSTATS`)  VALUES ('". $proid."','".$_SESSION['CUSID']."','".DATE('Y-m-d')."',0)";
              $mydb->setQuery($query);
              $mydb->executeQuery();
              redirect(web_root."index.php?q=profile");
             }

         
        }else{
             message("Invalid Username and Password! Please contact administrator", "error");
             redirect(web_root."index.php");
        }
 
 }
 }
 ?> 
 

 