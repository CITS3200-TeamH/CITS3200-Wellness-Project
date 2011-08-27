<?php
	function genPassword($id,$last) {
		$pw = '';
		$last = trim($last);
		if(strlen($last) >= 4) {
			$pw = $last[0].$last[1].$last[2].$last[3].$id[4].$id[5].$id[6].$id[7];
		}else {
			
			for($i=0;$i<strlen($last);$i++) {
				$pw .= $last[$i];
			}
			$pw .= $id[4].$id[5].$id[6].$id[7];
		}
		return $pw;
	}
?>
