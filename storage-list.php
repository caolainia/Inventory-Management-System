<?php /* Template name: Jose Storage List Page */

get_header(); ?>




<div class="jose-search-group container">
	<div class="row">
		<div class="input-group mb-3 col-6">
		  <input type="text" class="form-control" placeholder="Product Name" aria-label="Product Name" aria-describedby="basic-addon1">
		  <div class="input-group-append">
		    <button class="btn btn-outline-secondary" type="button">Search</button>
		  </div>
		</div>

		<div class="input-group mb-3 col-6">
		  <input type="text" class="form-control" placeholder="Recorder Name" aria-label="Recorder Name" aria-describedby="basic-addon2">
		  <div class="input-group-append">
		    <button class="btn btn-outline-secondary" type="button">Search</button>
		  </div>
		</div>
	</div>
</div>

<table class="table">
  <tr>
    <th>Product Name</th>
    <th>Import Amount</th> 
    <th>Unit</th>
    <th>Import Date (DD/MM/YYYY)</th>
    <th>Import Time (HH:MM:SS) </th> 
    <th>Recorder Name</th>
  </tr>
  <?php 
  for ($i=0; $i<8; $i++): ?>
	  <tr>
	    <td>Duck</td>
	    <td>100</td>
	    <td>PC</td>
	    <td>15/04/2021</td>
	    <td>10:46:30</td>
	    <td>Jose Yu</td>
	  </tr>
  <?php endfor; ?>
</table>

<?php get_footer(); ?>