<?php
	session_start();	
	require_once("inc/config.inc.php");
	$trending_sale_coupons = GetTrendingSaleCoupons();
	$total_trending_sale_coupons = count($trending_sale_coupons);
	
	// Mobile Detection Library
	require_once 'inc/mobile_detect.php';
	$detect = new Mobile_Detect;	
	
	// Geeting the Dimention and Orientation
	$ScreenWidth = $_GET['deviceWidth'];
	$ScreenOrientation = $_GET['deviceOrientation'];
?>
<?php if( $detect->isMobile() || $detect->isTablet()){ ?>
	<style type="text/css">
			.dealsOfWeekContainer .bx-wrapper .bx-pager {
				padding-top:15px;				
			}
			.prevSlideIcon, .nextSlideIcon {
				top:3px;
			}
	</style>
<?php } ?>

<ul class="topTrends">
	<?php 
	if($total_trending_sale_coupons>0){ 
	
			if( $detect->isMobile() && !$detect->isTablet()){			
				// If Device is in Landscape Mode
				if (isset($ScreenOrientation) && $ScreenOrientation == 'landscape')
				 {
					$number_of_columns = 3;					
				 }				 
				 // If Device is having screen lest that 480
				 elseif ($ScreenWidth < 480)
				 {
					$number_of_columns = 2;					
				 }
				 // If Device is in portrait Mode
				 elseif (isset($ScreenOrientation) && $ScreenOrientation == 'portrait')
				 {
				 	$number_of_columns = 2;
				 }
				 // Default
				 else {
				 	$number_of_columns = 3;
				 }
				 
			}
			
			elseif (isset($ScreenOrientation) && $ScreenOrientation == 'landscape') {
				$number_of_columns = 3;
			}
			
			// If not Mobile Device
			else {
				$number_of_columns = 3;
			}
		$multiple_of_three =$total_trending_sale_coupons+ ($total_trending_sale_coupons%$number_of_columns);
		$total_iterations = floor($multiple_of_three/$number_of_columns);
		$i = 1;
		$start_index = 0;
		//how many blocks we need to create
		for($i = 0; $i<$total_iterations; $i++){
			echo '<li><table>';

			//how many rows we need to create for every block
			for($k=0;$k<4;$k++){
				$next_index = $start_index;
				echo '<tr>';
				if($k==0){
					for($m=0;$m<$number_of_columns;$m++){
						$trending_coupon = array();
						if(array_key_exists($next_index, $trending_sale_coupons)){
							$trending_coupon = $trending_sale_coupons[$next_index];
						}
					?>
						<td <?php if(!$trending_coupon){?>class="empty"<?php } ?>>
							<?php if($trending_coupon){?>
							<div class="InfoContainer">
								<div class="storeTitle">
									<!-- <img alt="" src="<?php echo $trending_coupon['retailer_image']; ?>"/>-->
									<a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $trending_coupon['retailer_id']; ?>&c=<?php echo $trending_coupon['coupon_id']?>" <?php if (isLoggedIn()) echo "target=\"_blank\""; ?>>
										<img src="<?php echo SITE_URL.'admin/upload/retailer/';echo $trending_coupon['image_III'];?>">
									</a>
								</div>
							</div>
							<?php } ?>
						</td>
					<?php
					$next_index++;
					}
				}
				
				
				if($k==1){
					for($m=0;$m<$number_of_columns;$m++){
						$trending_coupon = array();
						if(array_key_exists($next_index, $trending_sale_coupons)){
							$trending_coupon = $trending_sale_coupons[$next_index];
						}
					?>
						<td class="saleInfo <?php if(!$trending_coupon){?>empty<?php } ?>">
							<?php if($trending_coupon){?>
							<div class="InfoContainer">
								<div class="storeText"><?php echo $trending_coupon['description']; ?></div>
							</div>
							<?php } ?>
						</td>
					<?php
					$next_index++;
					}
				}
				
				
				if($k==2){
					for($m=0;$m<$number_of_columns;$m++){
						$trending_coupon = array();
						if(array_key_exists($next_index, $trending_sale_coupons)){
							$trending_coupon = $trending_sale_coupons[$next_index];
						}
					?>
						<td <?php if(!$trending_coupon){?>class="empty"<?php } ?>>
							<?php if($trending_coupon){?>
							<div class="InfoContainer">
								<div class="addition">+</div>
							</div>
							<?php } ?>
						</td>
					<?php
					$next_index++;
					}
				}
				
				
				if($k==3){
					for($m=0;$m<$number_of_columns;$m++){
						$trending_coupon = array();
						if(array_key_exists($next_index, $trending_sale_coupons)){
							$trending_coupon = $trending_sale_coupons[$next_index];
						}
					?>
						<td <?php if(!$trending_coupon){?>class="empty"<?php } ?>>
							<?php if($trending_coupon){?>
							<div class="InfoContainer">
								<div class="cashBack">
									<div class="percentage">
										 <?php														
											$trending_coupon_type = GetCashbackType($trending_coupon['cashback']);
											$cashback = RemoveCashbackType($trending_coupon['cashback']);
										?>
										<?php echo $cashback;?>
										<span class="percentageSymbol"><?php echo $trending_coupon_type;?></span>
								   </div>                                               
									<div class="cashBackCaption">Cash Back</div>
								</div>
							</div>
							<?php } ?>
						</td>
					<?php
					$next_index++;
					}
				}
				echo '</tr>';
			}
			$start_index = $i+$number_of_columns;
			
			?>
		</tr>
	</table>								
	</li>

	<?php	}
	 } ?>
</ul>
<script>
	$(function(){
		var slider = $('.topTrends').bxSlider({
			adaptiveHeight: true,
			auto: false,
			pause: 2000,
			speed: 800,
			responsive: false,
			pager: true, // carasuls
			controls: true,
			onSliderLoad: function(){
				$('.bx-pager').prepend('<span id="pg-prev" class="prevSlideIcon"></span>');
				$('.bx-pager').append('<span id="pg-next" class="nextSlideIcon"></span>');
				$('#pg-next').on('click', function(){			
					slider.goToNextSlide();
				})				
				$('#pg-prev').on('click', function(){
					slider.goToPrevSlide();
				})
			}
		});
	});
</script>