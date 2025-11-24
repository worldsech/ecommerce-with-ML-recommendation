 
<?php require_once ("../../include/initialize.php"); 
   if (!isset($_SESSION['USERID'])){
      redirect(web_root."admin/index.php");
     }


?>

    <?php  
    if (isset($_POST['product_id'])){//add to cart

    // FIX: Use PROID instead of PRODUCTID
    $query = "SELECT * FROM `tblproduct` p, `tblcategory` c 
          WHERE p.`CATEGID`=c.`CATEGID` and PROID='".$_POST['product_id']."'";
      $mydb->setQuery($query);
       $cur = $mydb->loadSingleResult();

       addtocart($cur->PROID,1,$cur->PROPRICE);

    }

                       
                  
 

    ?>
           


<?php
if(isset($_GET['updateid'])){//update cart
    $max=count($_SESSION['fixnmix_cart']);
    for($i=0;$i<$max;$i++){

      $pid=$_SESSION['fixnmix_cart'][$i]['productid'];

      $qty=intval(isset($_GET['QTY'.$pid]) ? $_GET['QTY'.$pid] : "");
       $price=(double)(isset($_GET['subTOT'.$pid]) ? $_GET['subTOT'.$pid] : "");
    // echo '<script> alert('.$price.')  </script>';
     
      if($qty>0 && $qty<=9999){ 

        $_SESSION['fixnmix_cart'][$i]['qty']=$qty;
       $_SESSION['fixnmix_cart'][$i]['price']=$price;
      }
    
    }
}
 ?>
<?php 

if(isset($_GET['id'])) { //remove to cart
  removetocart($_GET['id']); 
  } 
?>


 
<div class="container"> 
      <div class="">
        <div class="panel panel-default">
          <div class="panel-body">  
            <fieldset>  
              <legend><h2 class="text-left">Cart List</h2></legend>
            <!-- <form action="index.php?view=orderdetails" method="post"> -->
              <div class="table-responsive">
               <div  class="fixnmix_scroll_carttwo" >
                  <table  class="table fixnmix-table" id="table" >
                     <thead>
                       <tr>
                          <th width="1%">#</th>
                          <th>Product</th>
                          <th>Description</th>
                          <th>Price</th>
                          <th>Quantity</th>
                          <th>Total</th>
                          <th>Action</th>
                       </tr>
                     </thead>  
                     
                                     
                   <tbody > 
        

                             <?php
                            if (!empty($_SESSION['fixnmix_cart'])){ 
                                  $count_cart = count($_SESSION['fixnmix_cart']);
                                  for ($i=0; $i < $count_cart  ; $i++) { 
                                  // FIX: Use PROID instead of PRODUCTID
                                  $query = "SELECT * FROM `tblproduct` p , `tblcategory` c 
                                    WHERE  p.`CATEGID`=c.`CATEGID` and PROID='".$_SESSION['fixnmix_cart'][$i]['productid']."'";
                                    $mydb->setQuery($query);
                                    $cur = $mydb->loadResultList();
                                    foreach ($cur as $result){ 
                          ?>
                                    <tr>
                                      <td></td>
                                      <td><img src="<?php echo web_root.'admin/products/'.$result->IMAGES; ?>" onload="totalprice()" width="50px" height="50px"></td>
                                      <td><?php echo $result->PRODESC ?></td>
                                      <td> ₦ <?php echo  $result->PROPRICE ?></td>
                                      <td>
                                      <input type="NUMBER" data-id="<?php echo $result->PROID;  ?>" class="qty" name="QTY<?php echo $result->PROID;  ?>" id="QTY<?php echo $result->PROID; ?>"  value="<?php echo $_SESSION['fixnmix_cart'][$i]['qty'] ?>"/>
                                      </td>
                                        <td>
                                         ₦ <output id="Osubtot<?php echo $result->PROID ?>"><?php echo   $_SESSION['fixnmix_cart'][$i]['price']; ?></output>
                                        </td>
                                      <!-- hidden textbox -->
                                      <input type="hidden" name="TOT<?php echo $result->PROID;  ?>" id="TOT<?php echo $result->PROID; ?>"  value="<?php echo $_SESSION['fixnmix_cart'][$i]['price'] ?>"/>
                                      <input type="hidden" name="PRICE<?php echo $result->PROID;  ?>" id="PRICE<?php echo $result->PROID; ?>"  value="<?php echo $result->PROPRICE; ?>"/></td>
                                        <input type="hidden" name="originalqty<?php echo $result->PROID;  ?>" id="originalqty<?php echo $result->PROID; ?>"  value="<?php echo $result->PROQTY;   ?>"/>
                                     <td>   <a href="controller.php?action=cartdelete&id=<?php echo $result->PROID; ?>"  data-id="<?php echo $result->PROID ?>"   class="delete btn btn-danger btn-xs">Remove</a></td>
                                    </tr>
                          <?php
                            }

                          }
                        }
                      ?>
                                       
                            
                    </tbody>
                  </table>
                </div>
              <table>
           <tfoot  >
                    <div ><strong><h1 align="right" >Total Price :  ₦ <span id="sum">0</span></h1></strong></td></div> 

                              
                  </tfoot>
        </table> 
       </div> 
   
                <a href="index.php?view=addorder" class="btn btn-default"><span class="glyphicon glyphicon-arrow-left"></span>&nbsp;<strong>Add Another Products</strong></a>
               <a href="index.php?view=orderdetails" name="proceed" class="btn btn_fixnmix pull-right"   ><strong>Proceed And Checkout</strong> <span class="glyphicon glyphicon-chevron-right"></span></a> 
       </fieldset>
          </div>    
        </div>
      </div>
   
 </div>