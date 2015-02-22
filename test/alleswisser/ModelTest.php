<?php

namespace Isokissa\Alleswisser;

require_once( "src/alleswisser/Model.php" );
require_once( "src/alleswisser/DataMapInterface.php" );


class ModelTest extends \PHPUnit_Framework_TestCase
{
    private $model;
    private $dataMap;
    
    public function setup(){
        $this->storage = new StubDataMap();
        $this->model = new Model( $this->storage );
    }
    
    public function tearDown() {
        $this->model->deleteAll();
    }

    public function testAfterCreatingNew_CountIsZero(){
        $this->assertEquals( 0, $this->model->count() );
    }
    
    public function testAfterClearAll_CountIsZero(){
        $this->model->addFirstQuestion("abc", "def", "ghi");
        $this->assertEquals( 1, $this->model->count() );
        $this->model->deleteAll();
        $this->assertEquals( 0, $this->model->count() );
    }
    
    public function testCreateWithWrongStorage_ThrowsException(){
        $this->setExpectedException( "Isokissa\Alleswisser\ModelInvalidDataMapException" );
        $dummy = new Model( null );
    }
    
    public function testAddFirstQuestionMissingAnswerYes_ThrowsException(){
        $this->setExpectedException( "Isokissa\Alleswisser\ModelMissingParametersException", 
                                     "answerYes is missing" );
        $this->model->addFirstQuestion( "question", "", "answerNo" );
    }

    public function testAddFirstQuestionMissingAnswerNo_ThrowsException(){
        $this->setExpectedException( "Isokissa\Alleswisser\ModelMissingParametersException", 
                                     "answerNo is missing" );
        $this->model->addFirstQuestion( "question", "yes", null );
    }
    
    public function testAddFirstQuestionMissingQuestion_ThrowsException(){
        $this->setExpectedException( "Isokissa\Alleswisser\ModelMissingParametersException", 
                                     "question is missing" );
        $this->model->addFirstQuestion( "", "yes", null );
    }
    
    public function testAddFirstQuestionCorrect_NotEmptyAnymoreAndEverythingCorrectlyStored(){
        $question = "Is it a living creature";
        $answerYes = "cat";
        $answerNo = "spoon";
        $this->model->addFirstQuestion( $question, $answerYes, $answerNo );
        $this->assertEquals( 1, $this->model->count() );
        $this->assertEquals( $question, $this->model->getQuestion( "0" ) );
        $this->assertEquals( $answerYes, $this->model->getAnswer( "0y" ) );
        $this->assertEquals( $answerNo, $this->model->getAnswer( "0n" ) );
    }
    
    public function testAddFirstQuestionTwice_ThrowsException(){
        $this->setExpectedException( "Isokissa\Alleswisser\ModelCannotAddFirstQuestionTwiceException" );
        $this->model->addFirstQuestion( "question", "yes", "no" );        
        $this->model->addFirstQuestion( "question2", "yes2", "no2" );        
    }

    public function testGetNonExistingQuestion_ReturnsEmpty(){
        $this->assertTrue( empty( $this->model->getQuestion( 0 ) ) );
    }

    public function testGetFirstQuestion_Succeeds(){
        $question = "Is it a living creature";
        $answerYes = "cat";
        $answerNo = "spoon";
        $this->model->addFirstQuestion( $question, $answerYes, $answerNo );
        $this->assertEquals( $question, $this->model->getQuestion( 0 ) );
    }
    
    public function testGetNonExistingQuestion_ReturnsNull(){
        $this->assertNull( $this->model->getQuestion( 123 ) );
    }
    
    public function testQuestionStoredPersistently_Succeeds(){
        $question = "Is it a living creature";
        $answerYes = "cat";
        $answerNo = "spoon";
        $this->model->addFirstQuestion( $question, $answerYes, $answerNo );
        $otherModel = new Model( $this->storage );
        $this->assertEquals( $question, $otherModel->getQuestion( 0 ) );
    }
    
    public function testAddDistinguishingQuestionMissingParentAnswerId_Throws(){
        $this->setExpectedException( "Isokissa\Alleswisser\ModelMissingParametersException", 
                                     "parentAnswerId is missing" );
        $this->model->addDistinguishingQuestion( null, "abc", "def" );
    }

    public function testAddDistinguishingQuestionMissingQuestion_Throws(){
        $this->setExpectedException( "Isokissa\Alleswisser\ModelMissingParametersException", 
                                     "question is missing" );
        $this->model->addDistinguishingQuestion( "123", null, "abc" );
    }

    public function testAddDistinguishingQuestionMissingAnswerYes_Throws(){
        $this->setExpectedException( "Isokissa\Alleswisser\ModelMissingParametersException", 
                                     "answerYes is missing" );
        $this->model->addDistinguishingQuestion( "123", "abc", null );
    }

    public function testAddDistinguishingQuestionInvalidParentId_Throws(){
        $parentQuestionId = "123y";
        $this->setExpectedException( "Isokissa\Alleswisser\ModelInvalidParentException", 
                                     $parentQuestionId );
        $this->model->addDistinguishingQuestion( $parentQuestionId, "abc", "def" );
    }

    public function testAddDistinguishingQuestionCorrect_Succeeds(){
        $originalAnswer = "cat"; 
        $this->model->addFirstQuestion( "Is it a living thing", $originalAnswer, "spoon" );
        $newQuestion = "Does it fly";
        $newAnswer = "bird";
        $questionId = $this->model->addDistinguishingQuestion( "0y", $newQuestion, $newAnswer );
        $this->assertEquals( $newQuestion, $this->model->getQuestion( $questionId ) );
        $this->assertEquals( $questionId , $this->model->getAnswer( "0y" ) ); // (1), (2) 
        $this->assertEquals( $originalAnswer, $this->model->getAnswer( $questionId."n" ) ); // (3)
        $this->assertEquals( $newAnswer, $this->model->getAnswer( $questionId."y" ) ); // (4) 
    }

}

class StubDataMap implements DataMapInterface {

    public $maps;
    
    public function __construct(){
        $this->maps = array();
    }

    public function set( $mapName, $key, $value ){
        if( empty( $this->maps[$mapName] ) ){
            $this->maps[$mapName] = array();
        }
        $this->maps[$mapName][$key] = $value;
    }

    public function get( $mapName, $key ){
        if( empty( $this->maps[$mapName][$key] ) ){
            return null;
        }
        else {
            return $this->maps[$mapName][$key];
        }
    }
    
    public function count( $mapName ){
        if( empty( $this->maps[$mapName] ) ){
            return 0;
        }
        else {
            return count( $this->maps[$mapName] );
        }
    }

    public function deleteAll() {
        $this->maps = array();
    }

}

?>
