<?php
   if (!isset($_SESSION['USERID'])){
      redirect(web_root."admin/index.php");
     }

?> 
        <form class="form-horizontal span6" action="" method="POST"  />
          <fieldset>
            <legend>Add New Order</legend> 
         
          <table class="table table-hover" id="fixnmix">
              <thead>
                <tr >
                  <th>No.#</th>
                  <th>Product</th>
                  <th>Description</th>
                  <th align="center">Quantity</th>
                  <th align="center">Price</th>
                  <th></th>
                </tr>
              </thead> 
              <tbody>
                <?php  
                  $query = "SELECT * FROM `tblproduct` p, `tblcategory` c 
                        WHERE p.`CATEGID`=c.`CATEGID` and PROSTATS='Available'";
                    $mydb->setQuery($query);
                    $cur = $mydb->loadResultList();

                  foreach ($cur as $result) {
                    echo '<tr>';
                    echo '<td width="5%" align="center"></td>';
                    echo '<td> <a href=""><img src="'.web_root.'admin/products/'. $result->IMAGES.'" width="60" height="60" title="'.$result->PRODESC.'"/></a></td>';
                    echo '<td >' . $result->PRODESC.'</td>';
                    echo '<td align="center">'. $result->PROQTY.'</td>';
                    echo '<td align="center">  ₦ '. $result->PROPRICE.'</td>';
                      echo '<td align="left">';
                    echo '<a href="controller.php?action=cartadd&id='.$result->PROID.'&price='.$result->PROPRICE.'" data-id="'.$result->PROID.'" class="btn btn_fixnmix btn-xs"><strong>Add to Cart</strong></a></td>';
                    //  echo '<td align="left">
                    // <a href="" data-target="#CART" data-toggle="modal"  data-id="'.$result->PRODUCTID.'" class="btn btn_fixnmix btn-xs MAINorder"><strong>Add to Cart</strong></a></td>';
                    echo '</tr>';
                  } 
                  ?>
              </tbody>            
            </table>
            </div><br/>
           </form>
       