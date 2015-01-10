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
             ->with( $this->equalTo( array( "Error: field 'question' is not given",
                                            "Error: field 'answerNo' is not given" ) 
                                    ) );
        $view->expects( $this->once() )
             ->method( "outputInitActionForm" );
        $controller = new QuestionController( $model, $view );
        $controller->init( $post );
    }
            
}
?>
