Alleswisser
===========

Web application will ask questions, trying to guess the thing that the 
user has imagined. If it finds the right answer, it will celebrate, 
otherwise it will learn and be more knowledgeable next time. 


How it works
------------

Every questions has two answers: yes or no. 

Answer can be either a concrete, final answer, or a pointer to the next 
question
    
Regardless of is it yes or no, if there is a pointer to next question, 
jump to that next question. If there is a final answer, ask whether it 
is correct. If yes - celebrate the victory. If not, ask "how to distinguish?", 
and put the pointer to the answer to the appropriate placeholder. 


"questions" datamap: 
---------------------

    <questionId> -> <questionText>

"answers" datamap: 
-------------------

    <questionId>[y|n] -> <answer>|<nextQuestionId>

Example: 
--------

    1 -> Is it a living thing?
    2 -> Is it a man-made thing? 

    1y -> cat
    1n -> 2
    2y -> car
    2n -> stone

Controller actions: 
-------------------

Next action is shown in **bold**. 

### no-action action

This action takes place when no "action" is given in the request. It
starts new session, which means ask the **answer** for the first 
question. If there is no first qustion, do the **init** action and 
ask the user to add first question 

### "init" action

Adds the first question. If "questions" datamap is not empty, do the 
initial no-action form with error message. This action has following 
parameters: 

* *question* -> text of the question
* *answerYes* -> final answer in case of "yes"
* *answerNo* -> final answer in case of "no"

If any of parameters is not given, next action will be again **init**, 
with appropriate error message. If all parameters are given, next
action will be "thank you" message and no-action form, in order to 
start new session. 


### "add" action

Adds a terminal question under given parentAnswerId. A "terminal question" 
is a question that has final answers for both "yes" and "no"
branches. Action parameters: 

* *parentAnswerId* -> This parameter is a pointer into answers datamap. 
  New question will be added as child of this parameter and the answer given 
  with this parameter will be updated to point to point to new question.
  The original answer will be moved to "no" branch of the new question. 
  "yes" branch of the new question will be the value from *answerYes* 
  parameter. 
* *question* -> text of the question
* *answerYes* -> final answer in case of "yes"
* *answerNo* (read-only) -> final answer in case of "no". Used only to help 
  the view render the form again without datamap lookup, in case of error. 

### "answer" action

**answer** action shows the question and expects "yes" or "no" answer from 
the user. Parameters:

* *questionId* -> id of the question asked
* *answer* -> contains the value of the answer: "yes" or "no"

As a reaction, the controller will lookup the answers datamap for given
*questionId* and *answer* combination and depending on the found value
do one of following: 

* No answer is found: next action will be no-action with error message. 
  This is system error which means data corruption. 
* The answer is a numeric: this means answer id of the next question. 
  Next action will be **answer** for the next question
* The answer is not numeric: it is considered to be the final answer. 
  Next action will be to ask the final answer, and the action is **answerFinal**.


### "answerFinal" action

Asks the final question. Parameters: 

* *answerId* -> id of the question asked
* *finalAnswer* -> the text of the final answer. 
* *answer* -> contains the value of the answer: "yes" or "no"

If answer is "yes", write a triumphal message and no-action. If answer is
"no", generate the **add** form to ask for right answer and the distinguishing
question. 



