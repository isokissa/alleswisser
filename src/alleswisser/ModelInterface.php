<?php
namespace Isokissa\Alleswisser;


interface ModelInterface
{    
    public function clearAll();
    
    public function count();
    
    public function addFirstQuestion( $question, $answerYes, $answerNo );
    
    public function addDistinguishingQuestion( $parentAnswerId, $question, $answerYes );
    
    public function getQuestion( $questionId );
    
    public function getAnswer( $answerId );
}

class ModelInvalidDataMapException extends \Exception {}

class ModelMissingParametersException extends \Exception {}

class ModelCannotAddFirstQuestionTwiceException extends \Exception {}

class ModelInvalidParentException extends \Exception {}


?>
