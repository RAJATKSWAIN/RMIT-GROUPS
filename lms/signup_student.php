<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

<?php
$conn = mysqli_connect("sql100.infinityfree.com", "if0_40697103", "rmitgroups123", "if0_40697103_lms");
if ($conn) {
    echo "✅ Connected successfully!";
} else {
    echo "❌ Connection failed: " . mysqli_connect_error();
}
 
?>
  <body id="login">
    <div class="container">
	<div class="row-fluid">
	<div class="span6">
		<div class="title_index">
				<?php include('title_index.php'); ?>
		</div>
	</div>
	<div class="span6">
		<div class="pull-right">
				<?php include('student_signin_form.php'); ?>
		</div>
	</div>
    </div>
	<div class="row-fluid">
		<div class="span12">
			<div class="index-footer">
				<?php include('link.php'); ?>
			</div>
		</div>
	</div>
		   <!-- /container -->
		<?php include('footer.php'); ?>
    </div> <!-- /container -->
<?php include('script.php'); ?>
  </body>
</html>