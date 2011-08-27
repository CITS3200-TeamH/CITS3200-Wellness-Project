<?php
	include 'layout.php';
	check_student();
	draw_headerin();
	draw_navin();
?>

<div id="main" style="padding-top:0px;">
	<div id="content_wide" style="padding-top:0px;">
		<div style="width:auto; height:auto; margin:0px; min-height:300px; padding:20px; background-color:rgb(245,245,245); border:solid 1px rgb(210,210,210);">
			<div style="width:auto; margin:0px; height:auto; padding:20px; font-size:10pt; min-height:200px; background-color:white; border:1px solid rgb(210,210,210); -moz-border-radius:10px; color:rgb(60,60,60); -webkit-border-radius:10px;">
				<p style="margin-top:0px;">
					<span style="color:#0864A5; font-size:14pt;">eDairy (<?php echo date('Y'); ?>)</span><br/><br/>
					Beta Release v1.0<br/>
					Launched: Mar 09, 2011.<br/><br/>
					<b>Development Team:</b><br/>
					Nat Benjanuvatra - Project Administrator (<a href="mailto:nat.benjanuvatra@uwa.edu.au" class="links">nat.benjanuvatra@uwa.edu.au<a/>)<br/>
					Jake Dallimore - Lead Developer<br/><br/>
					<span style="font-size:7pt;">This software has been developed for use as a learning and research tool for the School of Sports Science, Exercise & Health at the University of Western Australia.<br/>
					Further distribution or use by anyone other than the indicated party, unless otherwise specifically provided for, is prohibited without the approval of the development team.</span>					
				</p>
			</div>
		</div>
	</div>
</div>
<?php draw_footer(); ?>
