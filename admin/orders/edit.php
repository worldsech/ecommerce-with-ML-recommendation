<?php  
   if (!isset($_SESSION['USERID'])){
      redirect(web_root."admin/index.php");
     }


  $productid = $_GET['id'];
  $product = New Product();
  $singleproduct = $product->single_product($productid);

?>
<!-- NOTE: This file appears to be for editing a PRODUCT, not an order. -->
<!-- It is likely in the wrong directory and should be in admin/products/ -->
 <style type="text/css">
.sidebar-left .main{
  float:right;
}
.idebar-left .sidebar{
  float:left;
}

.sidebar-right .main{
  float:left;
}
.idebar-right .sidebar{
  float:right;
}
/*ala pa taposa..tulog taaht*/
</style>
    
  <script>
     function myFunction() { 
    var x = document.getElementById("image").value;

      image = document.getElementById('pic');
                image.src = x;
}
   
</script> 
    <!--/span-->  
        
       <!-- FIX: The form action must point to the products controller to save edits correctly. -->
       <form class="form-horizontal span6" action="../products/controller.php?action=edit" method="POST" enctype="multipart/form-data" />
 
          <fieldset>
            <legend>New Products</legend> 
                 
             <div class="container">     
                <div class="row">
                  <div class= "main col-xs-9">
                  
                <div class="form-group">
                    <div class="col-md-12">
                      <label class="col-md-3 control-label" for=
                      "PRODUCTNAME">Name:</label>

                      <div class="col-md-8">
                        <input  id="PROID" name="PROID"   type="hidden" value="<?php echo $singleproduct->PROID; ?>" />
                         <input class="form-control input-sm" id="PRODESC" name="PRODESC" placeholder=
                            "Product Name" type="text" value="<?php echo $singleproduct->PRODESC; ?>">
                      </div>
                       
                    </div>
               </div>

                  <!-- NOTE: This PRODUCTTYPE field does not exist in the tblproduct schema -->
                  <div class="form-group">
                    <div class="col-md-12">
                      <label class="col-md-3 control-label" for=
                      "PRODUCTTYPE">Type:</label>

                      <div class="col-md-8">
                         <input class="form-control input-sm" id="PRODUCTTYPE" name="PRODUCTTYPE" placeholder=
                            "Product Type" type="text" value="">
                      </div>
                    </div>
                  </div>

                  <!-- NOTE: This ORIGIN field does not exist in the tblproduct schema -->
                   <div class="form-group">
                    <div class="col-md-12">
                      <label class="col-md-3 control-label" for=
                      "ORIGIN">Origin:</label>

                      <div class="col-md-8">
                       <select class="form-control input-sm" name="ORIGIN" id="ORIGIN">
                          <option value="None">Select Origin (Not in DB)</option>
                          <?php

                          // $origin = New Origin();
                          // $singleorigin = $origin->single_origin($singleproduct->ORIGINID);
                          // echo  '<option SELECTED value='.$singleorigin->ORIGINID.' >'.$singleorigin->ORIGIN.'</option>';
                          
                          // $mydb->setQuery("SELECT * FROM `tblorigin` where ORIGINID <> '".$singleorigin->ORIGINID."'");
                          // $cur = $mydb->loadResultList();

                        // foreach ($cur as $result) {
                        //   echo  '<option  value='.$result->ORIGINID.' >'.$result->ORIGIN.'</option>';
                        //   }
                          ?>
          
                        </select> 
                      </div>
                    </div>
                  </div> 
                  <div class="form-group">
                    <div class="col-md-12">
                      <label class="col-md-3 control-label" for=
                      "CATEGORY">Category:</label>

                      <div class="col-md-8">
                       <select class="form-control input-sm" name="CATEGORY" id="CATEGORY">
                          <option value="None">Select Category</option>
                          <?php
                            //Statement

                          $category = new Category();
                          $singleocategory = $category->single_category($singleproduct->CATEGID);
                          echo  '<option SELECTED value='.$singleocategory->CATEGID.' >'.$singleocategory->CATEGORIES.'</option>';


                          $mydb->setQuery("SELECT * FROM `tblcategory` where CATEGID <> '".$singleocategory->CATEGID."'");
                          $cur = $mydb->loadResultList();
                        foreach ($cur as $result) {
                          echo  '<option  value='.$result->CATEGID.' >'.$result->CATEGORIES.'</option>';
                          }
                          ?>
          
                        </select> 
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-md-12">
                      <label class="col-md-3 control-label" for=
                      "QTY">Quantity:</label>
                      
                      <div class="col-md-8">
                         <input class="form-control input-sm" id="PROQTY" name="PROQTY" placeholder=
                            "Quantity" type="text" value="<?php echo $singleproduct->PROQTY; ?>">
                      </div>
                    </div>
                  </div>
 
                  <div class="form-group">
                    <div class="col-md-12">
                      <label class="col-md-3 control-label" for=
                      "PRICE">Price:</label>

                      <div class="col-md-8">
                         <input class="form-control input-sm" id="PROPRICE" name="PROPRICE" placeholder=
                            "Price" type="TEXT" value="<?php echo $singleproduct->PROPRICE; ?>">
                      </div>
                    </div>
                  </div>

                   <div class="form-group"> 
                    <div class="col-md-12"> 
                      <label class="col-md-3 control-label" for=
                      "INGREDIENTS">Ingredients/Description:</label>

                      <div class="col-md-8">
                         <textarea class="form-control input-sm" id="INGREDIENTS" name="INGREDIENTS" placeholder=
                            "Ingredients or Description" type="text"><?php echo $singleproduct->INGREDIENTS; ?></textarea>
                      </div>
                    </div>
                  </div>
            
             <div class="form-group">
                    <div class="col-md-12">
                      <label class="col-md-3 control-label" for=
                      "idno"></label>

                      <div class="col-md-8">
                        <button class="btn btn-btn_fixnmix" name="save" type="submit" >Save</button>
                      </div>
                    </div>
                  </div>
                  </div>
            

                 <div class="sidebar col-xs-3">
                    <div class="form-group">
                    <div class="col-md-12">
                      <div class="col-md-8">
                         <img name="pic" id="pic" src="<?php echo web_root.'admin/products/'.$singleproduct->IMAGES; ?>" width="215" height="300" title="" />
                      </div>
                    </div>
                  </div>
                   <div class="form-group">
                    <div class="col-md-12">
                      <div class="col-md-8">
                          <input type="file" name="image" id="image"  onchange="myFunction()" /> 
                      </div>
                    </div>
                  </div>
                    
                </div>
            </div>
        </div><!--End of container-->            
   
  
       
    <footer>
        <p>© Company janno</p>
    </footer>
</div>
<!--/.fluid-container-->
 </fieldset> 
 </form>