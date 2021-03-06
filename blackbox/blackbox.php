<?php
session_start()
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Black Box - Rion Brattig Correia</title>

	<link href="../css/bootstrap.min.css" rel="stylesheet">

	<style>		
		table#system tr td.gru { background-color:#000000; }
		table#system tr td.pty { background-color:#808080; }
		table#system tr td.jfk { background-color:#0000FF; }
		table#system tr td.lax { background-color:#FF00FF; }
		table#system tr td.las { background-color:#00FFFF; }
		table#system tr td.lis { background-color:#008000; }
		table#system tr td.bru { background-color:#FF0000; }
		table#system tr td.hkg { background-color:#FFA500; }
		table#system tr td.icn { background-color:#FFC0CB; }
		table#system tr td.mex { background-color:#FFFFFF; }
		.label-lg { font-size: 30px; }
	</style>


</head>
<body>
	
	<div class="container">

	<h1>The Black Box</h1>
	
	<div class="alert alert-warning">
		<p>
			The box is a 20 by 20 matrix of cells. In the input box you can enter the number of cycles you want the <strong>system</strong> to run each time.<br/>
		</p>
		<p>
			Study the <strong>behavior</strong> of the system. Propose a <strong>model</strong> of what it does.
		</p>
	</div>

<!--
BLACK BOX CODE STARTS HERE
-->
<?php
$ncol = 20;
$nrow = 20;
$debug = False;
/*
if (isset($_GET['cycles'])) {
	$cycles = $_GET['cycles'];
} else {
	$cycles = 1;
}
if (isset($_GET['cycles'])) {
	$step = $_SESSION['step'];
} else {
	$step = 1;
}
*/

/*
// Reset Blackbox
*/
if ($_GET['reset'] == 1) {
	unset($_SESSION['blackbox']);
	unset($_SESSION['blackbox_saved']);
	unset($_SESSION['step']);
	unset($_SESSION['step_saved']);
}

/*
// If Blackbox is not set, Init Blackbox
*/
if (!isset($_SESSION['blackbox'])) {
	//
	$step = 1;
	$cycles = 0;
	//
	$state = array();
	// Initiate Array with Random Numbers
	for ($i=0; $i<$nrow; $i++) {	
		$state[$i] = array();
		for ($j=0; $j<$ncol; $j++) {
			if ($debug) {
				$state[$i][$j] = 1; # Initiate with Zero
			} else {
				$state[$i][$j] = rand(0,9); # Initiate with Random	
			
				/* Quadrant 2 Random Init */
				/* 6 center squares (for each row) be initialized to either 0 or 9 */
				if ( ( ($i>=0) && ($i<= 9) ) && ( ($j >= 12) && ($j <= 17) ) ) {
					$state[$i][$j] = ($state[$i][$j] > 4) ? 9 : 0;
				}
		
			}			
		
		}
	}
// If blackbox is set, but needs to be reverted
} else if (isset($_GET['revert'])) {
	$revert = True;
	$state = $_SESSION['blackbox_saved'];
	$state_saved = $_SESSION['blackbox'];
	$step = $_SESSION['step_saved'];
	$step_saved = $_SESSION['step'];
// 
} else if ($_GET['cycles']) {
	$state = $_SESSION['blackbox'];
	$state_saved = $state;
	$step = $_SESSION['step'];
	$step_saved = $step;
	//
	$cycles = $_GET['cycles'];
} else {
	// Nothing to perform. Just print the Table
	$state = $_SESSION['blackbox'];
	$state_saved = $_SESSION['blackbox_saved'];
	$step = $_SESSION['step'];
	$step_saved = $_SESSION['step_saved'];
}

/*
// The Number Of Cycles to Perform
*/
if (!$revert) {
	for ($cycle=0; $cycle<$cycles; $cycle++) {
		$step += 1;	
		/*
		// 1 Quadrant - Majority Rule (top left)
		*/
		// x = from 0 to ($nrow)/2
		// y = from 0 to ($ncol)/2
		$x = rand(0, ($ncol/2-1) );
		$y = rand(0, ($nrow/2-1) );
		$maj_count = array_fill(0,9,0);

		$xp1 = $x + 1;
		$xm1 = $x - 1;
		$yp1 = $y + 1;
		$ym1 = $y - 1;
		if($x==0) { $xm1 = $ncol-1; }
		#if($x==$nrow/2) { $xp1 = 0; }
		if($y==0) { $ym1 = $nrow-1; }
		#if($y==$ncol/2) { $yp1 = 0; }

		$maj_count[ $state[$y][$x] ]++;
		$maj_count[ $state[$ym1][$x] ]++;
		$maj_count[ $state[$ym1][$xp1] ]++;
		$maj_count[ $state[$y][$xp1] ]++;
		$maj_count[ $state[$yp1][$xp1] ]++;
		$maj_count[ $state[$yp1][$x] ]++;
		$maj_count[ $state[$yp1][$xm1] ]++;
		$maj_count[ $state[$y][$xm1] ]++;
		$maj_count[ $state[$ym1][$xm1] ]++;

		#print '='.var_dump($maj_count);
		$maj = 0;
		$new_state = $state[$y][$x];
		foreach ($maj_count as $key => $value) {
			if ($value > $maj){
				$maj = $value;
				$new_state = $key;
			}
		}

		$state[$y][$x] = $new_state;

		/*
		// 2 Quadrant - (top right)
		// Boolean Network imbedded in Ashby's box
		// Note: 0=OFF 9=ON
		*/

		$x1 = rand(10, 19);
		$y1 = rand(0, 9);
		$x2 = rand(10, 19);
		$y2 = rand(0, 9);

		#print 'x1:'.$x1.' | y1:'.$y1.'<br/>';
		#print 'x2:'.$x2.' | y2:'.$y2.'<br/>';

		$n0 = ($state[$y1][11] > 4) ? True : False; 
		$n1 = ($state[$y1][12] > 4) ? True : False;
		$n2 = ($state[$y1][13] > 4) ? True : False;
		$n3 = ($state[$y1][14] > 4) ? True : False;
		$n4 = ($state[$y1][15] > 4) ? True : False;
		$n5 = ($state[$y1][16] > 4) ? True : False;
		$n6 = ($state[$y1][17] > 4) ? True : False;
		$n7 = ($state[$y1+1][15]>4) ? True : False; //n4 for the row below

		#print 'n0:'.$n0.' | '.'n1:'.$n1.' | '.'n2:'.$n2.' | '.'n3:'.$n3.' | '.'n4:'.$n4.' | '.'n5:'.$n5.' | '.'n6:'.$n6.' | '.'n7:'.$n7.' | <br/>';

		/*Treat first two nodes in the row & the last node normally (Ashby's Box)*/
		if ( ($x1 < 12) || ($x1 == 19) ) {
			#print 'First 2 Nodes or Last One<br/>';
			$state[$y1][$x1] = ($state[$y1][$x1] * $state[$y2][$x2]) % 10;
		}

		/* The 18th node in the row is a modified Ashby's - always uses n6 */
		if ($x1 == 18) {
			#print '18th Node<br/>';
			$state[$y1][$x1] = ($state[$y1][$x1] * $state[$y1][17]) % 10;
		}
	
		/* Update node 1: NAND */
		if ($x1 == 12) {
			#print 'Node 1<br/>';
			if ($n0 && $n2) {
			
				$state[$y1][$x1] = 0;
			} else {
				$state[$y1][$x1] = 9;
			}
		}
	
		/* Update node 2: AND  */
		if ($x1 == 13) {
			#print 'Node 2<br/>';
			if ($n3 && $n2) {
				$state[$y1][$x1] = 9;
			} else {
				$state[$y1][$x1] = 0;
			}
		}
	
		/*Update node 3: OR  */
		if ($x1 == 14) {
			#print 'Node 3<br/>';
			if ($n1 || $n4) {
				$state[$y1][$x1] = 9;
			} else { 
				$state[$y1][$x1] = 0;
			}
		}
		 
		/*Update node 4: OR  */
		if ($x1 == 15) {
			#print 'Node 4<br/>';
			if ($n2 || $n5) {
				$state[$y1][$x1] = 9;
			} else {
				$state[$y1][$x1] = 0; 
			}
		}
		
		/*Update node 5: AND  */
		if ($x1 == 16) {
			#print 'Node 5<br/>';
			if ($n3 && $n6) {
				$state[$y1][$x1] = 9;
			} else {
				$state[$y1][$x1] = 0;
			}
		}
	
		/*Update node 6: OR  */
		if ($x1 == 17) {
			#print 'Node 6<br/>';
			if ($y1 == 9) { /*wrapping up to the top row from the bottom*/
				if (($state[0][15] > 4) || $n4) {
					$state[$y1][$x1] = 9;
				} else { 
					$state[$y1][$x1] = 0;
				}
			} else {
				if ($n7 || $n4) {
					$state[$y1][$x1] = 9;
				} else {
					$state[$y1][$x1] = 0; 
				}
			}
		}
		
		/*
		// 3 Quadrant (bottom right)
		// //Random State
		// Hybrid of Quadrant 1 and 4
		*/
		/*
		$x = rand(0, ($nrow/2-1) );
		$y = rand( ($ncol/2), ($ncol-1) );

		//print '3QD = x:'.$x.' y:'.$y.'<br/>';
		$state[$y][$x] = rand(0,9);
		*/
		
		$x = rand(0, ($ncol/2-1) );
		$y = rand( ($nrow/2), ($nrow-1) );
		$maj_count = array_fill(0,9,0);

		$xp1 = $x + 1;
		$xm1 = $x - 1;
		$yp1 = $y + 1;
		$ym1 = $y - 1;
		if($x==0) { $xm1 = $ncol/2-1; }
		if($x==$ncol/2-1) { $xp1 = 0; }
		if($y==$nrow/2) { $ym1 = $nrow-1; }
		if($y==$ncol-1) { $yp1 = $nrow/2; }


		$maj_count[ $state[$y][$x] ]++;
		$maj_count[ $state[$ym1][$x] ]++;
		$maj_count[ $state[$ym1][$xp1] ]++;
		$maj_count[ $state[$y][$xp1] ]++;
		$maj_count[ $state[$yp1][$xp1] ]++;
		$maj_count[ $state[$yp1][$x] ]++;
		$maj_count[ $state[$yp1][$xm1] ]++;
		$maj_count[ $state[$y][$xm1] ]++;
		$maj_count[ $state[$ym1][$xm1] ]++;

		#print '='.var_dump($maj_count);
		$maj = 0;
		$new_state = $state[$y][$x];
		foreach ($maj_count as $key => $value) {
			if ($value > $maj){
				$maj = $value;
				$new_state = $key;
			}
		}
		
		$state[$y][$x] = ($state[$y][$x] * $new_state) % 10;
		
		/*
		// 4 Quadrant (bottom right)
		// (xy mod 10)
		*/

		$x1 = rand( ($ncol/2), ($ncol-1) );
		$y1 = rand( ($nrow/2), ($nrow-1) );
		$x2 = rand( ($ncol/2), ($ncol-1) );
		$y2 = rand( ($nrow/2), ($nrow-1) );

		//print '4QD = x1:'.$x1.' y1:'.$y1.'<br/>';
		//print '4QD = x2:'.$x2.' y2:'.$y2.'<br/>';
		$state[$y1][$x1] = ($state[$y1][$x1] * $state[$y2][$x2]) % 10;
	} //end cycles
} //end !revert
/*
// Save State to SESSION
*/
$_SESSION['blackbox'] = $state;
$_SESSION['blackbox_saved'] = $state_saved;
$_SESSION['step'] = $step;
$_SESSION['step_saved'] = $step_saved;

?>

<?php
/*
// Colors
*/
$c = array();
$c[0] = 'gru';
$c[1] = 'pty';
$c[2] = 'jfk';
$c[3] = 'lax';
$c[4] = 'las';
$c[5] = 'lis';
$c[6] = 'bru';
$c[7] = 'hkg';
$c[8] = 'icn';
$c[9] = 'mex';
?>

<div class="container">
	<!-- Table -->
	<div class="col-md-7">
		<div class="panel panel-default">

<?php
/*
// Print Table
*/
print "\t\t<table id='system' class='table table table-bordered' width='620' height='620'>\n";
for ($i=0; $i<$nrow; $i++) {	
	print "\t\t\t<tr>\n";
	for ($j=0; $j<$ncol; $j++) {
		$classes = $c[$state[$i][$j]];
		print "\t\t\t\t<td class='".$classes."' width='31' height='31'>";
		//print $state[$i][$j];
		print "</td>\n";
	}
	print "\t\t\t</tr>\n";
}
print "\t\t</table>\n";
//
if (isset($_GET['cycles_input'])) {
	$cycles = $_GET['cycles_input'];
} else if (!isset($cycles)) {
	$cycles = 1;
}
?>
		</div>
	</div>
	
	<!-- Controls -->
	<div class="col-md-5">
		<br><br>
		
		<div class="container">
			<div class="col-sm-6">
				<h3>Current cycle: <span class="label label-primary label-lg"><?=$step?></span></h3>
			</div>
		</div>
		
		<br><br>
		<hr>

		<form class="form-horizontal" action="<?php print $_SERVER['PHP_SELF']; ?>" method="get">
			<div class="form-group form-group-lg">
				<label class="col-sm-4 control-label" for="cycles">Cycles to run:</label>
				<div class="col-sm-6">
					<input type="text" id="cycles" class="form-control form-lg" name="cycles" value="<?=$cycles?>"/>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-4 col-sm-8">
					<button type="submit" class="btn btn-success btn-lg"">Go!</button>
				</div>
			</div>
		</form>
		
		<br>
		<hr>
		
		<form class="form-horizontal" action="<?php print $_SERVER['PHP_SELF']; ?>" method="get">
			<input type="hidden" name="revert" value="1" />
			<input type="hidden" name="cycles_input" value="<?php print $cycles; ?>" />
			<div class="form-group form-group-lg">
				<label class="col-sm-4 control-label" for="cycles">Previous state:</label>
				<div class="col-sm-8">
					<button type="submit" class="btn btn-warning btn-lg"">Revert!</button>
				</div>
			</div>
		</form>
		
		<form class="form-horizontal" action="<?php print $_SERVER['PHP_SELF']; ?>" method="get">
			<input type="hidden" name="reset" value="1" />
			<input type="hidden" name="cycles_input" value="<?php print $cycles; ?>" />
			<div class="form-group form-group-lg">
				<label class="col-sm-4 control-label" for="cycles">Reset system:</label>
				<div class="col-sm-8">
					<button type="submit" class="btn btn-danger btn-lg"">Reset!</button>
				</div>
			</div>
		</form>
		
	</div>		
</div>
	
<?php

/*
// Print Array of Numbers to D3
*/
/*
print 'state = [';
for ($i=0; $i<$nrow; $i++) {	
	print '[';
	for ($j=0; $j<$ncol; $j++) {
		print $state[$i][$j];
		if ($j < $ncol-1) {
			print ',';
		}
	}
	print ']';
	if ($i < $nrow-1) {
		print ',';
	}
}
print '];';
*/

?>

<!--
BLACKBOX CODE ENDS HERE
-->

	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="../js/bootstrap.min.js"></script>
</body>
</html>
