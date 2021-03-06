<?php

declare(strict_types=1);

namespace Zlikavac32\Enum\Tests;

use LogicException;
use PHPUnit\Framework\TestCase;
use Zlikavac32\Enum\Tests\Fixtures\AbstractEnumWithoutEnumerate;
use Zlikavac32\Enum\Tests\Fixtures\DuplicateNameEnum;
use Zlikavac32\Enum\Tests\Fixtures\EnumThatDependsOnEnum;
use Zlikavac32\Enum\Tests\Fixtures\EnumThatExtendsNonAbstractEnumWithoutEnumerate;
use Zlikavac32\Enum\Tests\Fixtures\EnumThatExtendsValidEnum;
use Zlikavac32\Enum\Tests\Fixtures\ValidEnumWithOneParent;
use Zlikavac32\Enum\Tests\Fixtures\EnumWithSomeVeryVeryLongNameA;
use Zlikavac32\Enum\Tests\Fixtures\EnumWithSomeVeryVeryLongNameB;
use Zlikavac32\Enum\Tests\Fixtures\InvalidAliasNameEnum;
use Zlikavac32\Enum\Tests\Fixtures\InvalidNumberAliasEnumerationObjectsEnum;
use Zlikavac32\Enum\Tests\Fixtures\InvalidObjectAliasEnumerationObjectsEnum;
use Zlikavac32\Enum\Tests\Fixtures\InvalidOverrideConstructorEnum;
use Zlikavac32\Enum\Tests\Fixtures\NameWithinEnumerateEnum;
use Zlikavac32\Enum\Tests\Fixtures\NoEnumerateMethodEnum;
use Zlikavac32\Enum\Tests\Fixtures\NonAbstractEnum;
use Zlikavac32\Enum\Tests\Fixtures\NonObjectEnumerationObjectsEnum;
use Zlikavac32\Enum\Tests\Fixtures\OrdinalWithinEnumerateEnum;
use Zlikavac32\Enum\Tests\Fixtures\ValidObjectsEnum;
use Zlikavac32\Enum\Tests\Fixtures\ValidStringEnum;
use Zlikavac32\Enum\Tests\Fixtures\WrongClassEnumerationObjectsEnum;
use Zlikavac32\Enum\Tests\Fixtures\ZeroLengthEnumerationObjectsEnum;
use function iterator_to_array;
use function json_encode;

class EnumTest extends TestCase
{
    public function testThatIdentityCheckWorks(): void
    {
        $this->assertTrue(ValidStringEnum::ENUM_A() === ValidStringEnum::ENUM_A());
        $this->assertTrue(ValidStringEnum::ENUM_B() === ValidStringEnum::ENUM_B());
        $this->assertTrue(ValidStringEnum::ENUM_A() !== ValidStringEnum::ENUM_B());
    }

    public function testThatEqualityCheckWorks(): void
    {
        $this->assertTrue(ValidStringEnum::ENUM_A() == ValidStringEnum::ENUM_A());
        $this->assertTrue(ValidStringEnum::ENUM_B() == ValidStringEnum::ENUM_B());
        $this->assertTrue(ValidStringEnum::ENUM_A() != ValidStringEnum::ENUM_B());
    }

    public function testThatAnyOfReturnsTrue(): void
    {
        $this->assertTrue(ValidStringEnum::ENUM_A()->isAnyOf(ValidStringEnum::ENUM_B(), ValidStringEnum::ENUM_A()));
    }

    public function testThatAnyOfReturnsFalse(): void
    {
        $this->assertFalse(ValidStringEnum::ENUM_A()->isAnyOf(ValidStringEnum::ENUM_B()));
    }

    public function testThatEnumObjectsHaveValidOrdinal(): void
    {
        $this->assertSame(
            0,
            ValidObjectsEnum::ENUM_A()
                ->ordinal()
        );
        $this->assertSame(
            1,
            ValidObjectsEnum::ENUM_B()
                ->ordinal()
        );
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage You can not retrieve ordinal within enumerate()
     */
    public function testThatOrdinalThrowExceptionUntilValueIsDefined(): void
    {
        OrdinalWithinEnumerateEnum::ENUM_A();
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage You can not retrieve name within enumerate()
     */
    public function testThatNameThrowExceptionUntilValueIsDefined(): void
    {
        NameWithinEnumerateEnum::ENUM_A();
    }

    public function testThatEnumObjectsHaveValidName(): void
    {
        $this->assertSame(
            'ENUM_A',
            ValidObjectsEnum::ENUM_A()
                ->name()
        );
        $this->assertSame(
            'ENUM_B',
            ValidObjectsEnum::ENUM_B()
                ->name()
        );
    }

    public function testThatEnumObjectHasValidJsonEncodeRepresentation(): void
    {
        $this->assertSame(
            '"ENUM_A"',
            json_encode(ValidObjectsEnum::ENUM_A())
        );
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Cloning enum element is not allowed
     */
    public function testThatCloneIsNotSupported(): void
    {
        clone ValidObjectsEnum::ENUM_A();
    }

    public function testThatValueOfReturnsRequestedEnum(): void
    {
        $this->assertSame(ValidObjectsEnum::ENUM_A(), ValidObjectsEnum::valueOf('ENUM_A'));
    }

    /**
     * @expectedException \Zlikavac32\Enum\EnumNotFoundException
     * @expectedExceptionMessage Enum element I_DONT_EXIST missing in Zlikavac32\Enum\Tests\Fixtures\ValidObjectsEnum
     */
    public function testThatValueOfThrowsExceptionWhenEnumDoesNotExist(): void
    {
        ValidObjectsEnum::valueOf('I_DONT_EXIST');
    }

    public function testThatContainsReturnsTrueForExistingEnum(): void
    {
        $this->assertTrue(ValidObjectsEnum::contains('ENUM_A'));
    }

    public function testThatContainsReturnsFalseForNonExistingEnum(): void
    {
        $this->assertFalse(ValidObjectsEnum::contains('I_DONT_EXIST'));
    }

    public function testThatEnumObjectsHaveValidDefaultToStringImplementation(): void
    {
        $this->assertSame('ENUM_A', (string) ValidObjectsEnum::ENUM_A());
        $this->assertSame('ENUM_B', (string) ValidObjectsEnum::ENUM_B());
    }

    public function testThatIteratorIteratesOverEnumObjects(): void
    {
        $this->assertSame(
            [
                ValidObjectsEnum::ENUM_A(),
                ValidObjectsEnum::ENUM_B(),
            ],
            iterator_to_array(ValidObjectsEnum::iterator())
        );
    }

    public function testThatValuesCanBeReturned(): void
    {
        $this->assertSame(
            [
                ValidStringEnum::ENUM_A(),
                ValidStringEnum::ENUM_B(),
            ],
            ValidStringEnum::values()
        );
    }

    public function testThatIteratorIteratesOverStringEnumObjects(): void
    {
        $this->assertSame(
            [
                ValidStringEnum::ENUM_A(),
                ValidStringEnum::ENUM_B(),
            ],
            iterator_to_array(ValidStringEnum::iterator())
        );
    }

    public function testThatDependentEnumCanBeCreated(): void
    {
        $this->assertSame(ValidStringEnum::ENUM_A(), EnumThatDependsOnEnum::ENUM_A()->enum());
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage No argument must be provided when calling
     *     Zlikavac32\Enum\Tests\Fixtures\ValidObjectsEnum::ENUM_B
     */
    public function testThatEnumObjectCallsMustBeWithoutArguments(): void
    {
        ValidObjectsEnum::ENUM_B(0);
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Enum Zlikavac32\Enum\Tests\Fixtures\ZeroLengthEnumerationObjectsEnum must define at
     *     least one element
     */
    public function testThatZeroLengthEnumerationObjectConfigurationThrowsException(): void
    {
        ZeroLengthEnumerationObjectsEnum::iterator();
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Enum Zlikavac32\Enum\Tests\Fixtures\NonAbstractEnum must be declared as abstract
     */
    public function testThatNonAbstractEnumThrowsException(): void
    {
        NonAbstractEnum::ENUM_A();
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Element name "INVA LID" does not match pattern /^[a-zA-Z_][a-zA-Z_0-9]*$/
     */
    public function testThatInvalidAliasNameThrowsException(): void
    {
        InvalidAliasNameEnum::values();
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage You must provide protected static function enumerate(): array method in
     *     your enum class Zlikavac32\Enum\Tests\Fixtures\DefaultCreateEnumerationObjects
     */
    public function testThatDefaultEnumerationObjectConfigurationThrowsException(): void
    {
        NoEnumerateMethodEnum::iterator();
    }

    /**
     * @expectedException \Zlikavac32\Enum\EnumNotFoundException
     * @expectedExceptionMessage Enum element I_DONT_EXIST missing in Zlikavac32\Enum\Tests\Fixtures\ValidObjectsEnum
     */
    public function testThatAccessingNonExistingEnumThrowsException(): void
    {
        ValidObjectsEnum::I_DONT_EXIST();
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Element name 0 in enum
     *     Zlikavac32\Enum\Tests\Fixtures\InvalidObjectAliasEnumerationObjectsEnum is not valid
     */
    public function testThatInvalidObjectAliasThrowsException(): void
    {
        InvalidObjectAliasEnumerationObjectsEnum::iterator();
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Element name (object instance of
     *     Zlikavac32\Enum\Tests\Fixtures\InvalidNumberAliasEnumerationObjectsDummyEnum) in enum
     *     Zlikavac32\Enum\Tests\Fixtures\InvalidNumberAliasEnumerationObjectsEnum is not valid
     */
    public function testThatInvalidNumberAliasThrowsException(): void
    {
        InvalidNumberAliasEnumerationObjectsEnum::iterator();
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Enum element object in enum
     *     Zlikavac32\Enum\Tests\Fixtures\WrongClassEnumerationObjectsEnum must be an instance of
     *     Zlikavac32\Enum\Tests\Fixtures\WrongClassEnumerationObjectsEnum (an instance of
     *     Zlikavac32\Enum\Tests\Fixtures\AWrongClassEnumerationObjectsDummyEnum received)
     */
    public function testThatWrongEnumInstanceThrowsException(): void
    {
        WrongClassEnumerationObjectsEnum::iterator();
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Enum element object in enum
     *     Zlikavac32\Enum\Tests\Fixtures\NonObjectEnumerationObjectsEnum must be an instance of
     *     Zlikavac32\Enum\Tests\Fixtures\NonObjectEnumerationObjectsEnum (integer received)
     */
    public function testThatObjectEnumThrowsException(): void
    {
        NonObjectEnumerationObjectsEnum::iterator();
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Serialization/deserialization of enum element is not allowed
     */
    public function testThatSetStateThrowsException(): void
    {
        ValidStringEnum::__set_state();
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Serialization/deserialization of enum element is not allowed
     */
    public function testThatSleepThrowsException(): void
    {
        ValidStringEnum::ENUM_A()->__sleep();
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Serialization/deserialization of enum element is not allowed
     */
    public function testThatWakeupThrowsException(): void
    {
        ValidStringEnum::ENUM_A()->__wakeup();
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Serialization/deserialization of enum element is not allowed
     */
    public function testThatSerializeThrowsException(): void
    {
        ValidStringEnum::ENUM_A()->serialize();
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Serialization/deserialization of enum element is not allowed
     */
    public function testThatUnserializeThrowsException(): void
    {
        ValidStringEnum::ENUM_A()->unserialize('');
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage It seems that enum is not correctly initialized. Did you forget to call
     *     parent::__construct() in enum Zlikavac32\Enum\Tests\Fixtures\InvalidOverrideConstructorEnum?
     */
    public function testThatConstructMustBeCalledForName(): void
    {
        (new InvalidOverrideConstructorEnum())->name();
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage It seems that enum is not correctly initialized. Did you forget to call
     *     parent::__construct() in enum Zlikavac32\Enum\Tests\Fixtures\InvalidOverrideConstructorEnum?
     */
    public function testThatConstructMustBeCalledForOrdinal(): void
    {
        (new InvalidOverrideConstructorEnum())->ordinal();
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage It seems that enum is not correctly initialized. Did you forget to call
     *     parent::__construct() in enum Zlikavac32\Enum\Tests\Fixtures\InvalidOverrideConstructorEnum?
     */
    public function testThatConstructMustBeCalledForIsAnyOf(): void
    {
        (new InvalidOverrideConstructorEnum())->isAnyOf();
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage It seems you tried to manually create enum outside of enumerate() method for enum
     *     Zlikavac32\Enum\Tests\Fixtures\NonAbstractEnum
     */
    public function testThatNameThrowsExceptionWhenNotConstructedCorrectly(): void
    {
        (new NonAbstractEnum())->name();
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage It seems you tried to manually create enum outside of enumerate() method for enum
     *     Zlikavac32\Enum\Tests\Fixtures\NonAbstractEnum
     */
    public function testThatOrdinalThrowsExceptionWhenNotConstructedCorrectly(): void
    {
        (new NonAbstractEnum())->ordinal();
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage It seems you tried to manually create enum outside of enumerate() method for enum
     *     Zlikavac32\Enum\Tests\Fixtures\NonAbstractEnum
     */
    public function testThatToStringThrowsExceptionWhenNotConstructedCorrectly(): void
    {
        (new NonAbstractEnum())->__toString();
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Duplicate element ENUM_A exists in enum Zlikavac32\Enum\Tests\Fixtures\DuplicateNameEnum
     */
    public function testThatDuplicateElementThrowsException(): void
    {
        DuplicateNameEnum::ENUM_A();
    }

    public function testThatWorkaroundForPHPEvalBugWorks(): void
    {
        try {
            EnumWithSomeVeryVeryLongNameA::ENUM_A();
            $this->assertInstanceOf(
                EnumWithSomeVeryVeryLongNameB::class,
                EnumWithSomeVeryVeryLongNameB::ENUM_A()
            );
        } catch (LogicException $e) {
            $this->fail('Workaround no longer works');
        }
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Enum Zlikavac32\Enum\Tests\Fixtures\EnumThatExtendsValidEnum extends
     *                           Zlikavac32\Enum\Tests\Fixtures\ValidStringEnum which already defines enumerate()
     *                           method
     */
    public function testThatNonDefiningEnumClassInChainMustNotDefineEnumerate(): void
    {
        EnumThatExtendsValidEnum::ENUM_A();
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Class Zlikavac32\Enum\Tests\Fixtures\NonAbstractEnumWithoutEnumerate must be also
     *                           abstract (since
     *                           Zlikavac32\Enum\Tests\Fixtures\EnumThatExtendsNonAbstractEnumWithoutEnumerate extends
     *                           it)
     */
    public function testThatNonDefiningEnumClassInChainMustBeAbstract(): void
    {
        EnumThatExtendsNonAbstractEnumWithoutEnumerate::ENUM_A();
    }

    public function testThatEnumWithAbstractParentCanBeConstructed(): void
    {
        $this->assertTrue(ValidEnumWithOneParent::ENUM_A() instanceof AbstractEnumWithoutEnumerate);
        $this->assertTrue(ValidEnumWithOneParent::ENUM_A() instanceof ValidEnumWithOneParent);
    }
}
