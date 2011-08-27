<?php
	function getWHRating($age,$gender,$val) {
		if($gender == "" || $val == 'NULL' || $age == "") {
			$risk = NULL;
			return;
		}
			//check 
		if($gender == "M") {
			if($age >= 15 && $age <= 29) {
				if($val < 0.83) {
					$risk = "Low";
				}else if($val <= 0.88) {
					$risk = "Moderate";
				}else if($val <= 0.94) {
					$risk = "High";
				}else{
					$risk = "Very High";
				}
			}else if($age >= 30 && $age <= 39) {
				if($val < 0.84) {
					$risk = "Low";
				}else if($val <= 0.91) {
					$risk = "Moderate";
				}else if($val <= 0.96) {
					$risk = "High";
				}else{
					$risk = "Very High";
				}
			}else if($age >= 40 && $age <= 49) {
				if($val < 0.88) {
					$risk = "Low";
				}else if($val <= 0.95) {
					$risk = "Moderate";
				}else if($val <= 1) {
					$risk = "High";
				}else{
					$risk = "Very High";
				}
			}else if($age >= 50 && $age <= 59) {
				if($val < 0.9) {
					$risk = "Low";
				}else if($val <= 0.96) {
					$risk = "Moderate";
				}else if($val <= 1.02) {
					$risk = "High";
				}else{
					$risk = "Very High";
				}
			}else if($age >= 60 && $age <= 69) {
				if($val < 0.91) {
					$risk = "Low";
				}else if($val <= 0.98) {
					$risk = "Moderate";
				}else if($val <= 1.03) {
					$risk = "High";
				}else{
					$risk = "Very High";
				}
			}else {
				$risk = NULL;
			}
		}else {
			if($age >= 15 && $age <= 29) {
				if($val < 0.71) {
					$risk = "Low";
				}else if($val <= 0.77) {
					$risk = "Moderate";
				}else if($val <= 0.82) {
					$risk = "High";
				}else{
					$risk = "Very High";
				}
			}else if($age >= 30 && $age <= 39) {
				if($val < 0.72) {
					$risk = "Low";
				}else if($val <= 0.78) {
					$risk = "Moderate";
				}else if($val <= 0.84) {
					$risk = "High";
				}else{
					$risk = "Very High";
				}
			}else if($age >= 40 && $age <= 49) {
				if($val < 0.73) {
					$risk = "Low";
				}else if($val <= 0.79) {
					$risk = "Moderate";
				}else if($val <= 0.87) {
					$risk = "High";
				}else{
					$risk = "Very High";
				}
			}else if($age >= 50 && $age <= 59) {
				if($val < 0.74) {
					$risk = "Low";
				}else if($val <= 0.81) {
					$risk = "Moderate";
				}else if($val <= 0.88) {
					$risk = "High";
				}else{
					$risk = "Very High";
				}
			}else if($age >= 60 && $age <= 69) {
				if($val < 0.76) {
					$risk = "Low";
				}else if($val <= 0.83) {
					$risk = "Moderate";
				}else if($val <= 0.90) {
					$risk = "High";
				}else{
					$risk = "Very High";
				}
			}else {
				$risk = NULL;
			}
			
		}
		return $risk;
	}
	
	function getBMIRating($bmi) {
		if($bmi == 'NULL') {
			$rating = NULL;
			return;
		}
		if($bmi < 18.5) {
			$rating = "Underweight";
		}else if($bmi <= 24.9) {
			$rating = "Healthy";
		}else if($bmi <= 29.9) {
			$rating = "Overweight";
		}else if($bmi <= 39.9) {
			$rating = "Obese";
		}else {
			$rating = "Extreme Obesity";
		}
		return $rating;
	}	
?>
