<?php


class BeforeTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var \Rakit\Validation\Rules\Before
     */
    protected $validator;

    public function setUp()
    {
        $this->validator = new \Rakit\Validation\Rules\Before();
    }

    /**
     * @dataProvider getValidDates
     */
    public function testOnlyAWellFormedDateCanBeValidated($date)
    {
        $this->assertTrue(
            $this->validator->check($date, ["next week"])
        );
    }

    public function getValidDates()
    {
        $now = new DateTime();

        return [
            [2016],
            [$now->format("Y-m-d")],
            [$now->format("Y-m-d h:i:s")],
            ["now"],
            ["tomorrow"],
            ["2 years ago"]
        ];
    }

    /**
     * @dataProvider getInvalidDates
     * @expectedException Exception
     */
    public function testANonWellFormedDateCannotBeValidated($date)
    {
        $this->validator->check($date, ["tomorrow"]);
    }

    public function getInvalidDates()
    {
        $now = new DateTime();

        return [
            [12], //12 instead of 2012
            ["09"], //like '09 instead of 2009
            [$now->format("Y m d")],
            [$now->format("Y m d h:i:s")],
            ["tommorow"], //typo
            ["lasst year"] //typo
        ];
    }

    public function testProvidedDateFailsValidation()
    {

        $now = (new DateTime("today"))->format("Y-m-d");
        $today = "today";

        $this->assertFalse(
            $this->validator->check($now, ['yesterday'])
        );

        $this->assertFalse(
            $this->validator->check($today, ['yesterday'])
        );
    }

    /**
     * @expectedException Exception
     */
    public function testUserProvidedParamCannotBeValidatedBecauseItIsInvalid()
    {
        $this->validator->check("now", ["to,morrow"]);
    }
}
