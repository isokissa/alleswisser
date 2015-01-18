<?php

class QuestionView {

    public function outputNoActionForm( $textLines ){
        $output = <<<EOS
<form method="post">

EOS;
        foreach( $textLines as $line ){
            $output = $output."<p><label>".$line."</label></p>\n";
        }
        $output = $output.<<<EOS
<p>
    <input type="submit" value="OK"/>
</p>
</form>
EOS;
        return $output;
    }
    
    public function outputErrorMessages( $lines ){
        $output = "";
        foreach( $lines as $line ){
            $output = $output.'<p class="error">'.$line."</p>\n";
        }
        return $output;
    }
    
    public function outputInitActionForm(){
        $output = <<<EOS
<form method="post" action="?action=init">
<p>
    <label>No questions defined, enter the first one:</label>
</p>
<p>
    <input name="question" type="text" value="Is it a living thing"/>?
</p>
<p>
    <label>The "yes" answer: </label>
    <input name="answerYes" type="text" value="cat"/>
</p>
<p>
    <label>The "no" answer: </label>
    <input name="answerNo" type="text" value="stone"/>
</p>
<p>
    <input type="submit" value="OK"/></p>
</form>
EOS;
        return $output;
    }

    public function outputAddActionForm( $parentAnswerId, $answerNo ){
        $output = <<<EOS
<form method="post" action="?action=add">
<input name="parentAnswerId" type="hidden" value="$parentAnswerId"/>
<input name="answerNo" type="hidden" value="$answerNo"/>
<p>
    <label>You say <strong>$answerNo</strong> is not the correct answer, 
           please write the correct answer:</label>
</p>
<p>
    <input name="answerYes" type="text"/>?
</p>
<p>
    <label>Enter the question to distinguish your correct answer from 
           <strong>$answerNo</strong>:</label>
</p>
<p>
    <input name="question" type="text"/>?
</p>
<p><input type="submit" value="OK"/></p>
</form>
EOS;
        return $output;
    }
    
    public function outputAnswerActionForm( $questionId, $question ){
        $output = <<<EOS
<form method="post" action="?action=answer">
<input name="questionId" type="hidden" value="$questionId"/>
<p>
    <label>$question?</label>
</p>
<p>
    <input type="submit" name="answer" value="yes">Yes</input>
    <input type="submit" name="answer" value="no">No</input>
</p>
</form>
EOS;
        return $output;
    }
    
    public function outputFinalActionForm( $answerId, $finalQuestion ){
        $output = <<<EOS
<form method="post" action="?action=final">
<input name="answerId" type="hidden" value="$answerId"/>
<p>
    <label>Is the final answer <strong>$finalQuestion</strong>?</label>
</p>
<p>
    <input type="submit" name="answer" value="yes">Yes</input>
    <input type="submit" name="answer" value="no">No</input>
</p>
</form>
EOS;
        return $output;
    }

}

class QuestionViewInvalidModelException extends Exception {}


?>
