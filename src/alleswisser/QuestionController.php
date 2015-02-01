<?php
namespace Isokissa\Alleswisser;

class QuestionController 
{

    private $model; 
    private $view;

    public function __construct( $model, $view )
    {
        if( empty($model) ){
            throw new QuestionControllerInvalidModelException();
        }
        $this->model = $model;
        if( empty( $view ) ){
            throw new QuestionControllerInvalidViewException();
        }
        $this->view = $view;
    }
            
    public function init( $post ){
        $errors = $this->buildErrorMessages( array( "question",
                                                    "answerYes",
                                                    "answerNo" ), 
                                             $post );
        if( !empty( $errors ) ){
            return $this->view->outputErrorMessages( $errors ).
                   $this->view->outputInitActionForm();
        }
        else{
            $this->model->addFirstQuestion( $post["question"],
                                            $post["answerYes"],
                                            $post["answerNo"] );
            return $this->view->outputNoActionForm( array( "Thank you. Press OK to start again." ) );
        }
    }
    
    public function add( $post ){
        $errors = $this->buildErrorMessages( array( "parentAnswerId", 
                                                    "question", 
                                                    "answerYes",
                                                    "answerNo" ), $post );
        if( !empty( $errors ) ){
            return $this->view->outputErrorMessages( $errors ).
                   $this->view->outputAddActionForm( $post["parentAnswerId"],
                                                     $post["answerNo"] );
        }
        else{
            $this->model->addDistinguishingQuestion( $post["parentAnswerId"],
                                                     $post["question"],
                                                     $post["answerYes"] );
            return $this->view->outputNoActionForm( array( "Thank you. Press OK to start again." ) );
        }
    }
    
    public function answer( $post ){
        $errors = $this->buildErrorMessages( array( "questionId", "answer" ),
                                             $post );
        if( !empty( $errors ) ){
            return $this->view->outputErrorMessages( $errors ).
                   $this->view->outputNoActionForm( array( "Press OK to start again!" ) );
        }
        else{
            $answerKey = $post["questionId"].substr($post["answer"],0,1);
            $answer = $this->model->getAnswer( $answerKey );
            if( !empty($answer) ){
                if( is_numeric( $answer ) ){
                    $nextQuestionId = $answer;
                    $nextQuestion = $this->model->getQuestion( $nextQuestionId );
                    return $this->view->outputAnswerActionForm( $nextQuestionId, $nextQuestion );
                }
                else {
                    return $this->view->outputFinalActionForm( $answerKey, $answer );
                }
            }
            else {
                return $this->view->outputErrorMessages( array( "Fatal: question $answerKey is not found!" )  ).
                       $this->view->outputNoActionForm( array( "Bad error, the data is corrupted" ) );
            }
        }
    }
    
    public function answerFinal( $post ){
        $errors = $this->buildErrorMessages( array( "answerId", "finalAnswer", "answer" ),
                                             $post );
        if( !empty( $errors ) ){
            return $this->view->outputErrorMessages( $errors ).
                   $this->view->outputNoActionForm( array( "Press OK to start again!" ) );
        }
        else{
            if( $post["answer"] == "yes" ){
                return $this->view->outputNoActionForm( array( "I am so smart!", "Let's do it again!" ) );
            }
            else if( $post["answer"] == "no" ){
                return $this->view->outputAddActionForm( $post["answerId"], $post["finalAnswer"] );
            }
        }
    }

    private function buildErrorMessages( $expectedFields, $post ){
        $messages = array();
        foreach( $expectedFields as $field ){
            if( empty( $post[$field] ) ){
                $messages[] = "Error: field '$field' is not given";
            }
        }
        return $messages;
    }

}

class QuestionControllerInvalidModelException extends \Exception {}

class QuestionControllerInvalidViewException extends \Exception {}

?>
