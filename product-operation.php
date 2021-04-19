<?php /* Template Name: Product Operation Page */

get_header(); ?>

<div id="generator-container" class="container">
    <div class="row">

      <div class="col-12 mt-4">
          <h3>This is the product operation page</h3>
      </div>
        <div class="col-12 container">
          <form id="addProduct" name="form" action="<?php echo home_url() . "/product-operation-result";?>" method="post" class='bg-light mt-2 p-3'>
              <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">Product Name<span style="color:red">*</span>: </label>
                  <div class="col-sm-9">
                      <input name="productName" type="text" class="form-control" id="product-name" placeholder='Product Name' required>
                  </div>
              </div>

              <div class="row mt-3">
                  <label class="col-sm-3 col-form-label">Valid Duration<span style="color:red">*</span>: </label>
                  <div class="col-sm-9">
                      <input name="productDuration" value=30 placeholder='Enter the valid duration in days' type="text" class="form-control" id="product-duration" required>
                  </div>
              </div>

              <div class="form-group">
						    <label for="productSupplierSelect">Supplier</label>
						    <select class="form-control" id="productSupplierSelect" name="productSupplierSelect">
						      <option>BID</option>
						      <option>BFC</option>
						      <option>Woolworth</option>
						      <option>Coles</option>
						      <option>Aldi</option>
						    </select>
						  </div>

						  <div class="form-group">
						    <label for="productCategorySelect">Category</label>
						    <select class="form-control" id="productCategorySelect" name="productCategorySelect">
						      <option>Meat</option>
						      <option>Vegetable</option>
						      <option>Frozen</option>
						      <option>Dry Product</option>
						      <option>Sauce</option>
						    </select>
						  </div>

              <div class="row mt-3">
                  <label class="col-sm-3 col-form-label">Storage Temprature: </label>
                  <div class="col-sm-9">
                      <input name="productStorageTemp" placeholder='Please enter the temprature as degree Celsius' type="text" class="form-control" id="product-storage-temp">
                  </div>
              </div>

              <div class="form-group">
						    <label for="productUnitSelect">Product Unit</label>
						    <select class="form-control" id="productUnitSelect">
						      <option>Kg</option>
						      <option>L</option>
						      <option>PC</option>
						      <option>Box</option>
						    </select>
						  </div>

              <div class="row mt-3">
                  <label class="col-sm-3 col-form-label">Existing Amount</label>
                  <div class="col-sm-9">
                      <input name="productExistingAmount" value=0 placeholder="Please enter the initial value for the product" type="text" class="form-control" id="product-existing-amount">
                  </div>
              </div>

              <div id="generate-btn-container" style="text-align: center;" class="mt-5">
                  <input form="addProduct" value="Submit" id="register-btn" name="submit" type="submit" class="btn btn-primary">
              </div>    
              
          </form>
      </div>
    </div>
</div>








<?php get_footer(); ?>