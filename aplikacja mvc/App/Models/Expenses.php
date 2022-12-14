<?php

namespace App\Models;

use PDO;
use \App\Token;

/**
 * User model
 *
 * PHP version 7.0
 */
class Expenses extends \Core\Model
{
    /**
     * Error messages
     *
     * @var array
     */
    public $errors = [];

    /**
     * Class constructor
     *
     * @param array $data  Initial property values (optional)
     *
     * @return void
     */
    public function __construct($data = []){
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }
    public function addExpense(){
        $this->validateExpense();

        if (empty($this->errors)) {

            $idExpensesCategory = $this->getIdOfExpensesCategory();
            $idPaymentMethods = $this->getIdOfPaymentMethods();

            $sql = 'INSERT INTO expenses (user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment) VALUES (:user_id, :idExpensesCategory, :idPaymentMethods, :amount, :date, :comment)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_STR);
            $stmt->bindValue(':idExpensesCategory', $idExpensesCategory, PDO::PARAM_STR);
            $stmt->bindValue(':idPaymentMethods', $idPaymentMethods, PDO::PARAM_STR);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':date', $this->date, PDO::PARAM_STR);
            $stmt->bindValue(':comment', $this->comment, PDO::PARAM_STR);
            $stmt->execute();
            
            return true;
        }
        return false;
    }

    public function getIdOfExpensesCategory(){
        $sql = 'SELECT id FROM expenses_category_assigned_to_users WHERE user_id = :user_id AND name LIKE :expense';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_STR);
        $stmt->bindValue(':expense', $this->expense, PDO::PARAM_STR);
        $stmt->execute();

        $expensesId = $stmt->fetch();

        return $expensesId['id'];
    }

    public function getIdOfPaymentMethods(){
        $sql = 'SELECT id FROM payment_methods_assigned_to_users WHERE user_id = :user_id AND name LIKE :pay';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_STR);
        $stmt->bindValue(':pay', $this->pay, PDO::PARAM_STR);
        $stmt->execute();

        $paysId = $stmt->fetch();

        return $paysId['id'];
    }

    public function validateExpense(){
        // Amount
        if ((!isset($this->amount)) || ($this->amount <= 0)) {
            $this->errors[]  = "Wprowadzona kwota wydatku jest nieprawid??owa.";
        }

        // Comment
        $this->comment = htmlentities($this->comment,ENT_QUOTES,"UTF-8");

        // Date
        if(!isset($this->date)){
            $this->errors[] = "Nie podano daty wydatku.";
        }
        
        // Pay
        if(!isset($this->pay)){
            $this->errors[] = "Nie wybrano metody p??atno??ci.";
        }

        // Expense
        if(!isset($this->expense)){
            $this->errors[] = "Nie wybrano kategorii wydatku.";
        }
    }
}

