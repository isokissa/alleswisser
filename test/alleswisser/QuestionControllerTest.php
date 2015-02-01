<?php
namespace Isokissa\Alleswisser;

require_once( "src/alleswisser/QuestionController.php" );

class QuestionControllerTest extends \PHPUnit_Framework_TestCase
{
        
    protected function setUp()
    {
    }
    
    public function testCreateWithNullModel_ThrowsException()
    {
        $this->setExpectedException( "Isokissa\Alleswisser\QuestionControllerInvalidModelException" );
        $shouldFail = new QuestionController( null, null );
    }
    
    public function testCreateWithNullView_ThrowsException()
    {
        $dummyModel = $this->getMockBuilder( "ModelInterface" )
                           ->getMock();
        $this->setExpectedException( "Isokissa\Alleswisser\QuestionControllerInvalidViewException" );
        $shouldFail = new QuestionController( $dummyModel, null );
    }
    
    public function testActionInitWithCorrectInput_StartFromBeginning()
    {
        $post = array( "question" => "Is it wet",
                       "answerYes" => "sea", 
                       "answerNo" => "tree" );
        $model = $this->getMockBuilder( "ModelInterface" )
                      ->setMethods( array( "addFirstQuestion" ) )
                      ->getMock();
        $model->expects( $this->once() )
              ->method( "addFirstQuestion" )
              ->with( $this->equalTo( $post["question"] ),
                      $this->equalTo( $post["answerYes"] ),
                      $this->equalTo( $post["answerNo"] ) );
        $view = $this->getMockBuilder( "QuestionView" )
                     ->setMethods( array( "outputNoActionForm" ))
                     ->disableOriginalConstructor()
                     ->getMock();
        $view->expects( $this->once() )
             ->method( "outputNoActionForm" )
             ->with( $this->equalTo( array( "Thank you. Press OK to start again." ) ) );
        $controller = new QuestionController( $model, $view );
        $controller->init( $post );
    }

    public function testActionInitWithIncorrectInput_ErrorMessageAndInitAgain()
    {
        $post = array( "question" => null,
                       "answerYes" => "sea", 
                       "answerNo" => null );
        $model = $this->getMockBuilder( "ModelInterface" )
                      ->getMock();
        $view = $this->getMockBuilder( "QuestionView" )
                     ->setMethods( array( "outputErrorMessages",
                                          "outputInitActionForm" ) )
                     ->disableOriginalConstructor()
                     ->getMock();
        $view->expects( $this->once() )
             ->method( "outputErrorMessages" )
             ->with( array( "Error: field 'question' is not given",
                            "Error: field 'answerNo' is not given" ) );
        $view->expects( $this->once() )
             ->method( "outputInitActionForm" );
        $controller = new QuestionController( $model, $view );
        $controller->init( $post );
    }
    
    public function testActionAddWithIncorrectInput_ErrorMessageAndAddAgain(){
        $post = array( "parentAnswerId" => "5n",
                       "question" => null,
                       "answerYes" => null,
                       "answerNo" => "bird" );
        $model = $this->getMockBuilder( "ModelInterface" )
                      ->getMock();
        $view = $this->getMockBuilder( "QuestionView" )
                     ->setMethods( array( "outputErrorMessages", 
                                          "outputAddActionForm" ) )
                     ->disableOriginalConstructor()
                     ->getMock();
        $view->expects( $this->once() )
             ->method( "outputErrorMessages" )
             ->with( $this->equalTo( array( "Error: field 'question' is not given",
                                            "Error: field 'answerYes' is not given" )
                                    ) );
        $view->expects( $this->once() )
             ->method( "outputAddActionForm" )
             ->with( "5n", "bird" );
        $controller = new QuestionController( $model, $view );
        $controller->add( $post );
    }
    
    public function testActionAddWithCorrectInput_AddTheAnswerAndStartFromBeginning(){
        $post = array( "parentAnswerId" => "5n",
                       "question" => "Does it have four legs",
                       "answerYes" => "cat",
                       "answerNo" => "bird" );
        $model = $this->getMockBuilder( "ModelInterface" )
                      ->setMethods( array( "addDistinguishingQuestion" ) )
                      ->getMock();
        $model->expects( $this->once() )
              ->method( "addDistinguishingQuestion" )
              ->with( $this->equalTo( $post["parentAnswerId"] ),
                      $this->equalTo( $post["question"] ),
                      $this->equalTo( $post["answerYes"] ) );
        $view = $this->getMockBuilder( "QuestionView" )
                     ->setMethods( array( "outputErrorMessages", 
                                          "outputNoActionForm" ) )
                     ->disableOriginalConstructor()
                     ->getMock();
        $view->expects( $this->exactly( 0 ) )
             ->method( "outputErrorMessages" );
        $view->expects( $this->once() )
             ->method( "outputNoActionForm" )
             ->with( $this->equalTo( array( "Thank you. Press OK to start again." ) ) );
        $controller = new QuestionController( $model, $view );
        $controller->add( $post );
    }

    public function testActionAnswerIncompleteParameters_ErrorMessageAndStartFromBeginning(){
        $post = array( "questionId" => "5",
                       "answer" => null );
        $model = $this->getMockBuilder( "ModelInterface" )
                      ->getMock();
        $view = $this->getMockBuilder( "QuestionView" )
                     ->setMethods( array( "outputErrorMessages", 
                                          "outputNoActionForm" ) )
                     ->disableOriginalConstructor()
                     ->getMock();
        $view->expects( $this->once() )
             ->method( "outputErrorMessages" )
             ->with( $this->equalTo( array( "Error: field 'answer' is not given" ) ) );
        $view->expects( $this->once() )
             ->method( "outputNoActionForm" )
             ->with( $this->equalTo( array( "Press OK to start again!" ) ) );
        $controller = new QuestionController( $model, $view );
        $controller->answer( $post );
    }
    
    public function testActionAnswerWhichLeadsToNonExisting_ErrorMessageAndStartFromBeginning(){
        $post = array( "questionId" => "5",
                       "answer" => "yes" );
        $model = $this->getMockBuilder( "ModelInterface" )
                      ->setMethods( array( "getAnswer" ) )
                      ->getMock();
        $model->expects( $this->once() )
              ->method( "getAnswer" )
              ->with( $this->equalTo( "5y" ) )
              ->will( $this->returnValue(null) );
        $view = $this->getMockBuilder( "QuestionView" )
                     ->setMethods( array( "outputErrorMessages", 
                                          "outputNoActionForm" ) )
                     ->disableOriginalConstructor()
                     ->getMock();
        $view->expects( $this->once() )
             ->method( "outputErrorMessages" )
             ->with( $this->equalTo( array( "Fatal: question 5y is not found!" ) ) );
        $view->expects( $this->once() )
             ->method( "outputNoActionForm" )
             ->with( $this->equalTo( array( "Bad error, the data is corrupted" ) ) );
        $controller = new QuestionController( $model, $view );
        $controller->answer( $post );
    }
    
    public function testActionAnswerWhichLeadsToNextQuesiton_outputAnswerFormForNextQuestion(){
        $post = array( "questionId" => "5",
                       "answer" => "yes" );
        $nextQuestionId = "34";
        $nextQuestion = "Is it blue";
        $model = $this->getMockBuilder( "ModelInterface" )
                      ->setMethods( array( "getAnswer", "getQuestion" ) )
                      ->getMock();
        $model->expects( $this->once() )
              ->method( "getAnswer" )
              ->with( $this->equalTo( "5y" ) )
              ->will( $this->returnValue( $nextQuestionId ) );
        $model->expects( $this->once() )
              ->method( "getQuestion" )
              ->with( $this->equalTo( $nextQuestionId ) )
              ->will( $this->returnValue( $nextQuestion ) );
        $view = $this->getMockBuilder( "QuestionView" )
                     ->setMethods( array( "outputErrorMessages", 
                                          "outputAnswerActionForm" ) )
                     ->disableOriginalConstructor()
                     ->getMock();
        $view->expects( $this->exactly( 0 ) )
             ->method( "outputErrorMessages" );
        $view->expects( $this->once() )
             ->method( "outputAnswerActionForm" )
             ->with( $this->equalTo( $nextQuestionId ), 
                     $this->equalTo( $nextQuestion ) );
        $controller = new QuestionController( $model, $view );
        $controller->answer( $post );
    }

    public function testActionAnswerWhichLeadsToFinalAnswer_outputFinalAnswerForm(){
        $post = array( "questionId" => "5",
                       "answer" => "yes" );
        $answer = "cat";
        $model = $this->getMockBuilder( "ModelInterface" )
                      ->setMethods( array( "getAnswer" ) )
                      ->getMock();
        $model->expects( $this->once() )
              ->method( "getAnswer" )
              ->with( $this->equalTo( "5y" ) )
              ->will( $this->returnValue( $answer ) );
        $view = $this->getMockBuilder( "QuestionView" )
                     ->setMethods( array( "outputErrorMessages", 
                                          "outputFinalActionForm" ) )
                     ->disableOriginalConstructor()
                     ->getMock();
        $view->expects( $this->exactly( 0 ) )
             ->method( "outputErrorMessages" );
        $view->expects( $this->once() )
             ->method( "outputFinalActionForm" )
             ->with( $this->equalTo( "5y" ), 
                     $this->equalTo( $answer ) );
        $controller = new QuestionController( $model, $view );
        $controller->answer( $post );
    }
    
    public function testActionAnswerFinalIncomplete_ErrorMessageAndStartFromTheBeginning(){
        $post = array( "answerId" => "5", 
                       "finalAnswer" => "cat" );
        $model = $this->getMockBuilder( "ModelInterface" )
                      ->getMock();
        $view = $this->getMockBuilder( "QuestionView" )
                     ->setMethods( array( "outputErrorMessages",
                                          "outputNoActionForm" ) )
                     ->disableOriginalConstructor()
                     ->getMock();
        $view->expects( $this->once() )
             ->method( "outputErrorMessages" )
             ->with( $this->equalTo( array( "Error: field 'answer' is not given" ) ) );
        $view->expects( $this->once() )
             ->method( "outputNoActionForm" )
             ->with( $this->equalTo( array( "Press OK to start again!" ) ) );
        $controller = new QuestionController( $model, $view );
        $controller->answerFinal( $post );
    }

    public function testActionAnswerFinalReceivesYes_outputCelebrateAndStartFromBeginning(){
        $post = array( "answerId" => "5", 
                       "finalAnswer" => "cat",
                       "answer" => "yes" );
        $model = $this->getMockBuilder( "ModelInterface" )
                      ->getMock();
        $view = $this->getMockBuilder( "QuestionView" )
                     ->setMethods( array( "outputNoActionForm" ) )
                     ->disableOriginalConstructor()
                     ->getMock();
        $view->expects( $this->once() )
             ->method( "outputNoActionForm" )
             ->with( $this->equalTo( array( "I am so smart!", "Let's do it again!" ) ) );
        $controller = new QuestionController( $model, $view );
        $controller->answerFinal( $post );
    }
            
    public function testActionAnswerFinalReceivesNo_outputAddActionFormToAskDistinguishingQuestion(){
        $post = array( "answerId" => "5n", 
                       "finalAnswer" => "cat",
                       "answer" => "no" );
        $model = $this->getMockBuilder( "ModelInterface" )
                      ->getMock();
        $view = $this->getMockBuilder( "QuestionView" )
                     ->setMethods( array( "outputAddActionForm" ) )
                     ->disableOriginalConstructor()
                     ->getMock();
        $view->expects( $this->once() )
             ->method( "outputAddActionForm" )
             ->with( $this->equalTo( $post["answerId"] ), 
                     $this->equalTo( $post["finalAnswer"] ) );
        $controller = new QuestionController( $model, $view );
        $controller->answerFinal( $post );
    }

}
?>
