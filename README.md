# mongo odm

[![Latest Stable Version](https://poser.pugx.org/mrsuh/mongo-odm/v/stable)](https://packagist.org/packages/mrsuh/mongo-odm)
[![Total Downloads](https://poser.pugx.org/mrsuh/mongo-odm/downloads)](https://packagist.org/packages/mrsuh/mongo-odm)
[![License](https://poser.pugx.org/mrsuh/mongo-odm/license)](https://packagist.org/packages/mrsuh/mongo-odm)

Simple Mongo ODM library.


## Installation ##

Add package to your require section in the composer.json file.

```bash
composer require mrsuh/mongo-odm:1.*
```

## Usage ##

```php
<?php

require 'vendor/autoload.php';

/* Create Document Class */
class Note extends ODM\Document\Document
{
    private $field;

    public function getField()
    {
        return $this->field;
    }

    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }
}

/* Initialize connection */
$dbal = new ODM\DBAL('127.0.0.1', '27017', 'test2');
$factory = new ODM\DocumentMapper\DataMapperFactory($dbal);

/* Initialize DataMapper for your Document */
$mapper = $factory->init(Note::class);

/* Use your data mapper */
$note = new Note();

$note->setField('My text');

$mapper->insert($note);

$note_find = $mapper->findOne(['id' => $note->getId()]);

var_dump($note_find->getField());
/* string(7) "My text" */

$note->setField([1,2,3,4,5]);

$mapper->update($note);

$note_find = $mapper->findOne(['id' => $note->getId()]);

var_dump($note_find->getField());
/*
array(5) {
    [0]=>
  int(1)
  [1]=>
  int(2)
  [2]=>
  int(3)
  [3]=>
  int(4)
  [4]=>
  int(5)
}
*/

$mapper->find();

$mapper->delete($note);

$mapper->drop();

```