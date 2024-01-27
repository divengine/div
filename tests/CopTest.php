<?php

use PHPUnit\Framework\TestCase;
use divengine\div;

// Classes for testing

class Geo
{
    public float $lat;
    public float $lng;
}

class Address
{
    public string $street;
    public string $suite;
    public string $city;
    public string $zipcode;

    public Geo $geo;
}

class User
{
    public int $id;
    public string $name;
    public string $username;

    /** @var array<Address> */
    public array $addresses;
}

// The test
class CopTest extends TestCase
{
    public function testScalarValues()
    {
        $source = 10;
        $target = 5;

        $source = div::cop($source, $target);

        $this->assertEquals($source, $target);
    }

    public function testObjectValues()
    {
        $source = new stdClass();
        $source->property = 'value';

        $target = new stdClass();
        $target->property = 'otherValue';

        div::cop($target, $source);

        $this->assertEquals($source->property, $target->property);
    }

    public function testArrayValues()
    {
        $source = [
            'property' => 'value'
        ];

        $target = [
            'property' => 'otherValue'
        ];

        div::cop($target, $source);

        $this->assertEquals($source['property'], $target['property']);
    }

    public function testArrayValuesWithDifferentKeys()
    {
        $source = [
            'property' => 'value'
        ];

        $target = [
            'otherProperty' => 'otherValue'
        ];

        div::cop($target, $source);

        $this->assertEquals($source['property'], $target['property']);
    }

    public function testComplexObject()
    {
        $rawJson = '{
            "id": 1,
            "name": "John Doe",
            "username": "johndoe",
            "addresses": [
                {
                    "street": "Kulas Light",
                    "suite": "Apt. 556",
                    "city": "Gwenborough",
                    "zipcode": "92998-3874",
                    "geo": {
                        "lat": -37.3159,
                        "lng": 81.1496
                    }
                },
                {
                    "street": "Sunset Boulevard",
                    "suite": "Apt. 123",
                    "city": "Los Angeles",
                    "zipcode": "90210",
                    "geo": {
                        "lat": 34.0522,
                        "lng": -118.2437
                    }
                }
            ]
        }';

        $u = new User;
        $jsonObject = json_decode($rawJson);
        div::cop($u, $jsonObject, strict: false);

        $this->assertEquals($u->id, $jsonObject->id);
        $this->assertEquals($u->name, $jsonObject->name);
        $this->assertEquals($u->username, $jsonObject->username);
        $this->assertEquals($u->addresses[0]->street, $jsonObject->addresses[0]->street);
        $this->assertEquals($u->addresses[0]->suite, $jsonObject->addresses[0]->suite);
        $this->assertEquals($u->addresses[0]->city, $jsonObject->addresses[0]->city);
        $this->assertEquals($u->addresses[0]->zipcode, $jsonObject->addresses[0]->zipcode);
        $this->assertEquals($u->addresses[0]->geo->lat, $jsonObject->addresses[0]->geo->lat);
        $this->assertEquals($u->addresses[0]->geo->lng, $jsonObject->addresses[0]->geo->lng);
        $this->assertEquals($u->addresses[1]->street, $jsonObject->addresses[1]->street);
        $this->assertEquals($u->addresses[1]->suite, $jsonObject->addresses[1]->suite);
        $this->assertEquals($u->addresses[1]->city, $jsonObject->addresses[1]->city);
        $this->assertEquals($u->addresses[1]->zipcode, $jsonObject->addresses[1]->zipcode);
        $this->assertEquals($u->addresses[1]->geo->lat, $jsonObject->addresses[1]->geo->lat);
        $this->assertEquals($u->addresses[1]->geo->lng, $jsonObject->addresses[1]->geo->lng);

        // check data types
        $this->assertIsInt($u->id);
        $this->assertIsString($u->name);
        $this->assertIsString($u->username);
        $this->assertIsArray($u->addresses);

        // addresses is an array of Address objects
        $this->assertInstanceOf(Address::class, $u->addresses[0]);
        $this->assertInstanceOf(Address::class, $u->addresses[1]);

        // geo is an object of Geo
        $this->assertInstanceOf(Geo::class, $u->addresses[0]->geo);
        $this->assertInstanceOf(Geo::class, $u->addresses[1]->geo);
    }

    public function testStrictModeWithObject()
    {
        $source = new stdClass();
        $source->property = 'value';

        $target = new stdClass();
        $target->otherProperty = 'otherValue';

        div::cop($target, $source, strict: true);

        $this->assertObjectNotHasProperty('property', $target);
    }

    public function testStrictModeWithArray()
    {
        $source = [
            'property' => 'value'
        ];

        $target = [
            'otherProperty' => 'otherValue'
        ];

        div::cop($target, $source, strict: true);

        $this->assertArrayNotHasKey('property', $target);
    }

    public function testStrictModeWithArrayAndObject()
    {
        $source = [
            'property' => 'value'
        ];

        $target = new stdClass();
        $target->otherProperty = 'otherValue';

        div::cop($target, $source, strict: true);

        $this->assertObjectNotHasProperty('property', $target);
    }
}
