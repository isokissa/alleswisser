<?php

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
        
    public function answeraa( $post ){
        $result = array( "questionId" => 1 );
        if( !empty($post["questionId"]) && !empty($post["answer"]) ){
            $answerKey = $post["questionId"].substr($post["answer"],0,1);
            $answer = $this->model->getAnswer( $answerKey );
            if( !empty($answer) ){
                if( is_numeric( $answer ) ){
                    $result["questionId"] = $answer;
                }
                else {
                    $result["questionId"] = $answerKey;
                    $result["finalAnswer"] = $answer;
                }
            }
        }
        return $result;
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
            $this->model->addAnswer( $post["parentAnswerId"],
                                     $post["question"],
                                     $post["answerYes"] );
            return $this->view->outputNoActionForm( array( "Thank you. Press OK to start again." ) );
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

class QuestionControllerInvalidModelException extends Exception {}

class QuestionControllerInvalidViewException extends Exception {}

?>
