<?php
namespace Isokissa\Alleswisser;

require_once( "src/alleswisser/QuestionView.php" );

class QuestionViewTest extends \PHPUnit_Framework_TestCase
{
    private $view;    
    
    protected function setUp(){
        $this->view = new QuestionView();
    }
        
    public function testOutputNoActionForm(){
        $text1 = "Testing text, first line";
        $text2 = "and second line";
        $expectedOutput =<<<EOS
<form method="post">
<p><label>$text1</label></p>
<p><label>$text2</label></p>
<p>
    <input type="submit" value="OK"/>
</p>
</form>
EOS;
        $output = $this->view->outputNoActionForm( array( $text1, $text2 ) );
        $this->assertEquals( $expectedOutput, $output );
    }

    public function testOutputInitActionForm_Works(){
        $question = "Is it a living creature"; 
        $output = $this->view->outputInitActionForm();
        $expectedOutput = <<<EOS
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
        $this->assertEquals( $expectedOutput, $output );
    }

    public function testOutputAddActionForm_Works(){
        $parentAnswerId = 123;
        $answerNo = "cat";
        $output = $this->view->outputAddActionForm( $parentAnswerId, $answerNo );
        $expectedOutput = <<<EOS
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
        $this->assertEquals( $expectedOutput, $output ); 
    }

    public function testOutputAnswerActionForm(){
        $questionId = 123;
        $question = "Is it a living thing";
        $output = $this->view->outputAnswerActionForm( $questionId, $question );
        $expectedOutput = <<<EOS
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
        $this->assertEquals( $expectedOutput, $output );
    }

    public function testOutputAnswerFinalActionForm(){
        $answerId = "231y";
        $finalAnswer = "cat"; 
        $output = $this->view->outputAnswerFinalActionForm( $answerId, $finalAnswer );
        $expectedOutput = <<<EOS
<form method="post" action="?action=answerFinal">
<input name="answerId" type="hidden" value="$answerId"/>
<input name="finalAnswer" type="hidden" value="$finalAnswer"/>
<p>
    <label>Is the final answer <strong>$finalAnswer</strong>?</label>
</p>
<p>
    <input type="submit" name="answer" value="yes">Yes</input>
    <input type="submit" name="answer" value="no">No</input>
</p>
</form>
EOS;
        $this->assertEquals( $expectedOutput, $output ); 
    }
    
    public function testOutputErrorMessages(){
        $lines = array("first", "second");
        $output = $this->view->outputErrorMessages( $lines );
        $expectedOutput = <<<EOS
<p class="error">$lines[0]</p>
<p class="error">$lines[1]</p>

EOS;
        $this->assertEquals( $expectedOutput, $output );
    }

}
?>
