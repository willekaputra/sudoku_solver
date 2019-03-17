<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

class SudokuController extends Controller
{
	public function index(Request $request) {
		$validator = Validator::make($request->all(), [
			"row"    => "required|array|min:9",
		]);
		if(!$validator->fails()) {
			$arrayGrid = array();
			foreach ($request->row as $key => $value) {
				$value = str_replace("[", "", $value);
				$value = str_replace("]", "", $value);
				array_push($arrayGrid, explode(",", $value));
			}
			$n = count($arrayGrid);
			if($this->solve($arrayGrid, $n)) {
				$arrayResult = array();
				foreach ($arrayGrid as $key => $value) {
					$strResult = "[". implode(",", $value). "]";
					array_push($arrayResult, $strResult);
				}
				return $arrayResult;
			}
			else {
				dd("Tidak ada solusi yang tersedia");
			}
		}
		else {
			dd($validator->errors()->all());
		}
	}

	private function solve(&$arrayGrid, $n) {
		$isEmpty = true; 
		for ($i = 0; $i < $n; $i++) {
			for ($j = 0; $j < $n; $j++) {
				if ($arrayGrid[$i][$j] == 0) {
					$row = $i; 
					$col = $j; 

					$isEmpty = false;  
					break; 
				} 
			} 
			if (!$isEmpty) { 
				break; 
			} 
		}
		// Tidak ada kotak yang kosong tersisa 
		if ($isEmpty)  
		{ 
			return true; 
		} 

   		// Backtrack algroithm
		for ($num = 1; $num <= $n; $num++) 
		{ 
			if ($this->checkCandidateAvailable($arrayGrid, $row, $col, $num)) { 
				$arrayGrid[$row][$col] = $num; 
				if ($this->solve($arrayGrid, $n))  
				{ 
					return true; 
				}  
                $arrayGrid[$row][$col] = 0; // replace it 
        	} 
    	} 
    	return false; 
	}

	private function checkCandidateAvailable($sudokuGrid, $x, $y, $candidateNumber) {
	    //check column
		for($col = 0; $col < 9; $col++) {
			if($sudokuGrid[$x][$col] == $candidateNumber) 
				return false;
		}

	    //check row
		for($row = 0; $row < 9; $row++) {
			if($sudokuGrid[$row][$y] == $candidateNumber) 
				return false;
		}

	    //check in box
		$r = $x - $x % 3;
		$c = $y - $y % 3;
		for ($i = $r; $i < $r + 3; $i++) {
			for ($j = $c; $j < $c + 3; $j++) {
				if ($sudokuGrid[$i][$j] == $candidateNumber)
					return false;
			}
		}
		return true;
	}
}
