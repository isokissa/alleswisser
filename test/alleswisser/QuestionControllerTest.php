<?php

require_once( "src/alleswisser/QuestionController.php" );


class QuestionControllerTest extends PHPUnit_Framework_TestCase
{
        
    protected function setUp()
    {
    }
    
    public function testCreateWithNullModel_ThrowsException()
    {
        $this->setExpectedException( "QuestionControllerInvalidModelException" );
        $shouldFail = new QuestionController( null, null );
    }
    
    public function testCreateWithNullView_ThrowsException()
    {
        $dummyModel = $this->getMockBuilder( "Model" )
                           ->getMock();
        $this->setExpectedException( "QuestionControllerInvalidViewException" );
        $shouldFail = new QuestionController( $dummyModel, null );
    }
    
    public function testActionInitWithCorrectInput_StartFromBeginning()
    {
        $post = array( "question" => "Is it wet",
                       "answerYes" => "sea", 
                       "answerNo" => "tree" );
        $model = $this->getMockBuilder( "Model" )
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
        $model = $this->getMockBuilder( "Model" )
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
        $model = $this->getMockBuilder( "Model" )
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
        $model = $this->getMockBuilder( "Model" )
                      ->setMethods( array( "addAnswer" ) )
                      ->getMock();
        $model->expects( $this->once() )
              ->method( "addAnswer" )
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
            
}
?>
