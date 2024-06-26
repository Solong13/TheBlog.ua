<?php
$dbh = include_once ('../config/db_connection.php');
// Валідація полів. В іделі потрібно створити загальний клас для перевірки полів, без вказівки ключів масива
function verificationOfFields(array $fields) : array
{

    foreach ($fields as $key => $value) {
        $fields[$key] = trim(htmlspecialchars($value));

        // Перевiрка на пусті поля
        if (empty($fields[$key])) {
            die("Please fill in all the required fields!");

        }
    }
        if (isset($fields['name'])) {
            if (strlen($fields['name']) < 3 || strlen(isset($fields['name'])) > 20) {
                $fields['error'] = "Name must be between 3 and 20 characters long.";
            }
        }

        // Пароль не довший ніж 8 символів
        if (isset($fields['password'])) {
            if (!(strlen($fields['password']) <= 8)) {
                $fields['error'] = 'Your password must be at least 8 characters long.';
            }
        }

        // Валідність електронної пошти
        if (isset($fields['email'])) {
            if (!filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
                $fields['error'] = 'Please enter a valid email address.';
            }
        }


    return  $fields;
}


// Блок функцій для реєстрації

// Функція для перевірки чи існує користувач за допомогою запиту до бази даних
function checkUserExists($dbh, $result_of_validation) {
    // Логіка перевірки існування користувача
    // Запит до бази даних, перевірка результату тощо

    $query = "SELECT email FROM users WHERE email = :email";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':email', $result_of_validation['email']);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result;

}

// Функція для реєстрації нового користувача
function registerUser($dbh, $result_of_validation) {
    // Логіка реєстрації користувача
    // Вставка нового користувача в базу даних, перевірка унікальності тощо
    $chk_email = checkUserExists($dbh, $result_of_validation);

    if (empty($chk_email)) {
        $query = "INSERT INTO users (name, password, email) VALUES (:name, :password, :email)";
        $stmt = $dbh->prepare($query);
        $stmt->execute([':email' => $result_of_validation['email'], ':name' => $result_of_validation['name'], ':password' => $result_of_validation['password']]);
        echo 'User created!';
    } else {
        echo 'User already exists!';
    }

}