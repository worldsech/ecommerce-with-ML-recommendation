<section id="slider"><!--slider-->
    <div class="container">
      <div class="row">
        <div class="col-sm-12">
          <div id="slider-carousel" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
              <li data-target="#slider-carousel" data-slide-to="0" class="active"></li>
              <li data-target="#slider-carousel" data-slide-to="1"></li>
              <li data-target="#slider-carousel" data-slide-to="2"></li>
            </ol>
            
            <div class="carousel-inner">
              <div class="item active">
                <div class="col-sm-6">
                  <h1><span>YOUR</span>-SHOP</h1>
                  <h2>Your Awesome E-Commerce Site</h2>
                  <p>A great place to find all the products you need. </p>
                 
                </div>
                <div class="col-sm-6">
                  <img src="images/home/girl1.jpg" class="girl img-responsive" alt="Showcase Image 1" />
                  <!-- <img src="images/home/pricing.png"  class="pricing" alt="" /> -->
                </div>
              </div>
              <div class="item">
                <div class="col-sm-6">
                  <h1><span>YOUR</span>-SHOP</h1>
                  <h2>Amazing Products</h2>
                  <p>We have a wide variety of products to choose from. </p>
                 
                </div>
                <div class="col-sm-6">
                  <img src="images/home/girl2.jpg" class="girl img-responsive" alt="Showcase Image 2" />
                  <!-- <img src="images/home/pricing.png"  class="pricing" alt="" /> -->
                </div>
              </div>
              
              <div class="item">
                <div class="col-sm-6">
                  <h1><span>YOUR</span>-SHOP</h1>
                  <h2>Fast Shipping</h2>
                  <p>Get your products delivered to your doorstep in no time. </p>
                 
                </div>
                <div class="col-sm-6">
                  <img src="images/home/girl3.jpg" class="girl img-responsive" alt="Showcase Image 3" />
                  <!-- <img src="images/home/pricing.png" class="pricing" alt="" /> -->
                </div>
              </div>
              
            </div>
            
            <a href="#slider-carousel" class="left control-carousel hidden-xs" data-slide="prev">
              <i class="fa fa-angle-left"></i>
            </a>
            <a href="#slider-carousel" class="right control-carousel hidden-xs" data-slide="next">
              <i class="fa fa-angle-right"></i>
            </a>
          </div>
          
        </div>
      </div>
    </div>
  </section><!--/slider-->

  <section>
    <?php
    /**
     * Renders a product card.
     *
     * @param object $result The product data object.
     */
    function render_product_card($result) {
    ?>
      <form method="POST" action="cart/controller.php?action=add">
        <input type="hidden" name="PROPRICE" value="<?php echo $result->PROPRICE; ?>">
        <input type="hidden" id="PROQTY" name="PROQTY" value="<?php echo $result->PROQTY; ?>">
        <input type="hidden" name="PROID" value="<?php echo $result->PROID; ?>">
        <div class="col-sm-4">
          <div class="product-image-wrapper">
            <div class="single-products">
              <div class="productinfo text-center">
                <img src="<?php echo web_root.'admin/products/'. $result->IMAGES; ?>" alt="" />
                <h2> ₦ <?php echo $result->PRODISPRICE; ?></h2>
                <p><?php echo $result->PRODESC; ?></p>
                <button type="submit" name="btnorder" class="btn btn-default add-to-cart"><i class="fa fa-shopping-cart"></i>Add to cart</button>
              </div>
            </div>
            <div class="choose">
              <ul class="nav nav-pills nav-justified">
                <li>
                  <?php if (isset($_SESSION['CUSID'])) { ?>
                    <a href="<?php echo web_root . 'customer/controller.php?action=addwish&proid=' . $result->PROID; ?>" title="Add to wishlist"><i class="fa fa-plus-square"></i>Add to wishlist</a>
                  <?php } else { ?>
                    <a href="#" title="Add to wishlist" class="proid" data-target="#smyModal" data-toggle="modal" data-id="<?php echo $result->PROID; ?>"><i class="fa fa-plus-square"></i>Add to wishlist</a>
                  <?php } ?>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </form>
    <?php
    }
    ?>
    <div class="container">
      <div class="row">
        <div class="col-sm-3">
            <?php include 'sidebar.php'; ?>
        </div>
        
        <div class="col-sm-9 padding-right">
          <div class="features_items"><!--features_items-->
            <h2 class="title text-center">Features Items</h2>

            <?php

            $query = "SELECT * FROM `tblpromopro` pr , `tblproduct` p , `tblcategory` c
            WHERE pr.`PROID`=p.`PROID` AND  p.`CATEGID` = c.`CATEGID`  AND PROQTY>0 ";
            $mydb->setQuery($query);
            $cur = $mydb->loadResultList();
           
            foreach ($cur as $result) { 
              render_product_card($result);
            } ?>
            
          </div><!--features_items--> 
          
          <div class="recommended_items"><!--recommended_items-->
            <h2 class="title text-center">recommended items</h2>
            
           <?php
            // We'll fetch up to 6 recommended items in a single query.
            $rec_query = "";
            if (isset($_SESSION['CUSID'])) {
                // Logged-in users get personalized recommendations
                $rec_query = "SELECT p.*, pr.*, c.* FROM `tblproduct_recommendations` r
                              JOIN `tblproduct` p ON r.RECOMMENDED_PROID = p.PROID
                              JOIN `tblpromopro` pr ON pr.PROID = p.PROID
                              JOIN `tblcategory` c ON c.CATEGID = p.CATEGID
                              WHERE r.CUSID = {$_SESSION['CUSID']} AND p.PROQTY > 0
                              ORDER BY r.SCORE DESC
                              LIMIT 6";
            } else {
                // Guests get the most popular items based on views (requires Phase 1 to be implemented)
                $rec_query = "SELECT p.*, pr.*, c.*, COUNT(v.PROID) as view_count FROM `tblproduct` p
                              JOIN `tblpromopro` pr ON pr.PROID = p.PROID
                              JOIN `tblcategory` c ON c.CATEGID = p.CATEGID
                              LEFT JOIN `tblproductviews` v ON v.PROID = p.PROID
                              WHERE p.PROQTY > 0
                              GROUP BY p.PROID
                              ORDER BY view_count DESC
                              LIMIT 6";
            }

            $mydb->setQuery($rec_query);
            $cur = $mydb->loadResultList();

            // Only show the carousel if we have recommendations
            if ($cur) {
                // Split the results for the two carousel slides
                $carousel_item1 = array_slice($cur, 0, 3);
                $carousel_item2 = array_slice($cur, 3, 3);
            ?>
            
            <div id="recommended-item-carousel" class="carousel slide" data-ride="carousel">
              <div class="carousel-inner">
                
                <div class="item active"> 
                    <?php foreach ($carousel_item1 as $result) {
                        render_product_card($result);
                    } ?>
                </div>

                <?php if (!empty($carousel_item2)) { ?>
                <div class="item">  
                    <?php foreach ($carousel_item2 as $result) {
                        render_product_card($result);
                    } ?>
                </div>
                <?php } ?>

              </div>
               <a class="left recommended-item-control" href="#recommended-item-carousel" data-slide="prev">
                <i class="fa fa-angle-left"></i>
                </a>
                <a class="right recommended-item-control" href="#recommended-item-carousel" data-slide="next">
                <i class="fa fa-angle-right"></i>
                </a>      
            </div>
            
            <?php } // End of if ($cur) ?>

          </div><!--/recommended_items-->
          
        </div>
      </div> 
          
        </div>
      </div>
    </div>
  </section>