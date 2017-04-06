<?php
//tested
//viser quiz
function showQuiz($connection,$qid){
    $showQuizName="select name from quiz where qid='$qid'";
    $result = mysqli_query ( $connection, $showQuizName ) or die ( mysqli_error ( $connection ) );
    $count= mysqli_num_rows ( $result );
    //if($count==1){
    $row = mysqli_fetch_array ( $result );
    $quizName=$row['name'];
    return $quizName;
    //}
}
//viser statistikk for lærer
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

//tested
//viser quiznavn
    function displayQuizName($quizName){
        echo "<h2>".$quizName."</h2>";
}

//tested
//registrere bruker 
function registerUser($connection, $username, $email, $password, $teacher,$salt1,$salt2) {
    // trenger en logikk som skjekker om variablene ikke overskirder en lengde på 255
    if ($teacher) {
        $query = "INSERT INTO `users` (username, password, Remail , role,salt1,salt2) VALUES ('$username', '$password', '$email','T','$salt1','$salt2')";
    } else {
        $query = "INSERT INTO `users` (username, password, email , role,salt1,salt2) VALUES ('$username', '$password', '$email','S','$salt1','$salt2')";
    }
    return mysqli_query ( $connection, $query );
}

//funksjon for å sjekke resultat
function popAndGrade($connection, $role, $userid, $classname, $corrAns, $lastQuiz)
{
    $noOfCorrect = 0;
    //Her er det noe "off by one" tull. corrAns er 1-indeksert?!
    for ($i=0; $i < sizeof($corrAns); $i++) { 
        $answer = $_POST['question-'.($i+1).'-answers'];
        echo "<li>
        
                <h3>
                    Question ".($i+1).": 
                </h3>
                
                <div>
                    Your answer: $answer<br>".$corrAns[$i][0]." was correct.
                </div>";
                
        
        if ($role == 'S') {
            //Sjekker om dette spørsmålet har blitt besvart fra før:
            $ifExists = mysqli_query($connection, "SELECT * FROM hasAnsweredQuestion where userid=$userid and qid=$lastQuiz and questid=".$corrAns[$i][1]);
                                 
            
            if (mysqli_num_rows($ifExists) > 0) {
                //Oppdaterer spørsmål i DB
                if ($corrAns[$i][0] == $answer) {
                    $query = "UPDATE hasAnsweredQuestion SET answer=1 WHERE userid=$userid and qid=$lastQuiz and questid=".$corrAns[$i][1];
                    $noOfCorrect++;
                    priorityChange($connection,($corrAns[$i][0] == $answer), $userid, $lastQuiz, $corrAns, $i);
                    
                }
                else{
                    $query = "UPDATE hasAnsweredQuestion SET answer=0 WHERE userid=$userid and qid=$lastQuiz and questid=".$corrAns[$i][1];
                    priorityChange($connection,($corrAns[$i][0] == $answer), $userid, $lastQuiz, $corrAns, $i);
                }
            }else{
                //Lagrer nytt besvart spørsmål i DB
                if ($corrAns[$i][0] == $answer) {
                    $query = "INSERT INTO hasAnsweredQuestion (userid, qid, questid, answer) VALUES ($userid, $lastQuiz, ".$corrAns[$i][1].", 1)";
                    $noOfCorrect++;
                    priorityChange($connection,($corrAns[$i][0] == $answer), $userid, $lastQuiz, $corrAns, $i);
                }
                else{
                    $query = "INSERT INTO hasAnsweredQuestion (userid, qid, questid, answer) VALUES ($userid, $lastQuiz, ".$corrAns[$i][1].", 0)";
                    priorityChange($connection,($corrAns[$i][0] == $answer), $userid, $lastQuiz, $corrAns, $i);
                }
            }
            
            //Gjennomfører oppdatering
            mysqli_query($connection,$query);
            
        }
    }
    echo "
        <div> <br>
            You answered ".$noOfCorrect."/".sizeof($corrAns)." questions correctly.
        </div>";
}
//endrer prioritet på spørsmål
function priorityChange($connection, $isCorrect, $userid, $lastQuiz, $corrAns, $i)
{
    $query = mysqli_query($connection, "SELECT priority FROM hasAnsweredQuestion where userid=$userid and qid=$lastQuiz and questid=".$corrAns[$i][1]);
    $minOrMax = mysqli_fetch_assoc($query);
    if (($minOrMax['priority'] == 5) and $isCorrect) {
        # do nothing
    }
    elseif (($minOrMax['priority'] == 1) and !$isCorrect) {
        # do nothing
    }
    elseif ($isCorrect) {
        # plusone
        $query = "UPDATE hasAnsweredQuestion SET priority = priority + 1 WHERE userid=$userid and qid=$lastQuiz and questid=".$corrAns[$i][1];
        mysqli_query($connection,$query);
    }
    else{
        # minusone
         $query = "UPDATE hasAnsweredQuestion SET priority = priority - 1 WHERE userid=$userid and qid=$lastQuiz and questid=".$corrAns[$i][1];
         mysqli_query($connection,$query);
    }
    
}
//Populerer quizlisten dersom dette er et vanlig quiz
function popQuiz($connection, $currQuiz)
{
    $qid = mysqli_query($connection, "SELECT qid FROM quiz WHERE name='".$currQuiz."'")->fetch_assoc();
    //Holder styr på forrige (denne) quizen for grade.php
    $_SESSION['lastQuiz'] = $qid['qid'];
    $questionIDs = mysqli_query($connection, "SELECT * FROM quiz JOIN hasQuestions ON quiz.qid = hasQuestions.Quizid WHERE qid = '".$qid['qid']."'");
    //Setter opp et array for de rette svarene
    //$corrAns = array();
    $corrans = $_SESSION['corrAns'];
    $questionNumber = 1;
    while ($row = $questionIDs->fetch_assoc()){
        $currQuestion = mysqli_query($connection, "SELECT * FROM questions WHERE qid=".$row['queid'])->fetch_assoc();
        $corrAns[] = [$currQuestion['Ans'], $currQuestion['qid']];
        echo "<li>
    
        <h3>".$currQuestion['question']."</h3>
        
        <div>
            <input type='radio' name='question-$questionNumber-answers' id='question-".$questionNumber."-answers-A' value='A' />
            <label for='question-".$questionNumber."-answers-A'>A) ".$currQuestion['A']." </label>
        </div>
        
        <div>
            <input type='radio' name='question-$questionNumber-answers' id='question-".$questionNumber."-answers-B' value='B' />
            <label for='question-".$questionNumber."-answers-B'>B) ".$currQuestion['B']." </label>
        </div>
        
        <div>
            <input type='radio' name='question-$questionNumber-answers' id='question-".$questionNumber."-answers-C' value='C' />
            <label for='question-".$questionNumber."-answers-C'>C) ".$currQuestion['C']." </label>
        </div>
        
        <div>
            <input type='radio' name='question-$questionNumber-answers' id='question-".$questionNumber."-answers-D' value='D' />
            <label for='question-".$questionNumber."-answers-D'>D) ".$currQuestion['D']." </label>
        </div>
    
        </li>";
        $questionNumber++;
        }
    //Gjør korrekte svar til sessionvariabel
    $_SESSION['corrAns']     = $corrAns;
}
//Populerer questionlisten ved et spacedrep quiz. currPri sier hvilken prioritet som gjelder, numRows sier hvor mange av hver type spørsmål vi skal forsøke å hente ut.
function questPopper($connection, $userid, $currPri, $numRows, $questionNumber){
    $corrAns = $_SESSION['corrAns'];
    $questCounter = 0;
    //Går gjennom hver kategori
    while ($row = $currPri -> fetch_assoc() and $questCounter < $numRows) {
        //return heller corrans også kjører vi questionnumber som sessionvariabel
        //Henter spørsmål for hver kategori
        $currQuestion = mysqli_query($connection, "SELECT * FROM questions WHERE qid=".$row['questid'])->fetch_assoc();
        $corrAns[] = [$currQuestion['Ans'], $currQuestion['qid']];
        echo "<li>
        <h3>".$currQuestion['question']."</h3>
        
        <div>
            <input type='radio' name='question-$questionNumber-answers' id='question-".$questionNumber."-answers-A' value='A' />
            <label for='question-".$questionNumber."-answers-A'>A) ".$currQuestion['A']." </label>
        </div>
        
        <div>
            <input type='radio' name='question-$questionNumber-answers' id='question-".$questionNumber."-answers-B' value='B' />
            <label for='question-".$questionNumber."-answers-B'>B) ".$currQuestion['B']." </label>
        </div>
        
        <div>
            <input type='radio' name='question-$questionNumber-answers' id='question-".$questionNumber."-answers-C' value='C' />
            <label for='question-".$questionNumber."-answers-C'>C) ".$currQuestion['C']." </label>
        </div>
        
        <div>
            <input type='radio' name='question-$questionNumber-answers' id='question-".$questionNumber."-answers-D' value='D' />
            <label for='question-".$questionNumber."-answers-D'>D) ".$currQuestion['D']." </label>
        </div>
        </li>";
        $questCounter++;
        $questionNumber++;
        
        
    }
    //Gjør korrekte svar til sessionvariabel
    $_SESSION['corrAns']     = $corrAns;
    return $questionNumber;
}
//Hovedfunksjon for spacedrep quiz
function spacedRepQuiz($connection, $userid)
{
    //Kaller alle spacedrep quizzer for 666
    $_SESSION['lastQuiz'] = 666;
    //Setter opp et array for de rette svarene
    $corrAns = array();
    $questionNumber = 1; 
    //Henter ID for gjeldene spørsmål. Dårlig kode, jeg vet
    $pri1 = mysqli_query($connection, "SELECT DISTINCT(questid) FROM hasAnsweredQuestion JOIN questions where hasAnsweredQuestion.questID = questions.qid and priority = 1 and hasAnsweredQuestion.userid = $userid order by rand()");
    $pri2 = mysqli_query($connection, "SELECT DISTINCT(questid) FROM hasAnsweredQuestion JOIN questions where hasAnsweredQuestion.questID = questions.qid and priority = 2 and hasAnsweredQuestion.userid = $userid order by rand()");
    $pri3 = mysqli_query($connection, "SELECT DISTINCT(questid) FROM hasAnsweredQuestion JOIN questions where hasAnsweredQuestion.questID = questions.qid and priority = 3 and hasAnsweredQuestion.userid = $userid order by rand()");
    $pri4 = mysqli_query($connection, "SELECT DISTINCT(questid) FROM hasAnsweredQuestion JOIN questions where hasAnsweredQuestion.questID = questions.qid and priority = 4 and hasAnsweredQuestion.userid = $userid order by rand()");
    $pri5 = mysqli_query($connection, "SELECT DISTINCT(questid) FROM hasAnsweredQuestion JOIN questions where hasAnsweredQuestion.questID = questions.qid and priority = 5 and hasAnsweredQuestion.userid = $userid order by rand()");
    //Kaller hjelpefunksjonen
    $questionNumber = questPopper($connection, $userid, $pri1, 3, $questionNumber);
    $questionNumber = questPopper($connection, $userid, $pri2, 3, $questionNumber);
    $questionNumber = questPopper($connection, $userid, $pri3, 2, $questionNumber);
    $questionNumber = questPopper($connection, $userid, $pri4, 1, $questionNumber);
    $questionNumber = questPopper($connection, $userid, $pri5, 1, $questionNumber);
  
    
}

//tested
//Viser aktive quizer
function displayActiveQuizes($connection,$classname){
    $sql = mysqli_query($connection, "SELECT name FROM quiz WHERE (classid=(SELECT classid from class where classname='$classname') AND active=1) ORDER BY activDate DESC");
    while ($row = $sql->fetch_assoc()){
        echo "<a href='../common/quizPage.php?quiz=".$row['name']."'>".$row ['name'] ."</a></br>";
    }
}

//tested
//legger til student i fag
function attends($connection,$classname,$userid){
    $query = "INSERT INTO attends(userid,classid) values ('$userid',(SELECT classid FROM class WHERE classname='$classname'))";
    // utører sqloperasjonen eller skriver ut en feilmelding
    return mysqli_query ( $connection, $query );
}

//tested
//Skriver ut klasser studenten er med i
function showClassesStudent($connection,$userid){
    $showClasses = "SELECT classname, class.classid from class join attends on class.classid=attends.classid where attends.userid='$userid'";
    $classes = mysqli_query ( $connection, $showClasses ) or die ( mysqli_error ( $connection ) );
    return $classes;
}

//tested
//sletter klassen
function stopAttending($connection,$userid,$classid){
    $deleteAttends="delete from attends where classid='$classid' and userid='$userid'";
    $result = mysqli_query ( $connection, $deleteAttends ) or die ( mysqli_error ( $connection ) );
}
if(isset($_POST['delete'])){
 stopAttending($connection, $_SESSION['userid'], $_POST['delete']);
}
//viser statistikk for studenter
function studentStatistics($connection,$quizName,$userid){
    $qid = mysqli_query($connection, "SELECT qid FROM quiz WHERE name='".$quizName."'")->fetch_assoc();
    $questionIDs = mysqli_query($connection, "SELECT * FROM quiz JOIN hasQuestions ON quiz.qid = hasQuestions.Quizid WHERE qid = '".$qid['qid']."'");
    $i = 1;
    $totalCorrect = 0;
    $totalQuestions = 0;
    while ($row = $questionIDs->fetch_assoc()){
        $currQuestion = mysqli_query($connection, "SELECT question FROM questions WHERE qid=".$row['queid'])->fetch_assoc();
        $currScore = mysqli_query($connection, "SELECT answer FROM hasAnsweredQuestion WHERE qid='".$row['qid']."' AND questid='".$row['queid']."' AND userid = '".$userid."'");
        while ($score = $currScore->fetch_assoc()) {
            if ($score['answer']==1) {
                $totalCorrect++;
                $totalQuestions++;
                $grade = "<div style='float: right; color: green;'>Correct</div>";
            }
            else {
                $totalQuestions++;
                $grade = "<div style='float: right; color: red;'>Wrong</div>";
            }
        }
    
        foreach ($currQuestion as $key => $val) {
            echo $i.". ".$val.$grade."</br>";
        }
        $i++;
    }
    echo "<strong>Total score: ".$totalCorrect."/".$totalQuestions."</strong>";
}
//viser statistikk på coursePage for lærer
function displayStatistics($connection,$classname){
    $sql = mysqli_query ( $connection, "SELECT qid, name FROM quiz WHERE classid=(SELECT classid from class where classname='$classname')" );
    mysqli_close();
    while ( $row = $sql->fetch_assoc () ) {
        $qid = mysqli_query ( $connection, "SELECT qid FROM quiz WHERE name='" . $row ['name'] . "'" )->fetch_assoc ();
        mysqli_close();
        $questionIDs = mysqli_query ( $connection, "SELECT * FROM quiz JOIN hasQuestions ON quiz.qid = hasQuestions.Quizid WHERE qid = '" . $qid ['qid'] . "'" );
        mysqli_close();
        $totalCorrect = 0;
        $totalQuestions = 0;
        while ( $scoreCalculator = $questionIDs->fetch_assoc () ) {
            $currQuestion = mysqli_query ( $connection, "SELECT question FROM questions WHERE qid=" . $scoreCalculator ['queid'] )->fetch_assoc ();
            mysqli_close();
            $currScore = mysqli_query ( $connection, "SELECT answer FROM hasAnsweredQuestion WHERE qid=" . $scoreCalculator ['qid'] . " AND questid=" . $scoreCalculator ['queid'] );
            mysqli_close();
            while ( $score = $currScore->fetch_assoc () ) {
                if ($score ['answer'] == 1) {
                    $totalCorrect ++;
                    $totalQuestions ++;
                } else {
                    $totalQuestions ++;
                }
            }
        }
        $totalScorePercent = ($totalCorrect / $totalQuestions) * 100;
        $oneDecimalScore = number_format ( $totalScorePercent, 1 );
            
        if (($totalScorePercent <= 100) && (0 <= $totalScorePercent)) {
            echo "<a href='statisticsTeacher.php?quizId=" . $row ['qid'] . "'>" . $row ['name'] . "</a><div style='float: right';>" . $oneDecimalScore . "%</div></br>";
        } else {
            echo "<a href='statisticsTeacher.php?quizId=" . $row ['qid'] . "'>" . $row ['name'] . "</a><div style='float: right';>Not answered yet</div></br>";
        }
    }   
}


//tested
//skriver ut klassevanvet
function displayClassname(){
    Echo'<h2>Teacher - '. $_SESSION['classname'].'</h2>';
}
//finner quizzer i klasse
function showQuizes($connection, $classname) {
    $showQuizes = "SELECT qid,name,active from quiz WHERE classid=(SELECT classid from class where classname='$classname')";
    $quizes = mysqli_query ( $connection, $showQuizes ) or die ( mysqli_error ( $connection ) );
    mysqli_close ();
    return $quizes;
}
//skriver ut quizzer 
function displayQuizes($count,$quizes){
    if ($count > 0) {
        while ( $row = mysqli_fetch_array ( $quizes ) ) {
            echo "<form class='form-signin' method='POST'>
                                <a href='../common/quizPage.php?quiz=" . $row ['name'] . "'>" . $row ['name'] . "</a>
                                <button name='delete' class='btn btn-default btn-xs' type='submit' value=" . $row ['qid'] . " style='float: right;'>
                                    <span class='glyphicon glyphicon-trash' aria-hidden='true'></span></button>
                                <a href='createQuiz.php?id=" . $row ['qid'] . "' class='btn btn-default btn-xs' aria-label='Left Align' style='float: right;'>
                                    <span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a>";
            if ($row ['active'] == 1) {
                echo "<input type='hidden'  name='activQid' value=" . $row ['qid'] . "><button class='btn btn-default btn-xs' name='activStat' type='submit' value=" . $row ['active'] . " style='float: right;'>
                                    <li  style='color: green;'>Live</li>
                                </button></form>";
            } else {
                echo "<input type='hidden'  name='activQid' value=" . $row ['qid'] . "><button class='btn btn-default btn-xs'  name='activStat' type='submit' value=" . $row ['active'] . " style='float: right;'>
                                    <li  style='color: red;'>Live</li>
                                </button></form>";
            }
        }
    } else {
        echo "Du har ingen quizer.</br>";
    }
}
// lager en quiz
function makeQuiz($connection, $quizName, $classname) {
    $createQuiz = "INSERT INTO quiz (classid,name,active) values ((SELECT classid from class where classname='$classname'),'$quizName',0)";
    $result = mysqli_query ( $connection, $createQuiz ) or die ( mysqli_error ( $connection ) );
    mysqli_close ();
    header ( 'Location:coursePageTeacher.php?id=' . $_SESSION ['classname'] );
}
//sletter en quiz
function deleteQuiz($connection, $qid) {
    $deleteHasQuestions = "delete from hasQuestions where quizid='$qid'";
    $result = mysqli_query ( $connection, $deleteHasQuestions ) or die ( mysqli_error ( $connection ) );
    mysqli_close ();
    $deleteQuiz = "delete from quiz where qid='$qid'";
    $result = mysqli_query ( $connection, $deleteQuiz ) or die ( mysqli_error ( $connection ) );
    mysqli_close ();
    header ( 'Location:coursePageTeacher.php?id=' . $_SESSION ['classname'] );
}
// Gjør en quiz live
function activateQuiz($connection, $qid, $status) {
    $qiz = $_POST ['activQid'];
    $status = $_POST ['activStat'];
    if ($status == 1) {
        $activateQuiz = "update quiz set active=0 where qid='$qiz' ";
    } else {
        date_default_timezone_set ( 'Europe/Oslo' );
        $date = date ( 'Y-m-d H-i-s' );
        $activateQuiz = "update quiz set active=1, activDate='$date' where qid='$qiz' ";
    }
    $result = mysqli_query ( $connection, $activateQuiz ) or die ( mysqli_error ( $connection ) );
    mysqli_close ();
    header ( 'Location:coursePageTeacher.php?id=' . $_SESSION ['classname'] );
}
//lager et spørsmål og lagrer i database
function makeQuestion($connection,$qid,$classname,$question,$a,$b,$c,$d,$ans,$topic){
    $makeQuestion = "INSERT INTO questions(classid,question,A,B,C,D,Ans,tema) values((SELECT classid FROM class WHERE classname='$classname'),'$question','$a','$b','$c','$d','$ans','$topic') ";
    $putIntoQuiz = "INSERT INTO hasQuestions values($qid,(SELECT qid FROM questions WHERE question='$question')) ";
    $result = mysqli_query ( $connection, $makeQuestion) or die ( mysqli_error ( $connection ) );
    $result = mysqli_query ( $connection, $putIntoQuiz) or die ( mysqli_error ( $connection ) );
    header("Location:createQuiz.php?id=".$qid);
}
//finner spørsmål
function showQuestions($connection,$classname){
    $query = "SELECT question FROM questions WHERE classid=(SELECT classid from class where classname='$classname')";
    $questions = mysqli_query ( $connection, $query ) or die ( mysqli_error ( $connection ) );
    $count = mysqli_num_rows ( $questions );
    mysqli_close();
    return $count;
}
//setter opp query for å hente info om quizen
function getQuizInfo($connection,$qid){
    $showQuizName="select name from quiz where qid='$qid'";
    $result = mysqli_query ( $connection, $showQuizName ) or die ( mysqli_error ( $connection ) );
    $count= mysqli_num_rows ( $result );
    if($count==1){
        $row = mysqli_fetch_array ( $result );
        $quizName=$row['name'];
        return $quizName;
    }
}
//setter opp form for å legge til quiz fra høyre panel inn i quizen
function addQuestion($connection,$questionId,$topic,$qid){
    $addToHasQuestions="INSERT INTO hasQuestions (Quizid, queid) VALUES ($qid, $questionId)";
    $result = mysqli_query ( $connection, $addToHasQuestions ) or die ( mysqli_error ( $connection ) );
    mysqli_close();
    header('Location:createQuiz.php?id='.$qid.'&topic='.$topic);
}
//setter opp form for å slette spørsmål fra question-table
function deleteQuestion($connection,$qid,$topic,$questionId){
    $deleteQuestion="delete from questions where qid='$questionId'";
    $deleteFromHasquestions="delete from hasQuestions where queid='$questionId'";
    $result = mysqli_query ( $connection, $deleteFromHasquestions ) or die ( mysqli_error ( $connection ) );
    $result = mysqli_query ( $connection, $deleteQuestion ) or die ( mysqli_error ( $connection ) );
    mysqli_close();
    header('Location:createQuiz.php?id='.$qid.'&topic='.$topic);
}
//setter opp form for å slette spørsmål fra hasquestion-table
function removeQuestion($connection,$qid,$topic,$questionId){
    $deleteFromHasquestions="delete from hasQuestions where queid='$questionId'";
    $result = mysqli_query ( $connection, $deleteFromHasquestions ) or die ( mysqli_error ( $connection ) );
    mysqli_close();
    header('Location:createQuiz.php?id='.$qid.'&topic='.$topic);
}
//skriver ut questions
function displayQuestions($connection,$topic,$classname,$qid){
    if ($topic == '') {
         
        $sql = mysqli_query($connection, "SELECT qid, question, tema FROM questions WHERE classid=(SELECT classid from class where classname='$classname')");
        while ($row = $sql->fetch_assoc()){
    
            $alreadyInQuiz = mysqli_query($connection, "SELECT Quizid FROM hasQuestions WHERE queid='".$row['qid']."' AND quizId='".$qid."'");
    
            if ($alreadyInQuiz->num_rows == 0){
                echo  "<form class='form-signin' method='POST'>
                                        <button name='addQuestionToQuiz' class='btn btn-default btn-xs' type='submit' value=".$row['qid'].">
                                            ".$row['question']."</button>
                                            <button name='delete' class='btn btn-default btn-xs' type='submit' value=".$row['qid']." style='float: right;'>
                                            <span class='glyphicon glyphicon-trash' aria-hidden='true'></span></button></form>";
    
            }
            else {
                /* Bruk denne for disabled buttons
                 echo  "<form class='form-signin' method='POST'>
                 <button class='btn btn-default btn-xs disabled' value=".$row['qid'].">
                 ".$row['question']."</button></form>";
                 */
            }
    
        }
    
    }
    
    else {
    
        $sql = mysqli_query($connection, "SELECT qid, question, tema FROM questions WHERE classid=(SELECT classid from class where classname='$classname') AND tema='$topic'");
        while ($row = $sql->fetch_assoc()){
    
            $alreadyInQuiz = mysqli_query($connection, "SELECT Quizid FROM hasQuestions WHERE queid='".$row['qid']."' AND quizId='".$qid."'");
    
            if ($alreadyInQuiz->num_rows == 0){
                echo  "<form class='form-signin' method='POST'>
                                        <button name='addQuestionToQuiz' class='btn btn-default btn-xs' type='submit' value=".$row['qid'].">
                                            ".$row['question']."</button>
                                            <button name='delete' class='btn btn-default btn-xs' type='submit' value=".$row['qid']." style='float: right;'>
                                            <span class='glyphicon glyphicon-trash' aria-hidden='true'></span></button></form>";
    
            }
            else {
                /* Bruk denne for disabled buttons
                 echo  "<form class='form-signin' method='POST'>
                 <button class='btn btn-default btn-xs disabled' value=".$row['qid'].">
                 ".$row['question']."</button></form>";
                 */
            }
        }
    }
}
//skriver ut spørsmål i quizen
function displayQuestionsInQuiz($connection,$quizName){
    $qid = mysqli_query($connection, "SELECT qid FROM quiz WHERE name='".$quizName."'")->fetch_assoc();
    $questionIDs = mysqli_query($connection, "SELECT * FROM quiz JOIN hasQuestions ON quiz.qid = hasQuestions.Quizid WHERE qid = '".$qid['qid']."'");
    $i = 1;
    while ($row = $questionIDs->fetch_assoc()){
        $currQuestion = mysqli_query($connection, "SELECT question FROM questions WHERE qid=".$row['queid'])->fetch_assoc();
        foreach ($currQuestion as $key => $val) {
            echo "<form class='form-signin' method='POST'>".$i.". ".$val."<button name='remove' class='btn btn-default btn-xs' type='submit' value=".$row['queid']." style='float: right;'>
                                                <span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button></form>";
        }
        $i++;
    }
    
}
//henter klasser
function showClasses($connection,$userid){
$showClasses = "SELECT * FROM class WHERE creator='$userid'";
$classes = mysqli_query ( $connection, $showClasses ) or die ( mysqli_error ( $connection ) );
//mysqli_close();
return $classes;
}

//tested
//lager ny klasse
function makeClass($connection,$userid,$classname){
    $makeClass = "INSERT INTO class(classname, creator) values('$classname','$userid')";
    return mysqli_query ( $connection, $makeClass );
    mysqli_close();
}
//deaktiverer et fag
function deactivateClass($connection,$classID){
    $deleteCourse="UPDATE class SET teacherDeleted = 1 WHERE classid = $classID;";
    $result = mysqli_query ( $connection, $deleteCourse ) or die ( mysqli_error ( $connection ) );
    mysqli_close();
}
?>
