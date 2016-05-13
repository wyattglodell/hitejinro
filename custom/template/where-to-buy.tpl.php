<div class="container online-retailers">
	<h2 class="section-title">Online Retailer</h2>
	<a href="<?php echo $online ?>" target="_blank"><?php echo $online ?></a>
</div>
<div class="container">    
    
    <div class="bh-sl-container <?php echo $site ?>">
          <div class="bh-sl-form-container">
            <form id="bh-sl-user-location" method="post" action="#">
                  <label for="bh-sl-address">Enter Address or Zip Code:</label>
                  <input type="text" id="bh-sl-address" name="bh-sl-address" />        
                  <button id="bh-sl-submit" type="submit">Submit</button>
            </form>
          </div>
    
          <div id="map-container" class="bh-sl-map-container">
            <div id="bh-sl-map" class="bh-sl-map"></div>
            
            <div class="bh-sl-loc-list">
                <ul class="list"></ul>
            </div>
          </div>
    </div>
    
    <div class='clear'></div>
</div>

