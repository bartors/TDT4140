<?php
function showQuiz($connection,$qid){
	$showQuizName="select name from quiz where qid='$qid'";
	$result = mysqli_query ( $connection, $showQuizName ) or die ( mysqli_error ( $connection ) );
	$count= mysqli_num_rows ( $result );
	mysqli_close();
	//if($count==1){
	$row = mysqli_fetch_array ( $result );
	$quizName=$row['name'];
	return $quizName;
	//}
}
	function showQuizes($connection, $classname) {
		$showQuizes = "SELECT qid,name,active from quiz WHERE classid=(SELECT classid from class where classname='$classname')";
		$quizes = mysqli_query ( $connection, $showQuizes ) or die ( mysqli_error ( $connection ) );
		mysqli_close ();
		return $quizes;
	}
	
	function teacherStatistics($connection,$quizName){
		$qid = mysqli_query($connection, "SELECT qid FROM quiz WHERE name='".$quizName."'")->fetch_assoc();
		$questionIDs = mysqli_query($connection, "SELECT * FROM quiz JOIN hasQuestions ON quiz.qid = hasQuestions.Quizid WHERE qid = '".$qid['qid']."'");
		$i = 1;
		$totalCorrect = 0;
		$totalQuestions = 0;
		while ($row = $questionIDs->fetch_assoc()){
			$currQuestion = mysqli_query($connection, "SELECT question FROM questions WHERE qid=".$row['queid'])->fetch_assoc();
			$currScore = mysqli_query($connection, "SELECT answer FROM hasAnsweredQuestion WHERE qid=".$row['qid']." AND questid=".$row['queid']);
			$scoreCorrect = 0;
			$correct = 0;
			$total = 0;
			while ($score = $currScore->fetch_assoc()) {
				if ($score['answer']==1) {
					$correct++;
					$totalCorrect++;
					$total++;
					$totalQuestions++;
				}
				else {
					$total++;
					$totalQuestions++;
				}
			}
			$scorePercent = ($correct/$total)*100;
			$oneDecimalPercent = number_format($scorePercent, 1);
		
			foreach ($currQuestion as $key => $val) {
				if (($scorePercent <= 100) && (0 <= $scorePercent)) {
					echo $i.". ".$val." <div style='float: right;'>".$oneDecimalPercent."%</div></br>";
				}
				else {
					echo $i.". ".$val." <div style='float: right;'>Not answered yet</div></br>";
				}
			}
			$i++;
		}
		$totalScorePercent = ($totalCorrect/$totalQuestions)*100;
		$oneDecimalScore = number_format($totalScorePercent, 1);
		if (($totalScorePercent <= 100) && (0 <= $totalScorePercent)) {
			echo "<strong>Total score: ".$oneDecimalScore." %</strong>";
		}
		else {
		}
		$thisQuizId = $_GET['quizId'];
		$totalStudents = mysqli_query($connection, "SELECT DISTINCT userid FROM hasAnsweredQuestion WHERE qid='".$thisQuizId."'");
		$numberOfStudentsAnswer = 0;
		while ($totalStudentsRow = $totalStudents->fetch_assoc()){
			$numberOfStudentsAnswer++;
		}
		echo "</br>".$numberOfStudentsAnswer." students have answered this quiz.";
		
	}
	
	function displayQuizName($quizName){
		echo "<h2>".$quizName."</h2>";
	}
?>