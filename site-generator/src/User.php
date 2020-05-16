<?php

class User
{
    private $firstName;

    private $lastName;

    private $courseYear;

    private $courseName;

    private $facultyNumber;

    private $groupNumber;

    private $birthday;

    private $zodiac;

    private $link;

    private $photo;

    private $motivation;

    private $signature;

    public function __construct(
        $firstName,
        $lastName,
        $courseYear,
        $courseName,
        $facultyNumber,
        $groupNumber,
        $birthday,
        $zodiac,
        $link,
        $photo,
        $motivation,
        $signature
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->courseYear = $courseYear;
        $this->courseName = $courseName;
        $this->facultyNumber = $facultyNumber;
        $this->groupNumber = $groupNumber;
        $this->birthday = $birthday;
        $this->zodiac = $zodiac;
        $this->link = $link;
        $this->photo = $photo;
        $this->motivation = $motivation;
        $this->signature = $signature;
    }

    public function validate(): void
    {
        if (preg_match("/\p{L}+/", $this->firstName) != 1) {
            throw new Exception("Името съдържа само главни/малки букви");
        }
        if (preg_match("/\p{L}+/", $this->lastName) != 1) {
            throw new Exception("Фамилията съдържа само главни/малки букви");
        }
        if (!is_numeric($this->courseYear)) {
            throw new Exception("Година на курс е задължително поле");
        }
        if (!($this->courseYear >= 1800 && $this->courseYear <= 3999)) {
            throw new Exception("Година на курс трябва да е валидна година");
        }
        if (!$this->courseName) {
            throw new Exception("Име на курс е задължително поле");
        }
        if (!$this->facultyNumber) {
            throw new Exception("Факултетен номер е задължително поле");
        }
        if (!$this->groupNumber || !is_numeric($this->groupNumber)) {
            throw new Exception("Номер на група е задължително поле");
        }
        if (!$this->validateDate($this->birthday)) {
            throw new Exception("Дата на раждане трябва да е валидна дата");
        }
        if (!$this->zodiac) {
            throw new Exception("Зодиакалният знак е задължително поле");
        }
        if ($this->link && !filter_var($this->link, FILTER_VALIDATE_URL)) {
            throw new Exception("Линкът трябва да е валиден URL");
        }
        if (!$this->photo) {
            throw new Exception("Снимката е задължително поле");
        }
        if (!$this->signature) {
            throw new Exception("Подписът е задължително поле");
        }
    }

    function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public function storeInDb(): void
    {
        require_once "../src/Db.php";

        $db = new Db();

        $conn = $db->getConnection();

        $insertStatement = $conn->prepare(
            "INSERT INTO users (firstName, lastName, courseYear, courseName, facultyNumber, groupNumber, birthday, zodiac, link, photo, motivation, signature)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $insertResult = $insertStatement->execute([
            $this->firstName,
            $this->lastName,
            $this->courseYear,
            $this->courseName,
            $this->facultyNumber,
            $this->groupNumber,
            $this->birthday,
            $this->zodiac,
            $this->link,
            $this->photo,
            $this->motivation,
            $this->signature,
        ]);

        if (!$insertResult) {
            throw new Exception("Request failed, try again later");
        }
    }
}
