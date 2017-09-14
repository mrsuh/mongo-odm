# mongo odm

[![Latest Stable Version](https://poser.pugx.org/mrsuh/mongo-odm/v/stable)](https://packagist.org/packages/mrsuh/mongo-odm)
[![Total Downloads](https://poser.pugx.org/mrsuh/mongo-odm/downloads)](https://packagist.org/packages/mrsuh/mongo-odm)
[![License](https://poser.pugx.org/mrsuh/mongo-odm/license)](https://packagist.org/packages/mrsuh/mongo-odm)

Simple Mongo ODM library.


## Installation ##

Add package to your require section in the composer.json file.

```bash
composer require mrsuh/mongo-odm:2.*
```

## Usage ##

```php
<?php

require 'vendor/autoload.php';

use ODM\DBAL;
use ODM\Document\Document;
use ODM\DocumentManager\DocumentManagerFactory;

/**
 * @ODM\Collection(name="alphabet")
 */
class Alphabet extends Document {

    /**
     * @ODM\Field(name="language", type="string")
     */
    private $language;

    /**
     * @ODM\Field(name="words", type="Word[]")
     */
    private $words;

    /**
     * Alphabet constructor.
     */
    public function __construct()
    {
        $this->words = [];
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     * @return $this
     */
    public function setLanguage(string $language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return Word[]
     */
    public function getWords()
    {
        return $this->words;
    }

    /**
     * @param Word $word
     * @return $this
     */
    public function addWord(Word $word)
    {
        $this->words[] = $word;

        return $this;
    }
}

class Word {

    /**
     * @ODM\Field(name="name", type="string")
     */
    private $name;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }
}

$dbal = new DBAL('127.0.0.1', 27017, 'test');
$dm_alphabet = (new DocumentManagerFactory($dbal))->init(Alphabet::class);

$alphabet = new Alphabet();
$alphabet->setLanguage('English');
foreach(['a', 'b', 'c'] as $word_name) {
    $word = new Word();
    $word->setName($word_name);

    $alphabet->addWord($word);
}

$dm_alphabet->insert($alphabet);

$alphabet_from_db = $dm_alphabet->findOne(['_id' => $alphabet->getId()]);

echo $alphabet_from_db->getLanguage() . ' alphabet words: ' . PHP_EOL;
foreach($alphabet_from_db->getWords() as $word) {
    echo 'word ' . $word->getName() . PHP_EOL;
}

```

## Types

+ bool
+ integer
+ string
+ float
+ array
+ integer[]
+ string[]
+ float[]
+ \Obj
+ \Obj[]
