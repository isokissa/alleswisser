<?php
namespace Isokissa\Alleswisser;

require_once( "ModelInterface.php" );


class Model implements ModelInterface
{
    private $dataMap;
    
    public function __construct( $dataMap ){
        if( $dataMap instanceof DataMapInterface ){
            $this->dataMap = $dataMap; 
        }
        else{
            throw new ModelInvalidDataMapException();
        }
    }
    
    public function deleteAll(){
        $this->dataMap->deleteAll();
    }
    
    public function count(){
        return $this->dataMap->count( "questions" );
    }
    
    public function addFirstQuestion( $question, $answerYes, $answerNo ){
        $this->checkMissing( $question, "question" );
        $this->checkMissing( $answerYes, "answerYes" );
        $this->checkMissing( $answerNo, "answerNo" );
        if( 0 < $this->count() ){
            throw new ModelCannotAddFirstQuestionTwiceException();
        }
        $this->dataMap->set( "questions", "0", $question );
        $this->dataMap->set( "answers", "0y", $answerYes );
        $this->dataMap->set( "answers", "0n", $answerNo );
    }
    
    public function addDistinguishingQuestion( $parentAnswerId, $question, $answerYes ){
        $this->checkMissing( $parentAnswerId, "parentAnswerId" );
        $this->checkMissing( $question, "question" );
        $this->checkMissing( $answerYes, "answerYes" );
        if( empty( $this->dataMap->get( "answers", $parentAnswerId ) ) ){
            throw new ModelInvalidParentException( $parentAnswerId );
        }
        $originalAnswer = $this->dataMap->get( "answers", $parentAnswerId );
        $newQuestionId = uniqid();
        $this->dataMap->set( "questions", $newQuestionId, $question );
        $this->dataMap->set( "answers", $parentAnswerId, $newQuestionId );
        $this->dataMap->set( "answers", $newQuestionId."n", $originalAnswer );
        $this->dataMap->set( "answers", $newQuestionId."y", $answerYes );
        return $newQuestionId;
    }
    
    public function getQuestion( $questionId ){
        return $this->dataMap->get( "questions", $questionId );
    }
    
    public function getAnswer( $answerId ){
        return $this->dataMap->get( "answers", $answerId );
    }
    
    private function checkMissing( $parameter, $name ){
        if( empty( $parameter ) ){
            throw new ModelMissingParametersException( $name." is missing" ); 
        }
    }

}

?>
