<?php
/*
 * (C) Copyright 2013 Rasmus Zweidorff Iversen.
 *
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the GNU Lesser General Public License
 * (LGPL) version 2.1 which accompanies this distribution, and is available at
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 */

$keywordStart = "PHPUnitTest";
$keywords = array ( "TestSuite", "TestCase", "TestDescription" );

function assertTrue($condition) {
	if(!$condition) {
		throw new PHPUnitTestException();
	}
}

function assertFalse($condition) {
	if($condition) {
		throw new PHPUnitTestException();
	}
}

function assertNull($object) {
	if(!is_null($object)) {
		throw new PHPUnitTestException();
	}
}

function assertNotNull($object) {
	if(is_null($object)) {
		throw new PHPUnitTestException();
	}
}

function assertEmptyArray($object) {
	if(!empty($object)) {
		throw new PHPUnitTestException();
	}
}

function assertNotEmptyArray($object) {
	if(empty($object)) {
		throw new PHPUnitTestException();
	}
}

class PHPUnitTestException extends Exception {

}

function errorHandler($errno, $errstr, $errfile, $errline) {
	return true;
}

$source = file_get_contents(basename($_SERVER['SCRIPT_FILENAME']));
if(is_null($source)) {
	exit();
}
$tokens = token_get_all($source);

$index = 0;
foreach($tokens as $key => $token) {
	if(count($token) != 3) {
		continue;
	}
	
	if($token[0] != T_DOC_COMMENT) {
		continue;
	}
	
	$method = false;
	$methodName = null;
	for($i = $key + 1; $i < count($tokens); $i++) {
		if($tokens[$i][0] == T_WHITESPACE ||
		   $tokens[$i][0] == T_PRIVATE ||
		   $tokens[$i][0] == T_PUBLIC ||
		   $tokens[$i][0] == T_PROTECTED) {
			continue;
		}
		
		if($tokens[$i][0] == T_FUNCTION) {
			$method = true;
			continue;
		}
		
		if($tokens[$i][0] == T_STRING &&
		   $method) {
			$methodName = $tokens[$i][1];
			break;
		}

		break;
	}
	if(is_null($methodName)) {
		continue;
	}
	$tests[$index]["MethodName"] = $methodName;
	
	$comment = $token[1];
	
	if(preg_match("/\@" . $keywordStart . "/i", $comment)) {
		foreach($keywords as $keyword) {
			preg_match("/\@(" . $keyword .")[\t]+([a-zA-Z0-9 ]*)/i", $comment, $matches);
			if(count($matches) == 3) {
				$tests[$index][$keyword] = $matches[2];
			} else {
				$tests[$index][$keyword] = null;
			}

		}
	}
	$tests[$index]["Result"] = null;
	$index++;
}
?>

<table>
	<tr>
<?php
$titles = array_keys($tests[0]);
foreach($titles as $title) {
?>
		<td style="font-weight:bold;background-color:lightgray;"><?= $title ?></td>
<?php
}
?>
	</tr>
<?php
foreach($tests as $row) {
?>
	<tr>
<?php
	foreach($row as $key => $col) {
		if($key == "Result") {
		$status = "PASS";
		try {
			call_user_func($row["MethodName"]);
		} catch(Exception $e) {
			$status = "FAIL <a href=\"javascript:alert('" . str_replace("\n", "\\n\\n", addslashes($e->getTraceAsString())) . "')\">(Trace)</a>";
		}
?>
		<td style="background:<?= $status == "PASS" ? "lightgreen" : "lightcoral" ?>;">
<?php
		echo $status;
		$tests[$index]["Result"] = $status;
		
?>
		</td>
<?php
		} else {
?>
		<td><?= $col ?></td>
<?php
		}
	}
?>
	</tr>
<?php
}
?>
</table>
<?php
unset($evaluate_test);
?>